<?php

require_once '../controllers/SessionController.php';
SessionController::init();

require_once '../config/database.php';
require_once '../controllers/AuthController.php';


$message = '';
$action = $_GET['action'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AuthController();
    
    if ($_POST['form_type'] === 'register') {
        $result = $controller->register(
            $_POST['username'],
            $_POST['email'],
            $_POST['password'],
            $_POST['confirm_password']
        );
        $message = isset($result['error']) ? $result['error'] : $result['success'];
    } else {
        $res = $controller->login($_POST['username'], $_POST['password']);
        if (isset($res['error'])) {
            $message = $res['error'];
        } else {
            header('Location: felicidades.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo ucfirst($action); ?> - Camagru</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>

    <div class="container">
        <?php if ($message): ?>
            <p class="<?php echo strpos($message, 'error') ? 'error-message' : 'success-message'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <?php if ($action === 'register'): ?>
            <h2>Register</h2>
            <form method="POST">
                <input type="hidden" name="form_type" value="register">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password:</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit">Register</button>
            </form>
            <p class="login-link">
                Already have an account? <a href="?action=login">Login here</a>
            </p>
        <?php else: ?>
            <h2>Login</h2>
            <form method="POST">
                <input type="hidden" name="form_type" value="login">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <p class="login-link">
                <a href="forgot-password.php">Forgot Password?</a>
            </p>
            <p class="login-link">
                Don't have an account? <a href="?action=register">Register here</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>