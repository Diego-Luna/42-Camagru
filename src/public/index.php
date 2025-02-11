<?php
require_once '../controllers/sessionController.php';
SessionController::init();
require_once '../config/database.php';
require_once '../models/Image.php';

$pdo = getDatabaseConnection();

$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT id, user_id, image_path, created_at FROM images ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
  foreach ($images as $img):
    $loggedIn = isset($_SESSION['user_id']);
    echo '<div class="col-12 col-md-6 col-lg-4 mb-4">';
    include '../components/image_card.php';
    echo '</div>';
  endforeach;
  exit;
}

$loggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Camagru - Gallery</title>
  <link rel="stylesheet" href="./css/styles.css">
  <link rel="stylesheet" href="./css/clean_b.css">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
</head>
<body class="bg-light">
  <?php include '../components/navbar.php'; ?>
  <div class="container py-4">
    <h2 class="display-5 text-center mb-4">Gallery</h2>
    <!-- Grid of Images -->
    <div id="gallery-grid" class="row">
      <?php foreach ($images as $img): ?>
        <div class="col-12 col-md-6 col-lg-4 mb-4">
          <?php include '../components/image_card.php'; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <!-- Loading Indicator -->
    <div id="loading" class="d-none text-center my-3">
      <span class="text-muted">Loading more images...</span>
    </div>
  </div>
  

    <script src="./js/gallery.js"></script>
    <script src="./js/comments.js"></script>
</body>
</html>