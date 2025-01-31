<?php
class User {
    private $id;
    private $username;
    private $email;
    private $password;

    public function __construct($username, $email, $password) {
        $this->username = $username;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }

    public function save() {
        $pdo = getDatabaseConnection();
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password)
                VALUES (:username, :email, :password)
            ");
            
            return $stmt->execute([
                ':username' => $this->username,
                ':email' => $this->email,
                ':password' => $this->password
            ]);
        } catch (PDOException $e) {
            throw new Exception('User registration failed');
        }
    }

    public static function findById($id) {
        // Code to find a user by ID
    }

    public static function findByEmail($email) {
        // Code to find a user by email
    }
}

?>