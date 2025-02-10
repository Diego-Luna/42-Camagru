<?php
session_start();
require_once __DIR__ . '/../../controllers/sessionController.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Comment.php';
require_once __DIR__ . '/../../models/User.php';

// Security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

// Verify user is logged in
SessionController::requireLogin();

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    die('Invalid CSRF token');
}

$timeWindow = 60;
$maxComments = 5;
if (isset($_SESSION['last_comment_time'])) {
    if (time() - $_SESSION['last_comment_time'] < $timeWindow) {
        $_SESSION['comment_count'] = ($_SESSION['comment_count'] ?? 0) + 1;
        if ($_SESSION['comment_count'] > $maxComments) {
            http_response_code(429);
            die('Too many comments. Please wait a moment.');
        }
    } else {
        $_SESSION['comment_count'] = 1;
    }
}
$_SESSION['last_comment_time'] = time();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id']) && isset($_POST['comment'])) {
    $imageId = filter_var($_POST['image_id'], FILTER_VALIDATE_INT);
    $userId = filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT);
    $content = trim(strip_tags($_POST['comment']));
    
    if ($imageId === false || $userId === false) {
        http_response_code(400);
        die('Invalid input');
    }

    if (strlen($content) > 500) {
        http_response_code(400);
        die('Comment too long');
    }
    
    if ($content !== "") {
        if (Comment::saveComment($userId, $imageId, $content)) {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare("SELECT user_id FROM images WHERE id = ?");
            $stmt->execute([$imageId]);
            $imageOwner = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($imageOwner) {
                $ownerId = (int)$imageOwner['user_id'];
                if ($ownerId !== $userId) {
                    $stmt = $pdo->prepare("SELECT email, notifications_enabled FROM users WHERE id = ?");
                    $stmt->execute([$ownerId]);
                    $owner = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($owner && $owner['notifications_enabled']) {
                        $to = filter_var($owner['email'], FILTER_SANITIZE_EMAIL);
                        $subject = "New comment on your image";
                        $message = wordwrap(
                            "Your image (ID: $imageId) has received a new comment.", 
                            70, 
                            "\r\n"
                        );
                        $headers = 'From: noreply@camagru.com' . "\r\n" .
                                 'X-Mailer: PHP/' . phpversion();
                        
                        mail($to, $subject, $message, $headers);
                    }
                }
            }
        }
    }
}

header("Location: ../index.php");
exit;