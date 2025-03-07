<?php
require_once '../config/database.php';
require_once '../controllers/AuthController.php';
require_once '../controllers/sessionController.php';

// Header de seguridad
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self';img-src 'self' data:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

SessionController::init();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logout') {
    SessionController::logout();
}

$message = '';
$action = $_GET['action'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    // Sanitizar y limpiar inputs
    $username = trim(strip_tags(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
    $password = trim(strip_tags(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''));
    $confirm_password = trim(strip_tags(filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''));

    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        $message = "Username must contain only letters, numbers, or underscores and be between 3 and 30 characters long.";
    } else {
        $controller = new AuthController();

        if (isset($_POST['form_type']) && $_POST['form_type'] === 'register') {
            $result = $controller->register(
                $username,
                $email,
                $password,
                $confirm_password
            );
            $message = isset($result['error']) ? $result['error'] : $result['success'];
        } elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'login') {
            $res = $controller->login($username, $password);
            if (isset($res['error'])) {
                $message = $res['error'];
            } else {
                header('Location: congratulations.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo ucfirst(htmlspecialchars($action, ENT_QUOTES, 'UTF-8')); ?> - Camagru</title>
    <link rel="stylesheet" href="./css/clean_b.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->

    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, 'error') !== false ? 'alert-danger' : 'alert-success'; ?>" role="alert">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($action === 'register'): ?>
            <h2>Register</h2>
            <form method="POST">
                <input type="hidden" name="form_type" value="register">
                <div class="mb-3">
                    <label class="form-label">Username:</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password:</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <p class="mt-3">
                Already have an account? <a href="?action=login">Login here</a>
            </p>
        <?php else: ?>
            <h2>Login</h2>
            <form method="POST">
                <input type="hidden" name="form_type" value="login">
                <div class="mb-3">
                    <label class="form-label">Username:</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p class="mt-3">
                <a href="forgot-password.php">Forgot Password?</a>
            </p>
            <p class="mt-3">
                Don't have an account? <a href="?action=register">Register here</a>
            </p>
        <?php endif; ?>
    </div>
    <?php include '../components/footer.php'; ?>
</body>
</html>