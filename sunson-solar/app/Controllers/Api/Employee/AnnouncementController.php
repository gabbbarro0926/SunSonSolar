<?php

namespace App\Controllers\Api\Employee;

use App\Controllers\BaseApiController;
use App\Models\AnnouncementModel;

/** Employees read announcements meant for everyone or for their role. */
class AnnouncementController extends BaseApiController
{
    /** GET /api/announcements */
    public function index()
    {
        $role = $this->me()['role'];
        $rows = (new AnnouncementModel())
            ->groupStart()->where('audience', 'all')->orWhere('audience', $role)->groupEnd()
            ->orderBy('id', 'DESC')->findAll(100);

        return $this->ok($rows);
    }
}
