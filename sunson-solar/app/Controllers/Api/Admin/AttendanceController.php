<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\BaseApiController;
use App\Models\AttendanceModel;

/** Attendance / field-activity log for admin, HR and dispatchers. */
class AttendanceController extends BaseApiController
{
    /** GET /api/admin/attendance?user_id=&from=&to= */
    public function index()
    {
        $m = new AttendanceModel();
        if ($u = $this->request->getGet('user_id')) {
            $m->where('user_id', $u);
        }
        if ($f = $this->request->getGet('from')) {
            $m->where('checked_in_at >=', $f . ' 00:00:00');
        }
        if ($t = $this->request->getGet('to')) {
            $m->where('checked_in_at <=', $t . ' 23:59:59');
        }

        return $this->ok($m->orderBy('checked_in_at', 'DESC')->findAll(500));
    }
}
