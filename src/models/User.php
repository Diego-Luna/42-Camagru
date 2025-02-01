<?php
class User {
    private $id;
    private $username;
    private $email;
    private $password;
    private $confirmation_token;
    private $reset_token;
    private $reset_expires;
    private $confirmed = 0; // 0 = no confirmado; 1 = confirmado

    public function __construct($username, $email, $password) {
        $this->username = $username;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->confirmation_token = bin2hex(random_bytes(20));
    }

    public function save() {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, confirmation_token, confirmed)
            VALUES (:username, :email, :password, :token, :confirmed)
        ");
        return $stmt->execute([
            ':username' => $this->username,
            ':email' => $this->email,
            ':password' => $this->password,
            ':token' => $this->confirmation_token,
            ':confirmed' => $this->confirmed
        ]);
    }

    public function getToken() {
        return $this->confirmation_token;
    }

    public static function confirmUser($token) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("UPDATE users SET confirmed = 1 WHERE confirmation_token = :token");
        return $stmt->execute([':token' => $token]);
    }

    public static function isConfirmed($username) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("SELECT confirmed FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch();
        return $row && $row['confirmed'] == 1;
    }

    public static function findByUsername($username) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByEmail($email) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createPasswordReset($email) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            UPDATE users 
            SET reset_token = :token, reset_expires = :expires 
            WHERE email = :email
        ");
        
        return $stmt->execute([
            ':token' => $token,
            ':expires' => $expires,
            ':email' => $email
        ]) ? $token : false;
    }

    public static function resetPassword($token, $newPassword) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            UPDATE users 
            SET password = :password, reset_token = NULL, reset_expires = NULL 
            WHERE reset_token = :token 
            AND reset_expires > NOW()
        ");
        
        return $stmt->execute([
            ':password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ':token' => $token
        ]);
    }
}
?>