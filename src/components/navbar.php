<?php
require_once __DIR__ . '/../controllers/SessionController.php';
SessionController::init();
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">
    <div class="nav-brand">
        <a href="index.php">Camagru</a>
    </div>
    <div class="nav-links">
        <a href="index.php" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">Gallery</a>
        <?php if (SessionController::isLoggedIn()): ?>
            <a href="user.php" class="<?php echo $current_page === 'user.php' ? 'active' : ''; ?>">Profile</a>
            <form action="auth.php" method="POST" style="display: inline;">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">Logout</button>
            </form>
        <?php else: ?>
            <a href="auth.php" class="<?php echo $current_page === 'auth.php' ? 'active' : ''; ?>">Login/Register</a>
        <?php endif; ?>
    </div>
</nav>

<!-- <nav class="navbar">
    <div class="nav-brand">
        <a href="index.php">Camagru</a>
    </div>
    <div class="nav-links">
        <a href="index.php" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">Gallery</a>
        <?php if (SessionController::isLoggedIn()): ?>
            <a href="user.php" class="<?php echo $current_page === 'user.php' ? 'active' : ''; ?>">Profile</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="auth.php" class="<?php echo $current_page === 'auth.php' ? 'active' : ''; ?>">Login/Register</a>
        <?php endif; ?>
    </div>
</nav> -->