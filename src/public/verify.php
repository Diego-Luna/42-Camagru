<?php
require_once '../config/database.php';
require_once '../models/User.php';

$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    if (User::confirmUser($token)) {
        $message = "Cuenta confirmada exitosamente. <a href='login.php'>Inicia sesión ahora</a>.";
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
</head>
<body>
    <h1><?php echo $message; ?></h1>
</body>
</html>