<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use App\Models\ApiTokenModel;
use App\Models\DepartmentModel;
use App\Models\UserModel;

class AuthController extends BaseApiController
{
    private const TOKEN_HOURS = 24;

    /** POST /api/auth/register — customers only (employees are created by HR/admin). */
    public function register()
    {
        $data = $this->input();

        $rules = [
            'first_name'  => 'required|max_length[100]',
            'last_name'   => 'required|max_length[100]',
            'middle_name' => 'permit_empty|max_length[100]',
            'birthdate'   => 'required|valid_date[Y-m-d]',
            'gender'      => 'required|in_list[male,female,other,prefer_not_to_say]',
            'email'       => 'required|valid_email|is_unique[users.email]',
            'phone'       => 'required|max_length[30]',
            'address'     => 'required',
            'username'    => 'required|alpha_numeric|min_length[4]|max_length[60]|is_unique[users.username]',
            'password'    => 'required|min_length[8]|max_length[72]',
        ];
        if ($res = $this->check($data, $rules)) {
            return $res;
        }

        $users = new UserModel();
        $id    = $users->insert([
            'first_name'  => $data['first_name'],
            'last_name'   => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'birthdate'   => $data['birthdate'],
            'gender'      => $data['gender'],
            'email'       => $data['email'],
            'phone'       => $data['phone'],
            'address'     => $data['address'],
            'username'    => $data['username'],
            'password'    => $data['password'],
            'role'        => 'customer',   // never taken from the request
            'user_type'   => 'customer',
        ]);

        return $this->ok(UserModel::safe($users->find($id)), 'Registration successful.', 201);
    }

    /** POST /api/auth/login — body: { username (or email), password } */
    public function login()
    {
        $data = $this->input();
        if ($res = $this->check($data, ['username' => 'required', 'password' => 'required'])) {
            return $res;
        }

        // Brute-force guard: 10 attempts per minute per (IP + username).
        // Keyed on both so one office network (shared IP) is not locked out by a single typo-prone user.
        $key = 'login_' . md5($this->request->getIPAddress() . '|' . strtolower((string) $data['username']));
        if (service('throttler')->check($key, 10, MINUTE) === false) {
            return $this->respond(['status' => 429, 'message' => 'Too many login attempts. Try again in a minute.'], 429);
        }

        $user = (new UserModel())
            ->groupStart()->where('username', $data['username'])->orWhere('email', $data['username'])->groupEnd()
            ->first();

        if (! $user || ! password_verify($data['password'], $user['password_hash'])) {
            return $this->respond(['status' => 401, 'message' => 'Invalid username or password.'], 401);
        }
        if ((int) $user['is_active'] !== 1) {
            return $this->forbidden('Your account is deactivated. Please contact the administrator.');
        }

        $plain   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + self::TOKEN_HOURS * 3600);
        (new ApiTokenModel())->insert([
            'user_id'    => $user['id'],
            'token_hash' => hash('sha256', $plain),
            'expires_at' => $expires,
        ]);

        return $this->ok([
            'token'      => $plain,
            'token_type' => 'Bearer',
            'expires_at' => $expires,
            'user'       => UserModel::safe($user),
        ], 'Login successful.');
    }

    /** POST /api/auth/logout */
    public function logout()
    {
        preg_match('/^Bearer\s+(\S+)$/i', $this->request->getHeaderLine('Authorization'), $m);
        (new ApiTokenModel())->where('token_hash', hash('sha256', $m[1] ?? ''))->delete();

        return $this->ok(null, 'Logged out.');
    }

    /** GET /api/auth/me */
    public function whoami()
    {
        $user = UserModel::safe($this->me());
        if ($user['department_id']) {
            $dept = (new DepartmentModel())->find($user['department_id']);
            $user['department'] = $dept['name'] ?? null;
        }

        return $this->ok($user);
    }

    /** GET /api/departments (public list used by the employee form) */
    public function departments()
    {
        return $this->ok((new DepartmentModel())->orderBy('name')->findAll());
    }
}
