<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/UserType.php';

class User extends BaseModel {
    protected $table = 'users';

    public $id;
    public $name;
    public $email;
    protected $password;
    public $userTypeId;
    public $createdAt;
    public $createdByUserId;
    public $modifiedAt;
    public $modifiedByUserId;
    public $deletedAt;
    public $deletedByUserId;

    /**
     * Hash password on set
     * 
     * @param string $password
     * 
     * @return void
     */
    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    // Laravel-style mutator
    public function setAttributes(array $attributes) {
        foreach ($attributes as $key => $value) {
            if ($key === 'password') {
                $this->setPassword($value);
            } elseif (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return bool
     */
    public function emailExists(): bool {
        $stmt = $this->execute("SELECT id FROM {$this->table} WHERE email = ?", [$this->email]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function save() {
        $data = [
            'userTypeId' => $this->userTypeId,
            'name'       => $this->name,
            'email'      => $this->email,
            'password'   => $this->password,
            'createdAt'  => date('Y-m-d H:i:s'),
        ];

        if (isset($this->id)) {
            // TODO: Might add update logic here
        } else {
            $this->id = $this->create($data);
        }

        return $this->id;
    }

    /**
     * Confirm the password matches
     * 
     * @param string $password
     * 
     * @return bool
     */
    public function verifyPassword($password): bool {
        $stmt = $this->execute("SELECT password FROM {$this->table} WHERE email = ?", [$this->email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row && password_verify($password, $row['password']);
    }

    /**
     * Find a user by email and verify password
     *
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function findByEmailAndPassword(string $email, string $password): ?User
    {
        $stmt = $this->execute("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            // Populate current object with data from DB
            $this->setAttributes([
                'id'         => $row['id'],
                'name'       => $row['name'],
                'email'      => $row['email'],
                'password'   => $row['password'], // hashed password
                'userTypeId' => $row['userTypeId'] ?? null,
                'createdAt'  => $row['createdAt'] ?? null,
                'createdByUserId' => $row['createdByUserId'] ?? null,
                'modifiedAt' => $row['modifiedAt'] ?? null,
                'modifiedByUserId' => $row['modifiedByUserId'] ?? null,
                'deletedAt'  => $row['deletedAt'] ?? null,
                'deletedByUserId' => $row['deletedByUserId'] ?? null,
            ]);

            return $this;
        }

        return null; // not found or password invalid
    }
}
