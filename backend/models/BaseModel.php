<?php
class BaseModel {
    protected $pdo;
    protected $table;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Prepare pdo statement
     * 
     * @param string $sql
     * @param array $params
     * 
     * @throws \Exception
     * @return PDOStatement
     */
    protected function execute(string $sql, array $params = []): PDOStatement {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Database query error: " . $e->getMessage());
        }
    }

    /**
     * Find a record by ID
     *
     * @param mixed $id
     * 
     * @return $this|null
     */
    public function findById($id): ?self {
        $stmt = $this->execute("SELECT * FROM {$this->table} WHERE id = ? AND deletedAt IS NULL", [$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::class, [$this->pdo]);
        $record = $stmt->fetch();

        return $record ?: null;
    }

    /**
     * Find conditionally
     * 
     * @param mixed $column
     * @param mixed $value
     * 
     * @return array
     */
    public function where($column, $value): mixed {
        $stmt = $this->execute("SELECT * FROM {$this->table} WHERE {$column} = ?", [$value]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class, [$this->pdo]);
    }

    /**
     * Create new record
     * 
     * @param array $data
     * 
     * @return BaseModel
     */
    public function create(array $data): static {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));

        $this->execute(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})",
            array_values($data)
        );

        $id = $this->pdo->lastInsertId();

        // Fetch the newly created record
        $stmt = $this->execute("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        // Create and hydrate a new instance of the current model
        $model = new static($this->pdo);
        foreach ($record as $key => $value) {
            if (property_exists($model, $key)) {
                $model->$key = $value;
            }
        }

        return $model;
    }

    /**
     * Update the current record
     *
     * @param int $actionedByUserId ID of the user performing the delete
     * 
     * @return $this The updated model instance
     * @throws Exception If no ID is set or no fields to update
     */
    public function update(int $actionedByUserId): self
    {
        if (!isset($this->id)) {
            throw new Exception('Cannot update record: ID is not set.');
        }

        $fillable = property_exists($this, 'fillable') ? $this->fillable : [];
        $objectVars = get_object_vars($this);
        unset($objectVars['pdo'], $objectVars['table'], $objectVars['fillable']);

        $setParts = [];
        $values = [];

        foreach ($objectVars as $key => $value) {
            if (!empty($fillable) && !in_array($key, $fillable, true)) {
                continue;
            }

            $setParts[] = "{$key} = ?";
            $values[] = $value;
        }

        $values[] = $actionedByUserId;
        $values[] = $this->id;
        $setClause = implode(', ', $setParts);

        $stmt = $this->execute("UPDATE {$this->table} SET {$setClause}, modifiedByUserId = ?  WHERE id = ?", $values);

        if ($stmt->rowCount() === 0) {
            throw new Exception("No changes made or record not found for ID {$this->id}");
        }

        // Re-fetch to sync object
        $stmt = $this->execute("SELECT * FROM {$this->table} WHERE id = ?", [$this->id]);
        $updatedRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        foreach ($updatedRecord as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        return $this;
    }


    /**
     * Soft delete the current record
     *
     * @param int $actionedByUserId ID of the user performing the delete
     * 
     * @return bool True if the record was soft deleted, false if not found or already deleted
     * @throws Exception if $this->id is not set
     */
    public function delete(int $actionedByUserId): bool
    {
        if (!isset($this->id)) {
            throw new Exception('Cannot delete: ID is not set on this object.');
        }

        $stmt = $this->execute(
            "UPDATE {$this->table} 
            SET deletedAt = NOW(), 
                deletedByUserId = ?, 
                modifiedByUserId = ? 
            WHERE id = ? AND deletedAt IS NULL",
            [$actionedByUserId, $actionedByUserId, $this->id]
        );

        return $stmt->rowCount() > 0;
    }

    /**
     * Fetch all non-deleted records
     * 
     * @return array
     */
    public function all(): array {
        $stmt = $this->execute("SELECT * FROM {$this->table} WHERE deletedAt IS NULL");
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class, [$this->pdo]);
    }
}
