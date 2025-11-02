<?php
// Database configuration
$host = 'localhost';
$db   = 'swiftqueue_school';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// DSN string for PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Stop script and show error
    die(json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $e->getMessage()
    ]));
}
