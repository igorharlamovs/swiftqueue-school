<?php
require_once __DIR__ . '/BaseModel.php';

class CourseStatusType extends BaseModel {
    protected $table = 'coursetatustypes';

    public $id;
    public $typeName;
    public $typeVariable;
    public $createdAt;
    public $createdByUserId;
    public $modifiedAt;
    public $modifiedByUserId;
    public $deletedAt;
    public $deletedByUserId;

    protected array $fillable = [
        'typeName',
        'typeVariable',
    ];
}
