<?php
require_once '../config/database.php';
require_once '../controllers/AuthController.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AuthController();
    $res = $controller->login($_POST['username'], $_POST['password']);
    if (isset($res['error'])) {
        $message = $res['error'];
    } else {
        header('Location: felicidades.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($message): ?>
            <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
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
            Don't have an account? <a href="index.php">Register here</a>
        </p>
    </div>
</body>
</html>