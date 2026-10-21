<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\BaseApiController;
use App\Models\NotificationModel;
use App\Models\UserModel;

/**
 * Notifications control panel: admin decides which alert goes to whom.
 * Customers who switched off reminders / promotions / system alerts in
 * their preferences are skipped (schedule changes are always delivered).
 */
class NotificationController extends BaseApiController
{
    private const PREF = ['reminder' => 'pref_reminders', 'promotion' => 'pref_promotions', 'system' => 'pref_system'];

    /** POST /api/admin/notifications/send
     *  { type: reminder|promotion|schedule|system, title, message,
     *    audience: all_customers|all_employees|everyone|role:technician|users, user_ids?: [] } */
    public function send()
    {
        $data = $this->input();
        if ($res = $this->check($data, [
            'type'     => 'required|in_list[reminder,promotion,schedule,system]',
            'title'    => 'required|max_length[200]',
            'message'  => 'required|max_length[3000]',
            'audience' => 'required',
        ])) {
            return $res;
        }

        $users = (new UserModel())->select('id, role, user_type, pref_reminders, pref_promotions, pref_system')->where('is_active', 1);
        $aud   = $data['audience'];

        if ($aud === 'all_customers') {
            $users->where('user_type', 'customer');
        } elseif ($aud === 'all_employees') {
            $users->where('user_type', 'employee');
        } elseif ($aud === 'everyone') {
            // no filter
        } elseif (str_starts_with($aud, 'role:')) {
            $users->where('role', substr($aud, 5));
        } elseif ($aud === 'users') {
            $ids = array_filter(array_map('intval', (array) ($data['user_ids'] ?? [])));
            if (! $ids) {
                return $this->fail422(['user_ids' => 'Provide at least one user id.']);
            }
            $users->whereIn('id', $ids);
        } else {
            return $this->fail422(['audience' => 'Invalid audience.']);
        }

        $sent = 0;
        $m    = new NotificationModel();
        foreach ($users->findAll() as $u) {
            $pref = self::PREF[$data['type']] ?? null;
            if ($pref && $u['user_type'] === 'customer' && (int) $u[$pref] === 0) {
                continue;
            }
            $m->insert([
                'user_id'    => $u['id'],
                'type'       => $data['type'],
                'title'      => $data['title'],
                'message'    => $data['message'],
                'created_by' => $this->me()['id'],
            ]);
            $sent++;
        }

        return $this->ok(['recipients' => $sent], "Notification sent to $sent user(s).", 201);
    }

    /** GET /api/admin/notifications — recently sent (for the control panel history) */
    public function sent()
    {
        $rows = (new NotificationModel())->where('created_by IS NOT NULL')->orderBy('id', 'DESC')->findAll(200);

        return $this->ok($rows);
    }
}
