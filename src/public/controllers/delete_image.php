<?php
require_once __DIR__ . '/../../controllers/SessionController.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Image.php';

SessionController::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['image_id'])) {
    header("Location: /create_Img.php");
    exit;
}

$imageId = intval($_POST['image_id']);
$userId = $_SESSION['user_id'];

$pdo = getDatabaseConnection();

// Verificar que la imagen pertenezca al usuario logueado
$stmt = $pdo->prepare("SELECT image_path FROM images WHERE id = :id AND user_id = :user_id");
$stmt->execute([':id' => $imageId, ':user_id' => $userId]);
$image = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$image) {
    // La imagen no existe o no pertenece al usuario
    header("Location: /create_Img.php");
    exit;
}

// Borrar el archivo de imagen del servidor
$filePath = __DIR__ . '/../../public/' . $image['image_path'];
if (file_exists($filePath)) {
    unlink($filePath);
}

// Borrar el registro de la base de datos
$stmt = $pdo->prepare("DELETE FROM images WHERE id = :id AND user_id = :user_id");
$stmt->execute([':id' => $imageId, ':user_id' => $userId]);

header("Location: /create_Img.php");
exit;
?>