<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    public function register($username, $email, $password, $confirm_password) {
        if ($password !== $confirm_password) {
            return ['error' => 'Passwords do not match.'];
        }
        if (strlen($password) < 8) {
            return ['error' => 'Password must be at least 8 characters long.'];
        }

        if (User::findByUsername($username)) {
            return ['error' => 'Username is already in use.'];
        }
        if (User::findByEmail($email)) {
            return ['error' => 'Email is already in use.'];
        }

        try {
            $user = new User($username, $email, $password);
            if ($user->save()) {
                sendConfirmationEmail($email, $user->getToken());
                return ['success' => 'Registration successful. Please check your email to confirm your account.'];
            }
            return ['error' => 'Error registering user.'];
        } catch (Exception $e) {
            return ['error' => 'Unexpected error: ' . $e->getMessage()];
        }
    }

    public function login($username, $password) {
        $data = User::findByUsername($username);
        if (!$data) {
            return ['error' => 'User not found.'];
        }
        if ($data['confirmed'] == 0) {
            return ['error' => 'Account not confirmed. Please check your email.'];
        }
        if (!password_verify($password, $data['password'])) {
            return ['error' => 'Invalid credentials.'];
        }

        SessionController::init();
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        return ['success' => true];
    }

    public function forgotPassword($email) {
        $user = User::findByEmail($email);
        if (!$user) {
            return ['error' => 'Email not found.'];
        }
    
        $token = User::createPasswordReset($email);
        if ($token && sendPasswordResetEmail($email, $token)) {
            return ['success' => 'Password reset link has been sent to your email.'];
        }
        return ['error' => 'Failed to send reset link.'];
    }
    
    public function resetPassword($token, $password, $confirmPassword) {
        if ($password !== $confirmPassword) {
            return ['error' => 'Passwords do not match.'];
        }
        if (strlen($password) < 8) {
            return ['error' => 'Password must be at least 8 characters long.'];
        }
        
        if (User::resetPassword($token, $password)) {
            return ['success' => 'Password has been updated successfully.'];
        }
        return ['error' => 'Invalid or expired reset link.'];
    }
}
?>