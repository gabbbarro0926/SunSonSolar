<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\BaseApiController;
use App\Models\IncidentReportModel;

/** Admin dashboard side of technician problem reports. */
class IncidentController extends BaseApiController
{
    /** GET /api/admin/incidents?status=&category= */
    public function index()
    {
        $m = (new IncidentReportModel())
            ->select('incident_reports.*, users.first_name AS technician_first_name, users.last_name AS technician_last_name')
            ->join('users', 'users.id = incident_reports.technician_id');
        foreach (['status', 'category'] as $f) {
            if ($v = $this->request->getGet($f)) {
                $m->where("incident_reports.$f", $v);
            }
        }

        return $this->ok($m->orderBy('incident_reports.id', 'DESC')->findAll());
    }

    /** PUT /api/admin/incidents/(:num) — { status, admin_notes } */
    public function update(int $id)
    {
        $m = new IncidentReportModel();
        if (! $m->find($id)) {
            return $this->notFound('Report');
        }
        $data = $this->input();
        if ($res = $this->check($data, [
            'status'      => 'permit_empty|in_list[new,reviewed,resolved]',
            'admin_notes' => 'permit_empty|max_length[3000]',
        ])) {
            return $res;
        }
        $m->update($id, array_intersect_key($data, ['status' => 1, 'admin_notes' => 1]));

        return $this->ok($m->find($id), 'Report updated.');
    }
}
