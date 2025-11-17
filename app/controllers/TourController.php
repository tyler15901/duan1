<?php
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../models/TourModel.php';

class TourController {
    private $model;

    public function __construct() {
        $this->model = new TourModel();
    }

    public function index() {
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $tours = $this->model->getAll();
        require __DIR__ . '/../../views/tour/list.php';
    }

    public function create() {
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'MaLoaiTour' => $_POST['MaLoaiTour'] ?? null,
                'TenTour' => $_POST['TenTour'] ?? '',
                'MoTa' => $_POST['MoTa'] ?? '',
                'SoNgay' => $_POST['SoNgay'] ?? 1,
                'SoChoToiDa' => $_POST['SoChoToiDa'] ?? 40,
                'TrangThai' => $_POST['TrangThai'] ?? 'Hoạt động'
            ];

            if ($this->model->create($data)) {
                header('Location: ' . BASE_URL . 'tour');
                exit;
            }
        }

        $tourTypes = $this->model->getTourTypes();
        require __DIR__ . '/../../views/tour/create.php';
    }

    public function edit() {
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . 'tour');
            exit;
        }

        $tour = $this->model->getById($id);
        if (!$tour) {
            header('Location: ' . BASE_URL . 'tour');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'MaLoaiTour' => $_POST['MaLoaiTour'] ?? null,
                'TenTour' => $_POST['TenTour'] ?? '',
                'MoTa' => $_POST['MoTa'] ?? '',
                'SoNgay' => $_POST['SoNgay'] ?? 1,
                'SoChoToiDa' => $_POST['SoChoToiDa'] ?? 40,
                'TrangThai' => $_POST['TrangThai'] ?? 'Hoạt động'
            ];

            if ($this->model->update($id, $data)) {
                header('Location: ' . BASE_URL . 'tour');
                exit;
            }
        }

        $tourTypes = $this->model->getTourTypes();
        require __DIR__ . '/../../views/tour/edit.php';
    }
}