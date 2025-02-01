<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    public function register($username, $email, $password, $confirm_password) {
        if ($password !== $confirm_password) {
            return ['error' => 'Las contraseñas no coinciden.'];
        }
        if (strlen($password) < 8) {
            return ['error' => 'La contraseña debe tener al menos 8 caracteres.'];
        }

        // Verificar si el usuario o email ya existen
        if (User::findByUsername($username)) {
            return ['error' => 'El nombre de usuario ya está en uso.'];
        }
        if (User::findByEmail($email)) {
            return ['error' => 'El correo electrónico ya está en uso.'];
        }

        try {
            $user = new User($username, $email, $password);
            if ($user->save()) {
                sendConfirmationEmail($email, $user->getToken());
                return ['success' => 'Registro exitoso. Revisa tu correo para confirmar la cuenta.'];
            }
            return ['error' => 'Error al registrar el usuario.'];
        } catch (Exception $e) {
            return ['error' => 'Error inesperado: ' . $e->getMessage()];
        }
    }

    public function login($username, $password) {
        $data = User::findByUsername($username);
        if (!$data) {
            return ['error' => 'Usuario no encontrado.'];
        }
        if ($data['confirmed'] == 0) {
            return ['error' => 'Cuenta no confirmada. Revisa tu correo.'];
        }
        if (!password_verify($password, $data['password'])) {
            return ['error' => 'Credenciales inválidas.'];
        }
        // Iniciar sesión
        session_start();
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        return ['success' => true];
    }

    public function forgotPassword($email) {
        $user = User::findByEmail($email);
        if (!$user) {
            return ['error' => 'Email not found'];
        }
    
        $token = User::createPasswordReset($email);
        if ($token && sendPasswordResetEmail($email, $token)) {
            return ['success' => 'Password reset link sent to your email'];
        }
        return ['error' => 'Failed to send reset link'];
    }
    
    public function resetPassword($token, $password, $confirmPassword) {
        if ($password !== $confirmPassword) {
            return ['error' => 'Passwords do not match'];
        }
        if (strlen($password) < 8) {
            return ['error' => 'Password must be at least 8 characters'];
        }
        
        if (User::resetPassword($token, $password)) {
            return ['success' => 'Password updated successfully'];
        }
        return ['error' => 'Invalid or expired reset link'];
    }


}
?>