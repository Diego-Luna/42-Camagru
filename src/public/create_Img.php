<?php
require_once '../config/database.php';
require_once '../controllers/AuthController.php';
require_once '../controllers/sessionController.php';
require_once '../models/Image.php';

// CSP disallows inline scripts, so no inline <script> allowed
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self';");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

SessionController::init();
SessionController::requireLogin();

$userId = $_SESSION['user_id'];
$images = Image::getByUser($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Camagru - Create Image</title>
  <link rel="stylesheet" href="./css/clean_b.css">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"> -->
  <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="stylesheet" href="./css/create_img.css">
    
</head>
<body class="bg-light">
    <?php include '../components/navbar.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Main Content - Left Column -->
            <div class="col-md-8 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <h2 class="h4 mb-0">Create New Image</h2>
                    </div>
                    <div class="card-body">
                        <!-- Preview Area -->
                        <div class="preview-container bg-dark rounded mb-3 d-flex align-items-center justify-content-center">
                            <div id="preview" class="w-100"></div>
                            <canvas id="canvas" class="d-none"></canvas>
                            <video id="video" autoplay playsinline></video>
                        </div>

                        <!-- Controls -->
                        <div class="d-flex gap-2 justify-content-center mb-3">
                            <button id="toggleMode" class="btn btn-outline-secondary">Use Upload</button>
                            <button id="captureBtn" disabled class="btn btn-outline-primary">Take Photo</button>
                            <input type="file" id="fileInput" accept="image/*" class="d-none" />
                        </div>
                        
                        <button id="saveBtn" disabled class="btn btn-success w-100">Create Image</button>
                        
                        <!-- Instructions -->
                        <div class="alert alert-info mt-3 mb-0">
                            <h6 class="alert-heading">Instructions:</h6>
                            <ol class="mb-0 small">
                                <li>Select a sticker (required)</li>
                                <li>Use webcam or upload an image</li>
                                <li>Take/Select your photo</li>
                                <li>Drag sticker to position</li>
                                <li>Click Create Image to save</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Right Column -->
            <div class="col-md-4">
                <!-- Sticker Selection -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h3 class="h5 mb-0">Select Sticker</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <img src="stickers/cat_1.png"
                                     class="sticker img-thumbnail rounded w-100"
                                     data-src="stickers/cat_1.png"
                                     alt="Cat sticker">
                            </div>
                            <div class="col-6">
                                <img src="stickers/dog_1.png"
                                     class="sticker img-thumbnail rounded w-100"
                                     data-src="stickers/dog_1.png"
                                     alt="Dog sticker">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Previous Images -->
                <div class="card shadow">
                    <div class="card-header">
                        <h3 class="h5 mb-0">My Images</h3>
                    </div>
                    <div class="card-body p-2">
                        <?php if (!empty($images)): ?>
                            <div class="row g-2 thumbnail-grid">
                                <?php foreach($images as $img): ?>
                                    <div class="col-6">
                                        <div class="position-relative">
                                            <img src="<?php echo htmlspecialchars($img['image_path'], ENT_QUOTES, 'UTF-8'); ?>"
                                                 class="img-thumbnail w-100"
                                                 alt="Created Image">
                                            <form action="controllers/delete_image.php"
                                                  method="POST"
                                                  class="confirm-delete-form position-absolute bottom-0 start-0 end-0 m-2">
                                                <input type="hidden" name="image_id"
                                                       value="<?php echo htmlspecialchars($img['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No images created yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./js/photo.js"></script>
    <?php include '../components/footer.php'; ?>
</body>
</html>