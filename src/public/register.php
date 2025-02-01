<?php
require_once '../config/database.php';
require_once '../controllers/AuthController.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AuthController();
    $result = $controller->register(
        $_POST['username'],
        $_POST['email'],
        $_POST['password'],
        $_POST['confirm_password']
    );
    if (isset($result['error'])) {
        $message = $result['error'];
    } else {
        $message = $result['success'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
<?php if ($message): ?>
    <p style="color: red;"><?php echo $message; ?></p>
<?php endif; ?>
<form method="POST">
    <label>Username:</label>
    <input type="text" name="username" required /><br>

    <label>Email:</label>
    <input type="email" name="email" required /><br>

    <label>Password:</label>
    <input type="password" name="password" required /><br>

    <label>Confirm Password:</label>
    <input type="password" name="confirm_password" required /><br>

    <button type="submit">Register</button>
</form>
</body>
</html>