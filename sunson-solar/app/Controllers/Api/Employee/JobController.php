<?php

namespace App\Controllers\Api\Employee;

use App\Controllers\BaseApiController;
use App\Models\BookingModel;

/**
 * Technician jobs. Only the minimum customer info is exposed:
 * name, address and service type — no email, phone, birthdate, etc.
 */
class JobController extends BaseApiController
{
    private function query(): BookingModel
    {
        return (new BookingModel())
            ->select("bookings.id, bookings.scheduled_at, bookings.status, bookings.site_address, bookings.notes,
                      services.name AS service_type,
                      users.first_name AS customer_first_name, users.last_name AS customer_last_name")
            ->join('services', 'services.id = bookings.service_id')
            ->join('users', 'users.id = bookings.customer_id')
            ->where('bookings.technician_id', $this->me()['id']);
    }

    /** GET /api/technician/jobs?status=assigned */
    public function index()
    {
        $q = $this->query();
        if ($s = $this->request->getGet('status')) {
            $q->where('bookings.status', $s);
        }

        return $this->ok($q->orderBy('bookings.scheduled_at')->findAll());
    }

    /** GET /api/technician/jobs/(:num) */
    public function show(int $id)
    {
        $j = $this->query()->where('bookings.id', $id)->first();

        return $j ? $this->ok($j) : $this->notFound('Job');
    }

    /** PUT /api/technician/jobs/(:num)/complete */
    public function complete(int $id)
    {
        $j = (new BookingModel())->where('technician_id', $this->me()['id'])->find($id);
        if (! $j) {
            return $this->notFound('Job');
        }
        if (! in_array($j['status'], ['assigned', 'in_progress'], true)) {
            return $this->fail422(['status' => 'This job cannot be completed from its current status.']);
        }

        (new BookingModel())->update($id, ['status' => 'completed', 'completed_at' => date('Y-m-d H:i:s')]);
        $this->notify((int) $j['customer_id'], 'schedule', 'Service completed', "Your service (booking #$id) has been completed. Thank you!");

        return $this->ok(null, 'Job marked as completed.');
    }
}
