<?php
require_once '../controllers/SessionController.php';
SessionController::requireLogin();
require_once '../models/User.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$user = User::findByUsername($_SESSION['username']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile - Camagru</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="container">
        <h2>User Profile</h2>
        <div class="profile-info">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
        </div>
    </div>
</body>
</html>