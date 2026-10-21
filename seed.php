<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config/db.php';

$admins = [
    [
        'username'   => 'KittyKat16',
        'email'      => 'kat@sunson.solar',
        'password'   => 'ChangeMe!2025',
        'first'      => 'Katherine',
        'last'       => 'Sinagaraw',
        'gender'     => 'Female',
        'birth_date'  => '1990-01-01',
        'phone_no'      => '09920185736',
        'address'    => 'Sun Son Solar Office, Pasig City',
        'department' => 'Admin',
    ],
    [
        'username'   => 'SolSolis',
        'email'      => 'sol@sunson.solar',
        'password'   => 'ChangeMe!2025',
        'first'      => 'Sol',
        'last'       => 'Solis',
        'gender'     => 'Male',
        'birth_date'  => '1990-01-01',
        'phone_no'      => '09032432501',
        'address'    => 'Sun Son Solar Office, Pasig City',
        'department' => 'Admin',
    ],
];

foreach ($admins as $u) {
    try {
        $pdo->beginTransaction();

      
        $userType = 'admin';

        $stmt = $pdo->prepare(
            'INSERT INTO users (username, email, password_hash, user_type, must_change_password)
             VALUES (?, ?, ?, ?, 1)'
        );
        $stmt->execute([
            $u['username'],
            $u['email'],
            password_hash($u['password'], PASSWORD_DEFAULT),
            $userType,
        ]);
        $uid = (int) $pdo->lastInsertId();

        $stmt = $pdo->prepare(
            'INSERT INTO employees
             (user_id, first_name, middle_name, last_name, birthdate, gender, phone, address, department)
             VALUES (?, ?, NULL, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $uid,
            $u['first'],
            $u['last'],
            $u['birth_date'],
            $u['gender'],
            $u['phone_no'],
            $u['address'],
            $u['department'],
        ]);

        $pdo->commit();
        echo "✓ Seeded: {$u['username']} ({$u['department']}, type={$userType}) — user_id={$uid}\n";

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "✗ Failed {$u['username']}: " . $e->getMessage() . "\n";
    }
}

echo "\nDone. Seeded 2 admins (Kat + Sol).\n";