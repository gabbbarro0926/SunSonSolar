<?php

namespace App\Controllers\Api\Admin;

use App\Models\AnnouncementModel;
use App\Models\UserModel;

/** Admin/HR post announcements to employees (all, or one role). */
class AnnouncementController extends AbstractCrudController
{
    protected string $modelClass = AnnouncementModel::class;
    protected string $label      = 'Announcement';
    protected array $filters     = ['priority', 'audience'];
    protected array $rules       = [
        'title'    => 'required|max_length[200]',
        'body'     => 'required',
        'priority' => 'permit_empty|in_list[normal,safety,urgent]',
        'audience' => 'permit_empty|in_list[all,admin,hr,dispatcher,technician,staff]',
    ];

    protected function extraOnCreate(): array
    {
        return ['created_by' => $this->me()['id']];
    }

    /** Also drop a notification into every targeted employee's panel. */
    protected function afterCreate(array $row): void
    {
        $users = (new UserModel())->where('user_type', 'employee')->where('is_active', 1);
        if (($row['audience'] ?? 'all') !== 'all') {
            $users->where('role', $row['audience']);
        }
        foreach ($users->findAll() as $u) {
            $this->notify((int) $u['id'], 'system', strtoupper($row['priority']) . ': ' . $row['title'], $row['body']);
        }
    }
}
