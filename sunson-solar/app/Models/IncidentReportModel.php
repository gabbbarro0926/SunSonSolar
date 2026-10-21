<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentReportModel extends Model
{
    protected $table         = 'incident_reports';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['technician_id', 'booking_id', 'category', 'description', 'status', 'admin_notes'];
    protected $useTimestamps = true;
}
