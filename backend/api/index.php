<?php
require_once __DIR__ . '/auth/cors.php';
require_once __DIR__ . '../../config/db.php';

$path = $_SERVER['REQUEST_URI'];

if (strpos($path, '/register') !== false) {
    require __DIR__ . '/auth/register.php';
} elseif (strpos($path, '/login') !== false) {
    require __DIR__ . '/auth/login.php';
} elseif (strpos($path, '/commonlookups') !== false) {
    require __DIR__ . '/commonlookups.php';
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Not found']);
}
