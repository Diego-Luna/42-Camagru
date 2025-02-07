<?php
class User {
    private $id;
    private $username;
    private $email;
    private $password;
    private $confirmation_token;
    private $reset_token;
    private $reset_expires;
    private $confirmed = 0;

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

    public static function updateProfile($userId, $data) {
        $pdo = getDatabaseConnection();
    
        $username = trim($data['username']);
        $email = trim($data['email']);
    
        if (isset($username)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :userId");
            $stmt->execute([
                ':username' => $username,
                ':userId' => $userId
            ]);
            if ($stmt->fetch()) {
                return "Username already exists. Please choose another one.";
            }
        }
    
        if (isset($email)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :userId");
            $stmt->execute([
                ':email' => $email,
                ':userId' => $userId
            ]);
            if ($stmt->fetch()) {
                return "Email already exists. Please choose another one.";
            }
        }
    
        $updates = [];
        $params = [':id' => $userId];
    
        if ($username) {
            $updates[] = "username = :username";
            $params[':username'] = $username;
        }
        if ($email) {
            $updates[] = "email = :email";
            $params[':email'] = $email;
        }
        if (isset($data['notifications_enabled'])) {
            $updates[] = "notifications_enabled = :notifications";
            $params[':notifications'] = $data['notifications_enabled'];
        }
    
        if (empty($updates)) {
            return false;
        }
    
        try {
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                return "Username or email already in use. Please choose another one.";
            }
            throw $e;
        }
    }

    public static function updatePassword($userId, $newPassword) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            UPDATE users 
            SET password = :password 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ':id' => $userId
        ]);
    }

    public static function findById($id) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>