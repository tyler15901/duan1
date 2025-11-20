<?php
namespace App\Controllers;

require_once __DIR__ . '/../models/TourModel.php';
require_once __DIR__ . '/../models/TourImageModel.php';
require_once __DIR__ . '/../models/TourPriceModel.php';

class FrontendController {
    private $tourModel;
    private $tourImageModel;
    private $tourPriceModel;

    public function __construct() {
        $this->tourModel = new TourModel();
        $this->tourImageModel = new TourImageModel();
        $this->tourPriceModel = new TourPriceModel();
    }

    /**
     * Trang chủ
     */
    public function index() {
        // Lấy tour ưu đãi (có thể filter theo giá hoặc trạng thái)
        $saleTours = $this->tourModel->getFeaturedTours('sale', 6);
        $domesticTours = $this->tourModel->getToursByType(1, 6); // Tour trong nước
        $internationalTours = $this->tourModel->getToursByType(2, 6); // Tour quốc tế
        
        // Lấy ảnh cho từng tour
        foreach ($saleTours as &$tour) {
            $tour['images'] = $this->tourImageModel->getByTourId($tour['MaTour']);
        }
        foreach ($domesticTours as &$tour) {
            $tour['images'] = $this->tourImageModel->getByTourId($tour['MaTour']);
        }
        foreach ($internationalTours as &$tour) {
            $tour['images'] = $this->tourImageModel->getByTourId($tour['MaTour']);
        }

        require __DIR__ . '/../../views/frontend/index.php';
    }

    /**
     * Danh sách tour
     */
    public function list() {
        $type = $_GET['type'] ?? null; // 1: trong nước, 2: quốc tế
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'recommended';
        $page = $_GET['page'] ?? 1;
        $perPage = 12;

        // Lấy danh sách tour
        if ($type) {
            $tours = $this->tourModel->getToursByType($type);
        } else {
            $tours = $this->tourModel->getAll('Hoạt động');
        }

        // Lọc theo search
        if ($search) {
            $tours = array_filter($tours, function($tour) use ($search) {
                return stripos($tour['TenTour'], $search) !== false || 
                       stripos($tour['MoTa'], $search) !== false;
            });
        }

        // Sắp xếp
        $tours = $this->sortTours($tours, $sort);

        // Phân trang
        $total = count($tours);
        $totalPages = ceil($total / $perPage);
        $tours = array_slice($tours, ($page - 1) * $perPage, $perPage);

        // Lấy ảnh cho từng tour
        foreach ($tours as &$tour) {
            $tour['images'] = $this->tourImageModel->getByTourId($tour['MaTour']);
            $tour['price'] = $this->tourPriceModel->getCurrentPrice($tour['MaTour']);
        }

        require __DIR__ . '/../../views/frontend/list.php';
    }

    /**
     * Chi tiết tour
     */
    public function detail() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $tour = $this->tourModel->getById($id);
        if (!$tour) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Lấy ảnh tour
        $tour['images'] = $this->tourImageModel->getByTourId($id);
        
        // Lấy giá tour
        $tour['price'] = $this->tourPriceModel->getCurrentPrice($id);
        
        // Lấy lịch trình tour
        $tour['itinerary'] = $this->tourModel->getItinerary($id);
        
        // Lấy lịch khởi hành
        $tour['schedules'] = $this->tourModel->getSchedules($id);
        
        // Lấy tour liên quan
        $relatedTours = $this->tourModel->getRelatedTours($id, $tour['MaLoaiTour'], 4);
        foreach ($relatedTours as &$relatedTour) {
            $relatedTour['images'] = $this->tourImageModel->getByTourId($relatedTour['MaTour']);
        }

        require __DIR__ . '/../../views/frontend/detail.php';
    }

    /**
     * Sắp xếp tours
     */
    private function sortTours($tours, $sort) {
        switch ($sort) {
            case 'price-asc':
                usort($tours, function($a, $b) {
                    $priceA = $this->tourPriceModel->getCurrentPrice($a['MaTour']);
                    $priceB = $this->tourPriceModel->getCurrentPrice($b['MaTour']);
                    return ($priceA['GiaNguoiLon'] ?? 0) <=> ($priceB['GiaNguoiLon'] ?? 0);
                });
                break;
            case 'price-desc':
                usort($tours, function($a, $b) {
                    $priceA = $this->tourPriceModel->getCurrentPrice($a['MaTour']);
                    $priceB = $this->tourPriceModel->getCurrentPrice($b['MaTour']);
                    return ($priceB['GiaNguoiLon'] ?? 0) <=> ($priceA['GiaNguoiLon'] ?? 0);
                });
                break;
            case 'duration':
                usort($tours, function($a, $b) {
                    return $a['SoNgay'] <=> $b['SoNgay'];
                });
                break;
        }
        return $tours;
    }
}

