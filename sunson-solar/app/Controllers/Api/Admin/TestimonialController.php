<?php

namespace App\Controllers\Api\Admin;

use App\Models\TestimonialModel;

class TestimonialController extends AbstractCrudController
{
    protected string $modelClass = TestimonialModel::class;
    protected string $label      = 'Testimonial';
    protected string $orderBy    = 'id';
    protected string $orderDir   = 'DESC';
    protected array $filters     = ['is_published'];
    protected array $rules       = [
        'customer_name' => 'required|max_length[150]',
        'content' => 'required',
        'rating' => 'permit_empty|in_list[1,2,3,4,5]',
        'is_published' => 'permit_empty|in_list[0,1]',
    ];
}
