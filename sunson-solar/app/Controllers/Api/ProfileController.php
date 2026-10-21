<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use App\Models\ApiTokenModel;
use App\Models\UserModel;

/** Profile for any logged-in user (customer or employee). */
class ProfileController extends BaseApiController
{
    /** GET /api/profile */
    public function show()
    {
        return $this->ok(UserModel::safe($this->me()));
    }

    /** PUT /api/profile — role, username and department cannot be changed here. */
    public function update()
    {
        $me   = $this->me();
        $data = $this->input();

        $rules = [
            'first_name'  => 'permit_empty|max_length[100]',
            'last_name'   => 'permit_empty|max_length[100]',
            'middle_name' => 'permit_empty|max_length[100]',
            'birthdate'   => 'permit_empty|valid_date[Y-m-d]',
            'gender'      => 'permit_empty|in_list[male,female,other,prefer_not_to_say]',
            'email'       => "permit_empty|valid_email|is_unique[users.email,id,{$me['id']}]",
            'phone'       => 'permit_empty|max_length[30]',
            'address'     => 'permit_empty',
        ];
        if ($res = $this->check($data, $rules)) {
            return $res;
        }

        $update = array_intersect_key($data, array_flip(array_keys($rules)));
        $users  = new UserModel();
        if ($update) {
            $users->update($me['id'], $update);
        }

        return $this->ok(UserModel::safe($users->find($me['id'])), 'Profile updated.');
    }

    /** PUT /api/profile/password — { current_password, new_password } */
    public function changePassword()
    {
        $me   = $this->me();
        $data = $this->input();

        if ($res = $this->check($data, [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[8]|max_length[72]',
        ])) {
            return $res;
        }

        if (! password_verify($data['current_password'], $me['password_hash'])) {
            return $this->fail422(['current_password' => 'Current password is incorrect.']);
        }

        (new UserModel())->update($me['id'], ['password' => $data['new_password']]);

        // Sign out every other session after a password change.
        preg_match('/^Bearer\s+(\S+)$/i', $this->request->getHeaderLine('Authorization'), $m);
        (new ApiTokenModel())->where('user_id', $me['id'])
            ->where('token_hash !=', hash('sha256', $m[1] ?? ''))->delete();

        return $this->ok(null, 'Password changed.');
    }

    /** PUT /api/profile/preferences — { pref_reminders, pref_promotions, pref_system } */
    public function preferences()
    {
        $data   = $this->input();
        $update = [];
        foreach (['pref_reminders', 'pref_promotions', 'pref_system'] as $k) {
            if (array_key_exists($k, $data)) {
                $update[$k] = filter_var($data[$k], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
        }
        $users = new UserModel();
        if ($update) {
            $users->update($this->me()['id'], $update);
        }

        return $this->ok(UserModel::safe($users->find($this->me()['id'])), 'Preferences saved.');
    }
}
