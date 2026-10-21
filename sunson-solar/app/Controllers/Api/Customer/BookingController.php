<?php

namespace App\Controllers\Api\Customer;

use App\Controllers\BaseApiController;
use App\Models\AvailabilityModel;
use App\Models\BookingModel;
use App\Models\UserModel;

/** Customer: view and manage their own scheduled services. */
class BookingController extends BaseApiController
{
    private const CANCELLABLE = ['pending', 'approved', 'assigned'];

    private function baseQuery(): BookingModel
    {
        return (new BookingModel())
            ->select('bookings.*, services.name AS service_name, services.price AS service_price')
            ->join('services', 'services.id = bookings.service_id');
    }

    /** GET /api/customer/bookings?status=pending */
    public function index()
    {
        $m = $this->baseQuery()->where('bookings.customer_id', $this->me()['id']);
        if ($s = $this->request->getGet('status')) {
            $m->where('bookings.status', $s);
        }

        return $this->ok($m->orderBy('bookings.scheduled_at', 'DESC')->findAll());
    }

    /** GET /api/customer/bookings/(:num) */
    public function show(int $id)
    {
        $b = $this->baseQuery()->where('bookings.customer_id', $this->me()['id'])->where('bookings.id', $id)->first();

        return $b ? $this->ok($b) : $this->notFound('Booking');
    }

    /** POST /api/customer/bookings — { service_id, scheduled_at "Y-m-d H:i:s", site_address, notes } */
    public function create()
    {
        $data = $this->input();
        $rules = [
            'service_id'   => 'required|is_natural_no_zero|is_not_unique[services.id,is_active,1]',
            'scheduled_at' => 'required|valid_date[Y-m-d H:i:s]',
            'site_address' => 'permit_empty',
            'notes'        => 'permit_empty|max_length[2000]',
        ];
        if ($res = $this->check($data, $rules)) {
            return $res;
        }
        if (strtotime($data['scheduled_at']) <= time()) {
            return $this->fail422(['scheduled_at' => 'Schedule must be in the future.']);
        }
        if ($err = $this->availabilityError($data['scheduled_at'])) {
            return $this->fail422(['scheduled_at' => $err]);
        }

        $m  = new BookingModel();
        $id = $m->insert([
            'customer_id'  => $this->me()['id'],
            'service_id'   => $data['service_id'],
            'scheduled_at' => $data['scheduled_at'],
            'site_address' => $data['site_address'] ?? $this->me()['address'],
            'notes'        => $data['notes'] ?? null,
            'status'       => 'pending',
        ]);
        $this->notifyStaff($id, 'New booking request', 'A customer requested a service on ' . $data['scheduled_at'] . '.');

        return $this->ok($this->baseQuery()->find($id), 'Booking submitted. Please wait for approval.', 201);
    }

    /** PUT /api/customer/bookings/(:num)/reschedule — { scheduled_at } */
    public function reschedule(int $id)
    {
        $b = (new BookingModel())->where('customer_id', $this->me()['id'])->find($id);
        if (! $b) {
            return $this->notFound('Booking');
        }
        if (! in_array($b['status'], self::CANCELLABLE, true)) {
            return $this->fail422(['status' => 'This booking can no longer be changed.']);
        }

        $data = $this->input();
        if ($res = $this->check($data, ['scheduled_at' => 'required|valid_date[Y-m-d H:i:s]'])) {
            return $res;
        }
        if (strtotime($data['scheduled_at']) <= time()) {
            return $this->fail422(['scheduled_at' => 'Schedule must be in the future.']);
        }
        if ($err = $this->availabilityError($data['scheduled_at'], $id)) {
            return $this->fail422(['scheduled_at' => $err]);
        }

        // A changed schedule needs approval again and frees the technician.
        (new BookingModel())->update($id, [
            'scheduled_at'  => $data['scheduled_at'],
            'status'        => 'pending',
            'technician_id' => null,
        ]);
        $this->notifyStaff($id, 'Booking rescheduled', "Booking #$id was moved to {$data['scheduled_at']} by the customer.");

        return $this->ok($this->baseQuery()->find($id), 'Booking rescheduled. Waiting for approval.');
    }

    /** PUT /api/customer/bookings/(:num)/cancel */
    public function cancel(int $id)
    {
        $b = (new BookingModel())->where('customer_id', $this->me()['id'])->find($id);
        if (! $b) {
            return $this->notFound('Booking');
        }
        if (! in_array($b['status'], self::CANCELLABLE, true)) {
            return $this->fail422(['status' => 'This booking can no longer be cancelled.']);
        }

        (new BookingModel())->update($id, ['status' => 'cancelled']);
        $this->notifyStaff($id, 'Booking cancelled', "Booking #$id was cancelled by the customer.");

        return $this->ok(null, 'Booking cancelled.');
    }

    /**
     * Admin-configured availability. If nothing is configured for that weekday
     * the day is treated as open; otherwise the time must fall inside an active
     * slot that is not full yet.
     */
    private function availabilityError(string $when, ?int $ignoreBookingId = null): ?string
    {
        $ts   = strtotime($when);
        $dow  = (int) date('w', $ts);
        $time = date('H:i:s', $ts);

        $slots = (new AvailabilityModel())->where('day_of_week', $dow)->where('is_active', 1)->findAll();
        if (! $slots) {
            $any = (new AvailabilityModel())->where('day_of_week', $dow)->countAllResults();

            return $any ? 'We are not available on that day.' : null;
        }

        foreach ($slots as $s) {
            if ($time >= $s['start_time'] && $time < $s['end_time']) {
                $q = (new BookingModel())
                    ->where('scheduled_at >=', date('Y-m-d', $ts) . ' ' . $s['start_time'])
                    ->where('scheduled_at <', date('Y-m-d', $ts) . ' ' . $s['end_time'])
                    ->whereIn('status', ['pending', 'approved', 'assigned', 'in_progress']);
                if ($ignoreBookingId) {
                    $q->where('id !=', $ignoreBookingId);
                }

                return $q->countAllResults() >= (int) $s['max_bookings'] ? 'That time slot is already full.' : null;
            }
        }

        return 'That time is outside our available hours.';
    }

    /** Alert every admin and dispatcher. */
    private function notifyStaff(int $bookingId, string $title, string $message): void
    {
        $staff = (new UserModel())->whereIn('role', ['admin', 'dispatcher'])->where('is_active', 1)->findAll();
        foreach ($staff as $u) {
            $this->notify((int) $u['id'], 'schedule', $title, $message);
        }
    }
}
