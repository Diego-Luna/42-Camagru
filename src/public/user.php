<?php
require_once '../controllers/SessionController.php';
SessionController::requireLogin();
require_once '../models/User.php';
require_once '../config/database.php';

$message = '';
$user = User::findByUsername($_SESSION['username']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $updates = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'notifications_enabled' => isset($_POST['notifications']) ? 1 : 0
        ];
        
        if (User::updateProfile($_SESSION['user_id'], $updates)) {
            $message = "Profile updated successfully";
            $user = User::findByUsername($_POST['username']);
            $_SESSION['username'] = $_POST['username'];
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
<html>
<head>
    <title>Profile - Camagru</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="container">
        <h2>User Profile</h2>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="profile-section">
            <h3>Update Profile</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="notifications" <?php echo $user['notifications_enabled'] ? 'checked' : ''; ?>>
                        Receive email notifications for comments
                    </label>
                </div>
                
                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>
        
        <div class="profile-section">
            <h3>Change Password</h3>
            <form method="POST" onsubmit="return validatePasswords()">
                
                <div class="form-group">
                    <label>New Password:</label>
                    <input type="password" name="new_password" id="new_password" 
                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                           title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                           required>
                </div>
                
                <div class="form-group">
                    <label>Confirm New Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    <span id="password_match" style="color: red;"></span>
                </div>
                
                <button type="submit" name="update_password">Update Password</button>
            </form>
        </div>
    </div>

    <script>
    function validatePasswords() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const matchSpan = document.getElementById('password_match');
        
        if (newPassword !== confirmPassword) {
            matchSpan.textContent = 'Passwords do not match!';
            return false;
        }
        matchSpan.textContent = '';
        return true;
    }

    document.getElementById('confirm_password').addEventListener('input', function() {
        validatePasswords();
    });
    </script>
</body>
</html>