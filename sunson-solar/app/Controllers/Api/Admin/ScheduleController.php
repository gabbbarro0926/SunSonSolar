<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\BaseApiController;
use App\Models\BookingModel;
use App\Models\PaymentModel;
use App\Models\UserModel;

/** Calendar/schedule overview for admins and dispatchers. */
class ScheduleController extends BaseApiController
{
    private function query(): BookingModel
    {
        return (new BookingModel())
            ->select("bookings.*, services.name AS service_name,
                      c.first_name AS customer_first_name, c.last_name AS customer_last_name, c.phone AS customer_phone,
                      t.first_name AS technician_first_name, t.last_name AS technician_last_name")
            ->join('services', 'services.id = bookings.service_id')
            ->join('users c', 'c.id = bookings.customer_id')
            ->join('users t', 't.id = bookings.technician_id', 'left');
    }

    /** GET /api/admin/bookings?from=Y-m-d&to=Y-m-d&status=&technician_id= (calendar view data) */
    public function index()
    {
        $q = $this->query();
        if ($f = $this->request->getGet('from')) {
            $q->where('bookings.scheduled_at >=', $f . ' 00:00:00');
        }
        if ($t = $this->request->getGet('to')) {
            $q->where('bookings.scheduled_at <=', $t . ' 23:59:59');
        }
        if ($s = $this->request->getGet('status')) {
            $q->where('bookings.status', $s);
        }
        if ($tid = $this->request->getGet('technician_id')) {
            $q->where('bookings.technician_id', $tid);
        }

        $rows = $q->orderBy('bookings.scheduled_at')->findAll();

        // Group by day so the front end can drop it straight onto a calendar.
        $byDay = [];
        foreach ($rows as $r) {
            $byDay[substr($r['scheduled_at'], 0, 10)][] = $r;
        }

        return $this->ok(['items' => $rows, 'by_day' => $byDay]);
    }

    /** GET /api/admin/bookings/(:num) */
    public function show(int $id)
    {
        $b = $this->query()->find($id);

        return $b ? $this->ok($b) : $this->notFound('Booking');
    }

    /** GET /api/admin/technicians — active technicians for the "assign" dropdown. */
    public function technicians()
    {
        return $this->ok((new UserModel())
            ->select('id, first_name, last_name, phone')
            ->where('role', 'technician')->where('is_active', 1)->orderBy('first_name')->findAll());
    }

    /** PUT /api/admin/bookings/(:num)/approve */
    public function approve(int $id)
    {
        $b = $this->find($id, ['pending']);
        if (! is_array($b)) {
            return $b;
        }
        (new BookingModel())->update($id, ['status' => 'approved', 'decline_reason' => null]);
        $this->notify((int) $b['customer_id'], 'schedule', 'Booking approved', "Your booking #$id for {$b['scheduled_at']} was approved.");

        return $this->ok(null, 'Booking approved.');
    }

    /** PUT /api/admin/bookings/(:num)/decline — { reason } */
    public function decline(int $id)
    {
        $b = $this->find($id, ['pending', 'approved', 'assigned']);
        if (! is_array($b)) {
            return $b;
        }
        $data = $this->input();
        if ($res = $this->check($data, ['reason' => 'required|max_length[1000]'])) {
            return $res;
        }
        (new BookingModel())->update($id, ['status' => 'declined', 'decline_reason' => $data['reason'], 'technician_id' => null]);
        $this->notify((int) $b['customer_id'], 'schedule', 'Booking declined', "Your booking #$id was declined: {$data['reason']}");

        return $this->ok(null, 'Booking declined.');
    }

    /** PUT /api/admin/bookings/(:num)/assign — { technician_id } */
    public function assign(int $id)
    {
        $b = $this->find($id, ['approved', 'assigned']);
        if (! is_array($b)) {
            return $b;
        }
        $data = $this->input();
        if ($res = $this->check($data, ['technician_id' => 'required|is_natural_no_zero'])) {
            return $res;
        }
        $tech = (new UserModel())->where('role', 'technician')->where('is_active', 1)->find($data['technician_id']);
        if (! $tech) {
            return $this->fail422(['technician_id' => 'Not an active technician.']);
        }

        (new BookingModel())->update($id, ['technician_id' => $tech['id'], 'status' => 'assigned']);
        $this->notify((int) $tech['id'], 'schedule', 'New job assigned', "You were assigned to booking #$id on {$b['scheduled_at']}.");
        $this->notify((int) $b['customer_id'], 'schedule', 'Technician assigned', "A technician was assigned to your booking #$id.");

        return $this->ok(null, 'Technician assigned.');
    }

    /** PUT /api/admin/bookings/(:num)/reschedule — { scheduled_at } */
    public function reschedule(int $id)
    {
        $b = $this->find($id, ['pending', 'approved', 'assigned']);
        if (! is_array($b)) {
            return $b;
        }
        $data = $this->input();
        if ($res = $this->check($data, ['scheduled_at' => 'required|valid_date[Y-m-d H:i:s]'])) {
            return $res;
        }
        (new BookingModel())->update($id, ['scheduled_at' => $data['scheduled_at']]);
        $this->notify((int) $b['customer_id'], 'schedule', 'Schedule changed', "Your booking #$id was moved to {$data['scheduled_at']}.");
        if ($b['technician_id']) {
            $this->notify((int) $b['technician_id'], 'schedule', 'Schedule changed', "Booking #$id was moved to {$data['scheduled_at']}.");
        }

        return $this->ok(null, 'Schedule adjusted.');
    }

    /** POST /api/admin/bookings/(:num)/payments — { amount, method } records a payment + receipt number. */
    public function recordPayment(int $id)
    {
        $b = (new BookingModel())->find($id);
        if (! $b) {
            return $this->notFound('Booking');
        }
        $data = $this->input();
        if ($res = $this->check($data, [
            'amount' => 'required|decimal|greater_than[0]',
            'method' => 'permit_empty|in_list[cash,gcash,bank_transfer,card,check]',
        ])) {
            return $res;
        }

        $m  = new PaymentModel();
        $pid = $m->insert([
            'booking_id'  => $id,
            'customer_id' => $b['customer_id'],
            'amount'      => $data['amount'],
            'method'      => $data['method'] ?? 'cash',
            'status'      => 'paid',
            'receipt_no'  => 'SSS-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3))),
            'paid_at'     => date('Y-m-d H:i:s'),
        ]);
        $this->notify((int) $b['customer_id'], 'system', 'Payment received', 'We received your payment. Your receipt is in your History tab.');

        return $this->ok($m->find($pid), 'Payment recorded.', 201);
    }

    /** @return array|\CodeIgniter\HTTP\ResponseInterface booking row, or an error response */
    private function find(int $id, array $allowedStatuses)
    {
        $b = (new BookingModel())->find($id);
        if (! $b) {
            return $this->notFound('Booking');
        }
        if (! in_array($b['status'], $allowedStatuses, true)) {
            return $this->fail422(['status' => "Not allowed while the booking is '{$b['status']}'."]);
        }

        return $b;
    }
}
