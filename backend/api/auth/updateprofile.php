<?php
require_once __DIR__ . '/../../models/BaseModel.php';
require_once __DIR__ . '/../../models/User.php';

try {
    // Decode incoming JSON
    $data = json_decode(file_get_contents('php://input'), true);

    $userId = $data['id'] ?? '';
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $userTypeId = $data['userTypeId'] ?? '';

    // Validate input
    if (empty($userId) || empty($name) || empty($email) || empty($userTypeId)) {
        throw new Exception('All fields are required');
    }

    // Update User
    $userModel = new User($pdo);

    // Fetch the existing user
    $user = $userModel->findById($userId);

    if (!$user) {
        throw new Exception('User not found');
    }

    // Update only provided fields
    $updatableFields = ['name', 'email', 'userTypeId'];
    foreach ($updatableFields as $field) {
        if (array_key_exists($field, $data)) {
            $user->$field = $data[$field];
        }
    }

    // Check if email already exists
    if ($userModel->emailExists()) {
        throw new Exception('Email already registered');
    }

    // Save updates
    $user->update($userId);

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
        'message' => 'Failed updating user',
        'error' => $e->getMessage()
    ]);
}
