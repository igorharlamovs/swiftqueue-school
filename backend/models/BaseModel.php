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

    public function findById($id): mixed {
        $stmt = $this->execute("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function where($column, $value): mixed {
        $stmt = $this->execute("SELECT * FROM {$this->table} WHERE {$column} = ?", [$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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

    public function update(int $id, array $data): bool {
        $set = implode(", ", array_map(fn($col) => "$col = ?", array_keys($data)));
        $params = array_values($data);
        $params[] = $id;
        $stmt = $this->execute("UPDATE {$this->table} SET $set WHERE id = ?", $params);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool {
        $stmt = $this->execute("DELETE FROM {$this->table} WHERE id = ?", [$id]);
        return $stmt->rowCount() > 0;
    }
}
