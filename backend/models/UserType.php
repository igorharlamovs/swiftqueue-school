<?php
require_once __DIR__ . '/BaseModel.php';

class UserType extends BaseModel {
    protected $table = 'usertypes';

    public $id;
    public $typeName;
    public $typeVariable;
    public $createdAt;
    public $createdByUserId;
    public $modifiedAt;
    public $modifiedByUserId;
    public $deletedAt;
    public $deletedByUserId;

    // Laravel-style mutator
    public function setAttributes(array $attributes) {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Fetch all user types
     */
    public function all(): array {
        $stmt = $this->execute("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class, [$this->pdo]);
    }
}
