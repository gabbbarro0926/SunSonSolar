<?php

namespace App\Controllers\Api\Admin;

use App\Models\PostModel;

class PostController extends AbstractCrudController
{
    protected string $modelClass = PostModel::class;
    protected string $label      = 'Post';
    protected string $orderBy    = 'id';
    protected string $orderDir   = 'DESC';
    protected array $filters     = ['is_published'];
    protected array $rules       = [
        'title' => 'required|max_length[200]',
        'body' => 'required',
        'is_published' => 'permit_empty|in_list[0,1]',
    ];

    protected function extraOnCreate(): array
    {
        return ['author_id' => $this->me()['id']];
    }
}
