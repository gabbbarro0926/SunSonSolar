<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table         = 'notifications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'type', 'title', 'message', 'is_read', 'created_by', 'created_at'];
    protected $useTimestamps = true;
    protected $updatedField  = '';
}
