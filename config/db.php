<?php
declare(strict_types=1);

$host    = '127.0.0.1';
$name    = 'sun_son_solar';
$user    = 'root';
$pass    = '';       
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('DB connection failed: ' . $e->getMessage());  // pansamantala, i-remove sa production
}