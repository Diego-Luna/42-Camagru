<?php
require_once __DIR__ . '/../config/database.php';

class Comment {
    private $id;
    private $imageId;
    private $content;

    public function __construct($id, $imageId, $content) {
        $this->id = $id;
        $this->imageId = $imageId;
        $this->content = $content;
    }

    public function getId() {
        return $this->id;
    }

    public function getImageId() {
        return $this->imageId;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }
    
    public static function saveComment($userId, $imageId, $content) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("INSERT INTO comments (image_id, user_id, content) VALUES (?, ?, ?)");
        return $stmt->execute([$imageId, $userId, $content]);
    }
    
    public static function getComments($imageId) {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.image_id = ? ORDER BY c.created_at ASC");
        $stmt->execute([$imageId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>