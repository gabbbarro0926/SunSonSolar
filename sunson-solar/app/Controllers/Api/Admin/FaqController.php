<?php

namespace App\Controllers\Api\Admin;

use App\Models\FaqModel;

class FaqController extends AbstractCrudController
{
    protected string $modelClass = FaqModel::class;
    protected string $label      = 'FAQ';
    protected string $orderBy    = 'sort_order';
    protected string $orderDir   = 'ASC';
    protected array $filters     = ['is_published'];
    protected array $rules       = [
        'question' => 'required|max_length[255]',
        'answer' => 'required',
        'sort_order' => 'permit_empty|is_natural',
        'is_published' => 'permit_empty|in_list[0,1]',
    ];
}
