<?php
require_once '../controllers/SessionController.php';
SessionController::init();

require_once '../config/database.php';
require_once '../models/User.php';

$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    if (User::confirmUser($token)) {
        $message = "Cuenta confirmada exitosamente. <a href='auth.php'>Inicia sesión ahora</a>.";
    } else {
        $message = "Token inválido o cuenta ya confirmada.";
    }
} else {
    $message = "No se proporcionó ningún token.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verificación de Cuenta</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <h1><?php echo $message; ?></h1>
</body>
</html>