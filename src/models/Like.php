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
        // Code to save the like to the database
    }

    public function delete() {
        // Code to delete the like from the database
    }
}