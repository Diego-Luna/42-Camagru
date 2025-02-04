<?php
class Image {
    private $id;
    private $userId;
    private $imagePath;

    public function __construct($id, $userId, $imagePath) {
        $this->id = $id;
        $this->userId = $userId;
        $this->imagePath = $imagePath;
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function setImagePath($imagePath) {
        $this->imagePath = $imagePath;
    }

    public static function save($userId, $imagePath) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            INSERT INTO images (user_id, image_path, created_at) 
            VALUES (:user_id, :path, NOW())
        ");
        return $stmt->execute([
            ':user_id' => $userId,
            ':path' => $imagePath
        ]);
    }
    
    // Nuevo método para obtener imágenes por usuario
    public static function getByUser($userId) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            SELECT id, user_id, image_path 
            FROM images 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>