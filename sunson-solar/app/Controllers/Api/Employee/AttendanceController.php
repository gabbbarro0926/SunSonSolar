<?php

namespace App\Controllers\Api\Employee;

use App\Controllers\BaseApiController;
use App\Models\AttendanceModel;
use App\Models\BookingModel;

/**
 * One-button site check-in: the server records name + time; the app sends coordinates.
 */
class AttendanceController extends BaseApiController
{
    /** POST /api/attendance/check-in — { latitude, longitude, booking_id? } */
    public function checkIn()
    {
        $me   = $this->me();
        $data = $this->input();

        if ($res = $this->check($data, [
            'latitude'   => 'required|decimal|greater_than_equal_to[-90]|less_than_equal_to[90]',
            'longitude'  => 'required|decimal|greater_than_equal_to[-180]|less_than_equal_to[180]',
            'booking_id' => 'permit_empty|is_natural_no_zero',
        ])) {
            return $res;
        }

        $bookingId = $data['booking_id'] ?? null;
        if ($bookingId) {
            $booking = (new BookingModel())->where('technician_id', $me['id'])->find($bookingId);
            if (! $booking) {
                return $this->fail422(['booking_id' => 'That job is not assigned to you.']);
            }
            // Already checked in to this job today?
            $dup = (new AttendanceModel())->where('user_id', $me['id'])->where('booking_id', $bookingId)
                ->where('checked_in_at >=', date('Y-m-d') . ' 00:00:00')->countAllResults();
            if ($dup) {
                return $this->respond(['status' => 409, 'message' => 'You already checked in to this job today.'], 409);
            }
            if ($booking['status'] === 'assigned') {
                (new BookingModel())->update($bookingId, ['status' => 'in_progress']);
            }
        }

        $m  = new AttendanceModel();
        $id = $m->insert([
            'user_id'       => $me['id'],
            'full_name'     => trim($me['first_name'] . ' ' . $me['last_name']),
            'booking_id'    => $bookingId,
            'checked_in_at' => date('Y-m-d H:i:s'),
            'latitude'      => $data['latitude'],
            'longitude'     => $data['longitude'],
        ]);

        return $this->ok($m->find($id), 'Checked in. Salamat!', 201);
    }

    /** GET /api/attendance/mine?from=&to= */
    public function mine()
    {
        $m = (new AttendanceModel())->where('user_id', $this->me()['id']);
        if ($f = $this->request->getGet('from')) {
            $m->where('checked_in_at >=', $f . ' 00:00:00');
        }
        if ($t = $this->request->getGet('to')) {
            $m->where('checked_in_at <=', $t . ' 23:59:59');
        }

        return $this->ok($m->orderBy('checked_in_at', 'DESC')->findAll(200));
    }
}
