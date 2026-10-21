<?php

namespace App\Models;

use CodeIgniter\Model;

class QuoteRequestModel extends Model
{
    protected $table         = 'quote_requests';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'email', 'phone', 'address', 'service_id', 'message', 'status'];
    protected $useTimestamps = true;
}
