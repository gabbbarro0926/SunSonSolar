<?php

namespace App\Controllers\Api\Employee;

use App\Controllers\BaseApiController;
use App\Models\BookingModel;
use App\Models\IncidentReportModel;
use App\Models\UserModel;

/** Technician problem reports → go straight to the admin dashboard. */
class IncidentController extends BaseApiController
{
    /** GET /api/technician/incidents */
    public function mine()
    {
        return $this->ok((new IncidentReportModel())->where('technician_id', $this->me()['id'])->orderBy('id', 'DESC')->findAll());
    }

    /** POST /api/technician/incidents — { category, description, booking_id? } */
    public function create()
    {
        $data = $this->input();
        if ($res = $this->check($data, [
            'category'    => 'required|in_list[equipment,customer_concern,safety,other]',
            'description' => 'required|max_length[3000]',
            'booking_id'  => 'permit_empty|is_natural_no_zero',
        ])) {
            return $res;
        }

        if (! empty($data['booking_id'])
            && ! (new BookingModel())->where('technician_id', $this->me()['id'])->find($data['booking_id'])) {
            return $this->fail422(['booking_id' => 'That job is not assigned to you.']);
        }

        $m  = new IncidentReportModel();
        $id = $m->insert([
            'technician_id' => $this->me()['id'],
            'booking_id'    => $data['booking_id'] ?? null,
            'category'      => $data['category'],
            'description'   => $data['description'],
            'status'        => 'new',
        ]);

        foreach ((new UserModel())->where('role', 'admin')->where('is_active', 1)->findAll() as $a) {
            $this->notify((int) $a['id'], 'system', 'New incident report', "Report #$id ({$data['category']}) was submitted by a technician.");
        }

        return $this->ok($m->find($id), 'Report submitted to the admin.', 201);
    }
}
