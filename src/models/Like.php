<?php
require_once __DIR__ . '/../config/database.php';

class Like {
    private $id;
    private $imageId;

    public function __construct($id, $imageId) {
        $this->id = $id;
        $this->imageId = $imageId;
    }

    public function getId() {
        return $this->id;
    }

    public function getImageId() {
        return $this->imageId;
    }


    public static function saveLike($userId, $imageId) {
        $pdo = getDatabaseConnection();

        $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND image_id = ?");
        $stmt->execute([$userId, $imageId]);
        if ($existing = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Quitar like existente
            $stmt = $pdo->prepare("DELETE FROM likes WHERE id = ?");
            return $stmt->execute([$existing['id']]);
        }
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, image_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $imageId]);
    }
    
    public static function getLikesCount($imageId) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE image_id = ?");
        $stmt->execute([$imageId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? intval($result['count']) : 0;
    }

}
?>