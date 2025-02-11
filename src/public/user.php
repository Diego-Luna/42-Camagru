<?php
session_start();
require_once '../controllers/sessionController.php';
SessionController::requireLogin();
require_once '../models/User.php';
require_once '../config/database.php';

// Generate a CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$user = User::findByUsername($_SESSION['username']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Error: Invalid CSRF token");
    }
    
    if (isset($_POST['update_profile'])) {
        // Sanitize and validate inputs
        $raw_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $raw_email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $username = trim($raw_username);
        $email = trim($raw_email);
    
        // Validate username format: only letters, numbers, and underscores, 3-30 characters long
        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $message = "Username must be alphanumeric (underscores allowed) and be between 3 and 30 characters.";
        }
        // Validate email
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "The email address is not valid.";
        } else {
            $updates = [
                'username' => $username,
                'email'    => $email,
                'notifications_enabled' => isset($_POST['notifications']) ? 1 : 0
            ];
        
            $result = User::updateProfile($_SESSION['user_id'], $updates);
            if ($result === true) {
                $message = "Profile updated successfully.";
                $_SESSION['username'] = $updates['username'];
                $user = User::findByUsername($updates['username']);
            } elseif (is_string($result)) {
                $message = $result;
            } else {
                $message = "Failed to update profile.";
            }
        }
    }
    
    if (isset($_POST['update_password'])) {
        $new_password     = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Enforce minimum password requirements (reinforced on the server side)
        if (strlen($new_password) < 8 || 
            !preg_match('/[a-z]/', $new_password) || 
            !preg_match('/[A-Z]/', $new_password) || 
            !preg_match('/\d/', $new_password)) {
            $message = "Password must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.";
        }
        elseif ($new_password === $confirm_password) {
            if (User::updatePassword($_SESSION['user_id'], $new_password)) {
                $message = "Password updated successfully.";
            } else {
                $message = "Failed to update password.";
            }
        } else {
            $message = "Passwords do not match.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile - Camagru</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Using our own styles, without Bootstrap -->
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/clean_b.css">
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
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
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
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-3">
                        <label class="form-label">New Password:</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" 
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                               title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 8 or more characters"
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

    <?php include '../components/footer.php'; ?>
    <script src="./js/user.js"></script>
</body>
</html>