<?php
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../models/ReportModel.php';

class ReportController {
    private $reportModel;

    public function __construct() {
        $this->reportModel = new ReportModel();
    }

    /**
     * Revenue report
     */
    public function revenue() {
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? null;
        $quarter = $_GET['quarter'] ?? null;

        $revenueData = $this->reportModel->getRevenueByTime($year, $month, $quarter);
        $totalRevenue = $this->reportModel->getTotalRevenue();
        $totalBookings = $this->reportModel->getTotalBookings();

        require __DIR__ . '/../../views/report/revenue.php';
    }
}

