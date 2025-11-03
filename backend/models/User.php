<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/UserType.php';

class User extends BaseModel {
    protected $table = 'users';

    public $id;
    public $userTypeId;
    public $name;
    public $email;
    protected $password;
    public $createdAt;
    public $createdByUserId;
    public $modifiedAt;
    public $modifiedByUserId;
    public $deletedAt;
    public $deletedByUserId;

    protected array $fillable = [
        'name',
        'email',
        'password',
        'userTypeId',
    ];

    /**
     * Hash password on set
     * 
     * @param string $password
     * 
     * @return void
     */
    public function setPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @return bool
     */
    public function emailExists(): bool {
        $stmt = $this->execute("SELECT id FROM {$this->table} WHERE email = ?", [$this->email]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
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
     * 
     * @return User|null
     */
    public function findByEmailAndPassword(string $email, string $password): ?User
    {
        $stmt = $this->execute("SELECT * FROM {$this->table} WHERE email = ? AND deletedAt IS NULL", [$email]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::class, [$this->pdo]);
        $record = $stmt->fetch();

        return ($record && password_verify($password, $record->password)) ? $record : null;
    }
}
