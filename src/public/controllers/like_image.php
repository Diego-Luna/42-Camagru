<?php
require_once __DIR__ . '/../../controllers/SessionController.php';
SessionController::requireLogin();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Like.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id'])) {
    $imageId = intval($_POST['image_id']);
    $userId = $_SESSION['user_id'];
    Like::saveLike($userId, $imageId);
}


header("Location: ../index.php");
exit;
?>