<?php
require_once 'models/AlatModel.php';

class AlatController {
    private $model;

    public function __construct($db) {
        $this->model = new AlatModel($db);
    }

    public function index() {
        $data = $this->model->getAllAlat();
        require 'views/alat/index.php';
    }

   
}


?>

