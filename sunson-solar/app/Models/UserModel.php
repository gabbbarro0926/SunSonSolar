<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    public const ROLES_EMPLOYEE = ['admin', 'hr', 'dispatcher', 'technician', 'staff'];

    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'first_name', 'last_name', 'middle_name', 'birthdate', 'gender', 'email', 'phone',
        'address', 'username', 'password', 'password_hash', 'role', 'user_type', 'department_id',
        'is_active', 'pref_reminders', 'pref_promotions', 'pref_system',
    ];

    protected $useTimestamps = true;

    // Callers pass a plain "password"; it is hashed here and never stored as-is.
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
        }

        return $data;
    }

    /** Remove sensitive columns before returning a user in JSON. */
    public static function safe(?array $user): ?array
    {
        if ($user === null) {
            return null;
        }
        unset($user['password_hash']);

        return $user;
    }
}
