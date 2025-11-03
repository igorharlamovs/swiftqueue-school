<?php
require_once __DIR__ . '/../../models/User.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (!$email || !$password) {
        throw new Exception('Email and password are required.');
    }

    $userModel = new User($pdo);

    $user = $userModel->findByEmailAndPassword($email, $password);

    if(empty($user)) {
        throw new Exception('Invalid credentials');
    }

    // Generate a session token
    $token = bin2hex(random_bytes(32));

    echo json_encode([
        'success' => true,
        'data' => [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'userTypeId' => $user->userTypeId
            ],
            'token' => $token
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Login failed.',
        'error' => $e->getMessage()
    ]);
}
