<?php

namespace App\Controllers\Api\Admin;

use App\Models\ProductModel;

class ProductController extends AbstractCrudController
{
    protected string $modelClass = ProductModel::class;
    protected string $label      = 'Product';
    protected string $orderBy    = 'category';
    protected string $orderDir   = 'ASC';
    protected array $filters     = ['category', 'is_active'];
    protected array $rules       = [
        'category' => 'required|in_list[panels,inverters,batteries,racking_mounting,wires]',
        'name' => 'required|max_length[150]',
        'description' => 'permit_empty',
        'price' => 'required|decimal|greater_than_equal_to[0]',
        'stock' => 'permit_empty|is_natural',
        'image_url' => 'permit_empty|max_length[255]',
        'is_active' => 'permit_empty|in_list[0,1]',
    ];
}
