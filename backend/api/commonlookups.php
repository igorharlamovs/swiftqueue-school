<?php
try {
    require_once __DIR__ . '/../models/BaseModel.php';
    require_once __DIR__ . '/../models/UserType.php';

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

    //Fetch course types
    $courseStatusTypes = [];

    echo json_encode([
        'success' => true,
        'message' => 'Data initialised successfully',
        'userTypes' => $userTypes,
        'courseStatusTypes' => $courseStatusTypes
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Initialisation failed',
        'error' => $e->getMessage()
    ]);
}
