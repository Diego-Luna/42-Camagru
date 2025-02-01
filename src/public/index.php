<?php
require_once '../controllers/SessionController.php';
SessionController::init();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="container">
        <h2>Galería</h2>

        <p class="login-link">
            Don't have an account? <a href="auth.php">Register here</a>
        </p>            
        <p class="login-link">
            You have an account? <a href="auth.php">Login</a>
        </p>            
    </div>
</body>
</html>