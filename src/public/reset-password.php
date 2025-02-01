<?php
require_once '../config/database.php';
require_once '../controllers/AuthController.php';

require_once '../controllers/SessionController.php';
SessionController::init();

$message = '';
$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AuthController();
    $result = $controller->resetPassword(
        $_POST['token'],
        $_POST['password'],
        $_POST['confirm_password']
    );
    $message = isset($result['error']) ? $result['error'] : $result['success'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Set New Password</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="container">
        <h2>Set New Password</h2>
        <?php if ($message): ?>
            <p class="<?php echo isset($result['error']) ? 'error-message' : 'success-message'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>