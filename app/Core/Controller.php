<?php
class Controller {
    // Hàm gọi Model
    public function model($model) {
        require_once '../app/Models/' . $model . '.php';
        return new $model;
    }

    // Hàm gọi View (Giao diện)
    public function view($view, $data = []) {
        // Giải nén mảng data thành biến (VD: ['name'=>'A'] thành $name='A')
        extract($data);
        
        // Kiểm tra file view có tồn tại không
        if (file_exists('../app/Views/' . $view . '.php')) {
            require_once '../app/Views/' . $view . '.php';
        } else {
            die("View '$view' not found!");
        }
    }
}
?>