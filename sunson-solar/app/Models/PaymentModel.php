<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table         = 'payments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['booking_id', 'customer_id', 'amount', 'method', 'status', 'receipt_no', 'paid_at'];
    protected $useTimestamps = true;
}
