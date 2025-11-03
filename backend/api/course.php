<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/CourseStatusType.php';

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    $lastSegment = end($segments);

    $userModel = new User($pdo);
    $courseModel = new Course($pdo);
    $userTypeModel = new UserType($pdo);

    switch ($method) {
        // FETCH course/s
        case 'GET':
            $courses = $courseModel->all();
            $result = [];

            foreach ($courses as $course) {
                $result[] = [
                    'id' => $course->id,
                    'userId' => $course->userId,
                    'statusTypeId' => $course->statusTypeId,
                    'name' => $course->name,
                    'description' => $course->description,
                    'startAt' => $course->startAt,
                    'endAt' => $course->endAt,
                ];
            }

            echo json_encode([
                'success' => true,
                'message' => 'Course list fetched successfully',
                'courses' => $courses,
            ]);
            break;

        // CREATE course
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            $userId = $data['userId'] ?? null;
            $statusTypeId = $data['statusTypeId'] ?? null;
            $name = $data['name'] ?? null;
            $description = $data['description'] ?? null;
            $startAt = $data['startAt'] ?? null;
            $endAt = $data['endAt'] ?? null;

            if (empty($name) || empty($startAt) || empty($endAt) || empty($userId) || empty($statusTypeId)){
                throw new Exception('Missing required fields');
            }

            // Set attributes
            $savedCourse = $courseModel->create([
                'userId' => $userId,
                'name' => $name,
                'description' => $description,
                'statusTypeId' => $statusTypeId,
                'startAt' => $startAt,
                'endAt' => $endAt,
                'createdByUserId' => $userId,
                'modifiedByUserId' => $userId,
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Course created successfully',
                'course' => $savedCourse,
            ]);
            break;

        // DELETE course
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);

            $userId = $data['userId'] ?? null;

            if (empty($userId)) {
                throw new Exception('Unknown user peforming request');
            }

            if (!is_numeric($lastSegment)) {
                throw new Exception('Invalid course ID');
            }

            $course = $courseModel->findById($lastSegment);

            $user = $userModel->findById($userId);
            $userType = $userTypeModel->findById($user->userTypeId);

            if($userType->typeVariable == 'student' || ($course->userId != $user->id && $userType->typeVariable != 'admin')) {
                throw new Exception('You are unauthorised to perform this action');
            }

            $deleted = $course->delete($userId);

            if (!$deleted) {
                throw new Exception('Course not found or could not be deleted');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Course deleted successfully',
            ]);
            break;

        // UPDATE course
        case 'PATCH':
            // Decode incoming JSON data
            $data = json_decode(file_get_contents('php://input'), true);

            if (!is_numeric($lastSegment)) {
                throw new Exception('Invalid course ID');
            }

            if (empty($data)) {
                throw new Exception('Invalid JSON payload');
            }

            // Fetch the existing course
            $course = $courseModel->findById($lastSegment);
            if (!$course) {
                throw new Exception('Course not found');
            }

            $course = $courseModel->findById($lastSegment);

            $user = $userModel->findById($data['sessionUserId']);
            $userType = $userTypeModel->findById($user->userTypeId);

            if($userType->typeVariable == 'student' || ($course->userId != $user->id && $userType->typeVariable != 'admin')) {
                throw new Exception('You are unauthorised to perform this action');
            }

            // Update only provided fields
            $updatableFields = ['statusTypeId', 'name', 'description', 'startAt', 'endAt', ];
            foreach ($updatableFields as $field) {
                if (array_key_exists($field, $data['editCourse'])) {
                    $course->$field = $data['editCourse'][$field];
                }
            }

            // Save updates
            $course->update($data['sessionUserId']);

            echo json_encode([
                'success' => true,
                'message' => 'Course updated successfully',
                'data' => $course,
            ]);
            break;

        //Fallback for unsupported
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error handling request',
        'error' => $e->getMessage(),
    ]);
}
