<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\BaseApiController;
use App\Models\ApiTokenModel;
use App\Models\UserModel;

/** User management — admin and HR. Employees are created here (they cannot self-register). */
class UserController extends BaseApiController
{
    /** GET /api/admin/users?user_type=&role=&is_active=&q=&page=&per_page= */
    public function index()
    {
        $m = (new UserModel())
            ->select('users.id, users.first_name, users.last_name, users.middle_name, users.birthdate, users.gender, users.email,
                      users.phone, users.address, users.username, users.role, users.user_type, users.department_id,
                      users.is_active, users.created_at, departments.name AS department')
            ->join('departments', 'departments.id = users.department_id', 'left');

        foreach (['user_type', 'role', 'is_active'] as $f) {
            $v = $this->request->getGet($f);
            if ($v !== null && $v !== '') {
                $m->where("users.$f", $v);
            }
        }
        if ($q = $this->request->getGet('q')) {
            $m->groupStart()->like('users.first_name', $q)->orLike('users.last_name', $q)
                ->orLike('users.email', $q)->orLike('users.username', $q)->groupEnd();
        }

        $perPage = min(max((int) ($this->request->getGet('per_page') ?: 20), 1), 100);
        $page    = max((int) ($this->request->getGet('page') ?: 1), 1);
        $total   = (clone $m)->countAllResults(false);
        $rows    = $m->orderBy('users.id', 'DESC')->findAll($perPage, ($page - 1) * $perPage);

        return $this->ok(['total' => $total, 'page' => $page, 'per_page' => $perPage, 'items' => $rows]);
    }

    /** GET /api/admin/users/(:num) */
    public function show(int $id)
    {
        $u = (new UserModel())->find($id);

        return $u ? $this->ok(UserModel::safe($u)) : $this->notFound('User');
    }

    /** POST /api/admin/users — creates an EMPLOYEE account. */
    public function create()
    {
        $data  = $this->input();
        $roles = implode(',', UserModel::ROLES_EMPLOYEE);
        $rules = [
            'first_name'    => 'required|max_length[100]',
            'last_name'     => 'required|max_length[100]',
            'middle_name'   => 'permit_empty|max_length[100]',
            'birthdate'     => 'required|valid_date[Y-m-d]',
            'gender'        => 'required|in_list[male,female,other,prefer_not_to_say]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'phone'         => 'required|max_length[30]',
            'address'       => 'permit_empty',
            'username'      => 'required|alpha_numeric|min_length[4]|max_length[60]|is_unique[users.username]',
            'password'      => 'required|min_length[8]|max_length[72]',
            'role'          => "required|in_list[$roles]",
            'department_id' => 'required|is_natural_no_zero|is_not_unique[departments.id]',
        ];
        if ($res = $this->check($data, $rules)) {
            return $res;
        }
        if ($data['role'] === 'admin' && $this->me()['role'] !== 'admin') {
            return $this->forbidden('Only an admin can create another admin.');
        }

        $users  = new UserModel();
        $insert = array_intersect_key($data, array_flip(array_keys($rules)));
        $insert['user_type'] = 'employee';
        $id = $users->insert($insert);

        return $this->ok(UserModel::safe($users->find($id)), 'Employee account created.', 201);
    }

    /** PUT /api/admin/users/(:num) — "password" (reset) and "role" are admin-only. */
    public function update(int $id)
    {
        $users = new UserModel();
        $user  = $users->find($id);
        if (! $user) {
            return $this->notFound('User');
        }
        // HR manages employees only; admin accounts only by admins.
        if ($this->me()['role'] !== 'admin' && ($user['user_type'] !== 'employee' || $user['role'] === 'admin')) {
            return $this->forbidden();
        }

        $data  = $this->input();
        $roles = implode(',', UserModel::ROLES_EMPLOYEE);
        $rules = [
            'first_name'    => 'permit_empty|max_length[100]',
            'last_name'     => 'permit_empty|max_length[100]',
            'middle_name'   => 'permit_empty|max_length[100]',
            'birthdate'     => 'permit_empty|valid_date[Y-m-d]',
            'gender'        => 'permit_empty|in_list[male,female,other,prefer_not_to_say]',
            'email'         => "permit_empty|valid_email|is_unique[users.email,id,$id]",
            'phone'         => 'permit_empty|max_length[30]',
            'address'       => 'permit_empty',
            'username'      => "permit_empty|alpha_numeric|min_length[4]|max_length[60]|is_unique[users.username,id,$id]",
            'department_id' => 'permit_empty|is_natural_no_zero|is_not_unique[departments.id]',
        ];
        if ($this->me()['role'] === 'admin') {
            $rules['password'] = 'permit_empty|min_length[8]|max_length[72]';
            if ($user['user_type'] === 'employee') {
                $rules['role'] = "permit_empty|in_list[$roles]";
            }
        }
        if ($res = $this->check($data, $rules)) {
            return $res;
        }

        $update = array_intersect_key($data, array_flip(array_keys($rules)));
        if (isset($update['password']) && $update['password'] === '') {
            unset($update['password']);
        }
        if ($update) {
            $users->update($id, $update);
        }

        return $this->ok(UserModel::safe($users->find($id)), 'User updated.');
    }

    /** PUT /api/admin/users/(:num)/deactivate */
    public function deactivate(int $id)
    {
        return $this->setActive($id, false);
    }

    /** PUT /api/admin/users/(:num)/activate */
    public function activate(int $id)
    {
        return $this->setActive($id, true);
    }

    private function setActive(int $id, bool $active)
    {
        $users = new UserModel();
        $user  = $users->find($id);
        if (! $user) {
            return $this->notFound('User');
        }
        if ($id === (int) $this->me()['id']) {
            return $this->fail422(['id' => 'You cannot deactivate your own account.']);
        }
        if ($this->me()['role'] !== 'admin' && ($user['user_type'] !== 'employee' || $user['role'] === 'admin')) {
            return $this->forbidden();
        }

        $users->update($id, ['is_active' => $active ? 1 : 0]);
        if (! $active) {
            (new ApiTokenModel())->where('user_id', $id)->delete(); // force logout
        }

        return $this->ok(null, $active ? 'Account activated.' : 'Account deactivated.');
    }
}
