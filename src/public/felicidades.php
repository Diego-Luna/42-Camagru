<?php
require_once '../controllers/sessionController.php';
SessionController::requireLogin();

// Security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self' https://cdn.jsdelivr.net;img-src 'self' data:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Congratulations</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="container my-5 text-center">
        <h1>Congratulations! You have successfully logged in.</h1>
        <p>
            <a href="create_Img.php" class="btn btn-primary mt-3">Go to Image Creation</a>
        </p>
        <p>
            <a href="index.php" class="btn btn-secondary mt-3">View Gallery</a>
        </p>
    </div>
</body>
</html>