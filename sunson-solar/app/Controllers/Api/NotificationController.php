<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use App\Models\NotificationModel;

/** Notifications panel — same for customers and employees. */
class NotificationController extends BaseApiController
{
    /** GET /api/notifications?unread=1 */
    public function index()
    {
        $m = (new NotificationModel())->where('user_id', $this->me()['id']);
        if ($this->request->getGet('unread')) {
            $m->where('is_read', 0);
        }
        $unread = (new NotificationModel())->where('user_id', $this->me()['id'])->where('is_read', 0)->countAllResults();

        return $this->ok(['unread_count' => $unread, 'items' => $m->orderBy('id', 'DESC')->findAll(100)]);
    }

    /** PUT /api/notifications/(:num)/read */
    public function markRead(int $id)
    {
        $m = new NotificationModel();
        if (! $m->where('user_id', $this->me()['id'])->find($id)) {
            return $this->notFound('Notification');
        }
        $m->update($id, ['is_read' => 1]);

        return $this->ok(null, 'Marked as read.');
    }

    /** PUT /api/notifications/read-all */
    public function markAllRead()
    {
        (new NotificationModel())->where('user_id', $this->me()['id'])->set('is_read', 1)->update();

        return $this->ok(null, 'All notifications marked as read.');
    }
}
