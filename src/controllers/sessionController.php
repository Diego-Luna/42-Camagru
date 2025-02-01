<?php
class SessionController {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin() {
        self::init();
        if (!self::isLoggedIn()) {
            header('Location: /auth.php');
            exit;
        }
    }

    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public static function getUsername() {
        return $_SESSION['username'] ?? null;
    }

    public static function logout() {
        self::init();
        $_SESSION = array();
        session_destroy();
        header('Location: /auth.php');
        exit();
    }
}
?>