<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['category', 'name', 'description', 'price', 'stock', 'image_url', 'is_active'];
    protected $useTimestamps = true;
}
