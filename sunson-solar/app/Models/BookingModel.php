<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table         = 'bookings';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['customer_id', 'service_id', 'technician_id', 'scheduled_at', 'site_address', 'notes', 'status', 'decline_reason', 'completed_at'];
    protected $useTimestamps = true;
}
