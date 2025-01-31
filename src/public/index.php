<?php
require_once '../models/User.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <form action="controllers/AuthController.php" method="POST" class="registration-form">
            <h2>Create Account</h2>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required 
                       pattern="[A-Za-z0-9]{3,}" title="3 or more letters/numbers">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required 
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                       title="Must contain numbers, uppercase and lowercase letters">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" name="register">Register</button>
            
            <p class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </form>
    </div>
</body>
</html>