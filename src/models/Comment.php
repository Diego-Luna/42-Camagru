<?php

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
}

?>