<?php
// Security headers with updated img-src and no inline scripts allowed
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self' https://cdn.jsdelivr.net");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

require_once '../controllers/sessionController.php';
SessionController::requireLogin();
require_once '../models/User.php';
require_once '../config/database.php';

$message = '';
$user = User::findByUsername($_SESSION['username']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $updates = [
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'notifications_enabled' => isset($_POST['notifications']) ? 1 : 0
        ];
        
        $result = User::updateProfile($_SESSION['user_id'], $updates); // Store the result
        
        if ($result === true) {
            $message = "Profile updated successfully";
            $_SESSION['username'] = $updates['username'];
            $user = User::findByUsername($updates['username']);
        } elseif (is_string($result)) {
            $message = $result; // Display the error message returned from updateProfile
        } else {
            $message = "Failed to update profile";
        }
    }
    
    
    if (isset($_POST['update_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            if (User::updatePassword($_SESSION['user_id'], $_POST['new_password'])) {
                $message = "Password updated successfully";
            } else {
                $message = "Failed to update password";
            }
        } else {
            $message = "Passwords do not match";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile - Camagru</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="container my-4">
        <h2 class="mb-4">User Profile</h2>
        <?php if ($message): ?>
            <div class="alert alert-info" role="alert">
                <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
            </div>
        <?php endif; ?>

        <!-- Update Profile Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Update Profile</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username:</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES, "UTF-8"); ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, "UTF-8"); ?>" class="form-control" required>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="notifications" class="form-check-input" id="notificationsCheckbox" <?php echo $user['notifications_enabled'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="notificationsCheckbox">Receive email notifications for comments</label>
                    </div>

                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Change Password Section -->
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Change Password</h3>
            </div>
            <div class="card-body">
                <form method="POST" id="passwordForm">
                    <div class="mb-3">
                        <label class="form-label">New Password:</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" 
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                               title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm New Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        <div id="password_match" class="form-text text-danger"></div>
                    </div>

                    <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <script src="./js/user.js"></script>
</body>
</html>