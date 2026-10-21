<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table         = 'attendance';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'full_name', 'booking_id', 'checked_in_at', 'latitude', 'longitude'];
    protected $useTimestamps = false;
}
