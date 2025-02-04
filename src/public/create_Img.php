<?php
require_once '../config/database.php';
require_once '../controllers/AuthController.php';
require_once '../controllers/SessionController.php';
require_once '../models/Image.php';
SessionController::init();

SessionController::requireLogin();

$userId = $_SESSION['user_id'];
// Asume que este método existe y devuelve un arreglo de imágenes del usuario
$images = Image::getByUser($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Camagru - Create Image</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-gray-100 font-sans">

  <?php include '../components/navbar.php'; ?>

  <div class="max-w-5xl mx-auto mt-8 p-6 bg-white shadow-md rounded">
    <h1 class="text-2xl font-bold text-center mb-6">Create Image</h1>

    <div class="flex flex-wrap gap-6">
      <!-- Creation Section -->
      <div class="flex-1 min-w-[300px] flex flex-col items-center">
        <video id="video" autoplay playsinline class="w-full max-w-md bg-black rounded shadow"></video>
        <canvas id="canvas" class="w-full max-w-md bg-black hidden rounded shadow"></canvas>

        <div class="flex gap-3 mt-4 flex-wrap justify-center">
          <button id="toggleMode"
                  class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">
            Use Upload
          </button>
          <button id="captureBtn" disabled
                  class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
            Take Photo
          </button>
          <input type="file" id="fileInput" accept="image/*" class="hidden" />
        </div>

        <div class="mt-4">
          <button id="saveBtn" disabled
                  class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
            Create Image
          </button>
        </div>

        <div id="preview" class="mt-4 w-full max-w-md"></div>
      </div>

      <!-- Sticker Selection Section -->
      <div class="flex-1 min-w-[200px] bg-gray-50 p-4 rounded overflow-auto max-h-[600px]">
        <h3 class="font-semibold mb-2">Select Sticker (Required)</h3>
        <div class="grid grid-cols-2 gap-3">
          <img src="stickers/cat_1.png" class="sticker w-20 h-20 object-contain border-2 border-transparent rounded cursor-pointer hover:border-blue-500" data-src="stickers/cat_1.png" />
          <img src="stickers/dog_1.png" class="sticker w-20 h-20 object-contain border-2 border-transparent rounded cursor-pointer hover:border-blue-500" data-src="stickers/dog_1.png" />
        </div>
      </div>
    </div>

    <!-- User Created Images Section -->
    <div class="mt-8">
      <h2 class="text-xl font-bold mb-4">My Created Images</h2>
      <?php if (!empty($images)): ?>
        <div class="grid grid-cols-3 gap-4">
          <?php foreach($images as $img): ?>
            <div class="border p-2 rounded">
              <img src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="Created Image" class="w-full object-cover rounded mb-2" />
              <form action="controllers/delete_image.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
                <input type="hidden" name="image_id" value="<?php echo htmlspecialchars($img['id']); ?>" />
                <button type="submit" class="w-full px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                  Delete
                </button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p>You have not created any images yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <script src="./js/photo.js"></script>
</body>
</html>
