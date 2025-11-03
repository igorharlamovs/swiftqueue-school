<?php
try {
    require_once __DIR__ . '/../models/BaseModel.php';
    require_once __DIR__ . '/../models/UserType.php';
    require_once __DIR__ . '/../models/CourseStatusType.php';

    //Fetch user types
    $userTypeModel = new UserType($pdo);
    $userTypesList = $userTypeModel->all();

    foreach ($userTypesList as $type) {
        $userTypes[] = [
            'id' => $type->id,
            'typeName' => $type->typeName,
            'typeVariable' => $type->typeVariable,
        ];
    }

    //Fetch course status types
    $courseStatusTypeModel = new CourseStatusType($pdo);
    $courseStatusTypesList = $courseStatusTypeModel->all();

    foreach ($courseStatusTypesList as $type) {
        $courseStatusTypes[] = [
            'id' => $type->id,
            'typeName' => $type->typeName,
            'typeVariable' => $type->typeVariable,
        ];
    }

    echo json_encode([
        'success' => true,
        'message' => 'Data initialised successfully',
        'data' => [
            'userTypes' => $userTypes,
            'courseStatusTypes' => $courseStatusTypes
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Initialisation failed',
        'error' => $e->getMessage()
    ]);
}
