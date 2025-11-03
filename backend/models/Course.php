<?php
require_once __DIR__ . '/BaseModel.php';

class Course extends BaseModel {
    protected $table = 'courses';

    public $id;
    public $userId;
    public $statusTypeId;
    public $name;
    public $description;
    public $startAt;
    public $endAt;
    public $createdAt;
    public $createdByUserId;
    public $modifiedAt;
    public $modifiedByUserId;
    public $deletedAt;
    public $deletedByUserId;

    protected array $fillable = [
        'userId',
        'statusTypeId',
        'name',
        'description',
        'startAt',
        'endAt',
    ];
}
