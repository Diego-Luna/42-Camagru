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

}

?>