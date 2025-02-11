<?php
ob_start();

require_once __DIR__ . '/../../controllers/sessionController.php';
require_once __DIR__ . '/../../models/Image.php';
require_once __DIR__ . '/../../config/database.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

SessionController::requireLogin();
header('Content-Type: application/json');

try {
    if (!isset($_FILES['image']) || !isset($_POST['stickers'])) {
        throw new Exception('Missing image or stickers');
    }

    // 1. Comprobar si ocurrió un error en la subida del archivo
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'El archivo excede upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE  => 'El archivo excede MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL    => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE    => 'No se subió ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'No existe carpeta temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco',
            UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP detuvo la subida'
        ];
        $code = $_FILES['image']['error'];
        $message = $uploadErrors[$code] ?? 'Error desconocido en la subida';
        throw new Exception("File upload error: " . $message);
    }

    // Definir directorio absoluto en el servidor (en base a __DIR__)
    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        if (!file_exists($uploadDir)) {
            throw new Exception('Develop: Failed to create upload directory');
        }
    }

    $filename = uniqid() . '.png';
    $uploadFile = $uploadDir . $filename;
    // Construir la ruta relativa para guardar en la base de datos.
    // Recordar que __DIR__ es "src/public/controllers", por lo que la ruta relativa es "/controllers/uploads/filename"
    $relativePath = '/controllers/uploads/' . $filename;

    // 2. Verificar que el archivo temporal existe y es un archivo subido correctamente
    if (!is_uploaded_file($_FILES['image']['tmp_name'])) {
        throw new Exception('El archivo temporal no existe o no es un archivo subido mediante HTTP POST');
    }

    // 3. Comprobar que la ruta destino es válida y que se tienen permisos de escritura
    if (!is_writable($uploadDir)) {
        throw new Exception('El directorio de subida no es escribible : ' . $uploadDir);
    }

    // 4. Intentar mover el archivo
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        throw new Exception('Develop: Failed to upload file');
    }

    // Guardar en la base de datos la ruta relativa (para que la URL sea accesible desde el navegador)
    $success = Image::save($_SESSION['user_id'], $relativePath);
    
    // Limpiar cualquier salida antes de emitir el JSON
    ob_clean();
    echo json_encode([
        'success' => $success,
        'path' => $relativePath
    ]);

} catch (Exception $e) {
    error_log($e->getMessage());
    ob_clean();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>