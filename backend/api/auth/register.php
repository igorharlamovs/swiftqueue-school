<?php
require_once __DIR__ . '/../../models/BaseModel.php';
require_once __DIR__ . '/../../models/User.php';

try {
    // Decode incoming JSON
    $data = json_decode(file_get_contents('php://input'), true);

    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $userType = $data['userType'] ?? '';

    // Validate input
    if (!$name || !$email || !$password || empty($userType)) {
        throw new Exception('All fields are required');
    }

    // Create New User
    $user = new User($pdo);
    $user->setAttributes([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'userTypeId' => $userType['id']
    ]);

    // Check if email already exists
    if ($user->emailExists()) {
        throw new Exception('Email already registered');
    }

    // Save user to DB
    $user = $user->save();

    // Generate a random token for this session
    $token = bin2hex(random_bytes(32));

    echo json_encode([
        'success' => true,
        'message' => 'User registered successfully',
        'data' => [
            'user' => $user,
            'token' => $token
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Registration failed',
        'error' => $e->getMessage()
    ]);
}
