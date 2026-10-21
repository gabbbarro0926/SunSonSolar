<?php

namespace App\Controllers\Api\Admin;

use App\Models\ServiceModel;

class ServiceController extends AbstractCrudController
{
    protected string $modelClass = ServiceModel::class;
    protected string $label      = 'Service';
    protected string $orderBy    = 'id';
    protected string $orderDir   = 'ASC';
    protected array $filters     = [];
    protected array $rules       = [
        'name' => 'required|max_length[120]|is_unique[services.name,id,{id}]',
        'description' => 'permit_empty',
        'price' => 'required|decimal|greater_than_equal_to[0]',
        'duration_minutes' => 'permit_empty|is_natural_no_zero',
        'is_active' => 'permit_empty|in_list[0,1]',
    ];
}
