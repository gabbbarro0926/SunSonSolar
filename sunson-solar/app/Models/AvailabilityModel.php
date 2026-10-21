<?php

namespace App\Models;

use CodeIgniter\Model;

class AvailabilityModel extends Model
{
    protected $table         = 'availability';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['day_of_week', 'start_time', 'end_time', 'max_bookings', 'is_active'];
    protected $useTimestamps = true;
}
