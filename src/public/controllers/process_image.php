<?php
require_once __DIR__ . '/../../controllers/sessionController.php';
require_once __DIR__ . '/../../models/Image.php';
require_once __DIR__ . '/../../config/database.php';

SessionController::requireLogin();

header('Content-Type: application/json');

try {
    if (!isset($_FILES['image']) || !isset($_POST['stickers'])) {
        throw new Exception('Missing image or stickers');
    }

    $uploadDir = '../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = uniqid() . '.png';
    $uploadFile = $uploadDir . $filename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        throw new Exception('Failed to upload file');
    }

    $success = Image::save($_SESSION['user_id'], $uploadFile);
    
    echo json_encode([
        'success' => $success,
        'path' => $uploadFile
    ]);

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>