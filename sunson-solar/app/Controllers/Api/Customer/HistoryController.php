<?php

namespace App\Controllers\Api\Customer;

use App\Controllers\BaseApiController;
use App\Models\BookingModel;
use App\Models\PaymentModel;

/** Customer history tab: completed services, payments and receipts. */
class HistoryController extends BaseApiController
{
    /** GET /api/customer/history */
    public function index()
    {
        $uid = $this->me()['id'];

        $services = (new BookingModel())
            ->select('bookings.*, services.name AS service_name')
            ->join('services', 'services.id = bookings.service_id')
            ->where('bookings.customer_id', $uid)->where('bookings.status', 'completed')
            ->orderBy('bookings.completed_at', 'DESC')->findAll();

        $payments = (new PaymentModel())->where('customer_id', $uid)->orderBy('paid_at', 'DESC')->findAll();
        $total    = array_sum(array_map(fn ($p) => $p['status'] === 'paid' ? (float) $p['amount'] : 0, $payments));

        return $this->ok([
            'completed_services' => $services,
            'payments'           => $payments,
            'total_spent'        => round($total, 2),
        ]);
    }

    /** GET /api/customer/receipts/(:num) — payment id */
    public function receipt(int $id)
    {
        $p = (new PaymentModel())
            ->select('payments.*, services.name AS service_name, users.first_name, users.last_name, users.address')
            ->join('bookings', 'bookings.id = payments.booking_id')
            ->join('services', 'services.id = bookings.service_id')
            ->join('users', 'users.id = payments.customer_id')
            ->where('payments.customer_id', $this->me()['id'])->find($id);

        return $p ? $this->ok($p) : $this->notFound('Receipt');
    }
}
