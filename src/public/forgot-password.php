<?php
require_once '../config/database.php';
require_once '../controllers/AuthController.php';

require_once '../controllers/SessionController.php';
SessionController::init();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AuthController();
    $result = $controller->forgotPassword($_POST['email']);
    $message = isset($result['error']) ? $result['error'] : $result['success'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if ($message): ?>
            <p class="<?php echo isset($result['error']) ? 'error-message' : 'success-message'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit">Send Reset Link</button>
        </form>
        <p class="login-link">
            <a href="auth.php">Back to Login</a>
        </p>
    </div>
</body>
</html>