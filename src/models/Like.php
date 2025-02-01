<?php
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

    public function save() {
    }

    public function delete() {
    }
}

?>