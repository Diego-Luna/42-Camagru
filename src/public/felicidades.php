<?php

require_once '../controllers/SessionController.php';
SessionController::requireLogin();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Felicidades</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="container">
        <h1>¡Felicidades! Has iniciado sesión correctamente.</h1>
        <p><a href="edit.php">Ir a la página de edición</a></p>
        <p><a href="index.php">Ver la galería</a></p>
    </div>
</body>
</html>