<?php
declare(strict_types=1);

const ALLOWED_GENDERS     = ['Male', 'Female'];
const ALLOWED_DEPARTMENTS = ['Technician', 'Admin', 'Sales', 'HR'];
const ALLOWED_USER_TYPES  = ['customer', 'employee'];           

function validate_registration(array $data, PDO $pdo): array
{
    $errors = [];
    $namePattern = "/^[A-Za-zÀ-ÿ' -]{2,50}$/u";

    if (empty($data['first_name']) || !preg_match($namePattern, $data['first_name'])) {
        $errors['first_name'] = 'Enter a name using letters only.';
    }
    if (empty($data['last_name']) || !preg_match($namePattern, $data['last_name'])) {
        $errors['last_name'] = 'Enter a name using letters only.';
    }
    if (!empty($data['middle_name']) && !preg_match($namePattern, $data['middle_name'])) {
        $errors['middle_name'] = 'Use letters only.';
    }

    $bd = DateTime::createFromFormat('Y-m-d', $data['birthdate'] ?? '');
    if (!$bd) {
        $errors['birt_hdate'] = 'Enter a valid birth_date.';
    } else {
        $today = new DateTime('today');
        if ($bd > $today) {
            $errors['birth_date'] = 'Enter a valid birthdate.';
        }
    }

    if (empty($data['gender']) || !in_array($data['gender'], ALLOWED_GENDERS, true)) {
        $errors['gender'] = 'Select a gender.';
    }

    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } elseif (strlen($data['email']) > 100) {
        $errors['email'] = 'Email is too long.';
    } else {
        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'That email is already registered.';
        }
    }

    $phone = preg_replace('/\s+/', '', $data['phone'] ?? '');
    if (!preg_match('/^(09\d{9}|\+639\d{8})$/', $phone)) {
        $errors['phone'] = 'Enter an 11-digit number starting with 09.';
    }

    $addrLen = mb_strlen(trim($data['address'] ?? ''));
    if ($addrLen < 10 || $addrLen > 200) {
        $errors['address'] = 'Enter your complete address.';
    }

    if (empty($data['username']) || !preg_match('/^[A-Za-z0-9_]{4,20}$/', $data['username'])) {
        $errors['username'] = 'Username must be 4–20 characters (letters, numbers, underscores).';
    } else {
        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$data['username']]);
        if ($stmt->fetch()) {
            $errors['username'] = 'That username is taken.';
        }
    }

    $pw = $data['password'] ?? '';
    if (strlen($pw) < 8
        || !preg_match('/[A-Z]/', $pw)
        || !preg_match('/[0-9]/', $pw)
        || !preg_match('/[^A-Za-z0-9]/', $pw)) {
        $errors['password'] = 'Password needs 8+ characters with a capital, number and symbol.';
    }
    if (($data['confirm_password'] ?? '') !== $pw) {
        $errors['confirm_password'] = "Passwords don't match.";
    }

    // --- User type ---
    $type = $data['user_type'] ?? '';
    if (!in_array($type, ALLOWED_USER_TYPES, true)) {
        $errors['user_type'] = 'Select an account type.';
    }

    // --- Department (employees only) ---
    if ($type === 'employee') {
        if (empty($data['department']) || !in_array($data['department'], ALLOWED_DEPARTMENTS, true)) {
            $errors['department'] = 'Select a department.';
        }
    }

    return $errors;
}