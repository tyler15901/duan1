<?php
require_once __DIR__ . '/../core/Session.php';

class HomeController {
    public function index() {
        // Check if user is logged in
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        require __DIR__ . '/../../views/home/dashboard.php';
    }
}

