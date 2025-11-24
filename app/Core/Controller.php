<?php
// app/Core/Controller.php
class Controller {
    // Hàm gọi Model
    public function model($model) {
        require_once '../app/Models/' . $model . '.php';
        return new $model;
    }

    // Hàm gọi View
    public function view($view, $data = []) {
        require_once '../app/Views/' . $view . '.php';
    }
}