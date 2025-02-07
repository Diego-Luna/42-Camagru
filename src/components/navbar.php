<?php
require_once __DIR__ . '/../controllers/SessionController.php';
SessionController::init();
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Camagru</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="index.php">Gallery</a>
                </li>
                <?php if (SessionController::isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'user.php' ? 'active' : ''; ?>" href="user.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'create_Img.php' ? 'active' : ''; ?>" href="create_Img.php">Create Image</a>
                    </li>
                    <li class="nav-item">
                        <form action="auth.php" method="POST" class="d-inline">
                            <input type="hidden" name="action" value="logout">
                            <button type="submit" class="btn btn-link nav-link">Logout</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'auth.php' ? 'active' : ''; ?>" href="auth.php">Login/Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="./js/navbar.js"></script>