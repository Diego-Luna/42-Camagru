<?php
require_once '../controllers/sessionController.php';
SessionController::init();

require_once '../config/database.php';
require_once '../models/User.php';

// Security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self';img-src 'self' data:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    if (User::confirmUser($token)) {
        $message = "Account confirmed successfully. <a href='auth.php'>Login now</a>.";
    } else {
        $message = "Invalid token or account already confirmed.";
    }
} else {
    $message = "No token provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Verification</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/clean_b.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="container mt-5">
        <div class="alert alert-info" role="alert">
            <?php echo $message; ?>
        </div>
    </div>
    <?php include '../components/footer.php'; ?>
</body>
</html>