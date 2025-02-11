<?php
require_once '../config/database.php';
require_once '../controllers/AuthController.php';
require_once '../controllers/sessionController.php';
SessionController::init();

$message = '';

$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postToken = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmPassword = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    if (strlen($password) < 8 ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/\d/', $password)) {
        $message = "Password must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {
        $controller = new AuthController();
        $result = $controller->resetPassword($postToken, $password, $confirmPassword);
        $message = isset($result['error']) ? $result['error'] : $result['success'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/clean_b.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="container my-4">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h2 class="card-title mb-4 text-center">Set New Password</h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo isset($result['error']) ? 'danger' : 'success'; ?>" role="alert">
                        <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-3">
                        <label class="form-label">New Password:</label>
                        <input type="password" name="password" class="form-control" required
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                               title="Must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password:</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Password</button>
                </form>
            </div>
        </div>
    </div>
    <?php include '../components/footer.php'; ?>
</body>
</html>