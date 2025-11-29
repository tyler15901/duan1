<?php
class ReportController extends Controller {

    public function __construct() {
        require_once '../app/Core/Auth.php';
        Auth::checkAdmin();
    }

    public function index() {
        $model = $this->model('ReportModel');

        // Lọc theo tháng/năm
        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

        // Lấy danh sách chi tiết từng tour
        $profitData = $model->getProfitBySchedule($month, $year);

        // Lấy dữ liệu biểu đồ tổng quan (vẫn hiển thị để so sánh)
        $chartDataRaw = $model->getRevenueByTime();
        $chartDataRaw = array_reverse($chartDataRaw);
        
        $chartLabels = []; $chartRevenue = []; $chartProfit = [];
        foreach($chartDataRaw as $row) {
            $chartLabels[] = "T" . $row['Thang'];
            $chartRevenue[] = $row['DoanhThu'];
            $chartProfit[] = $row['LoiNhuan'];
        }

        $data = [
            'profit_list' => $profitData,
            'current_month' => $month,
            'current_year' => $year,
            'chart_labels' => json_encode($chartLabels),
            'chart_revenue' => json_encode($chartRevenue),
            'chart_profit' => json_encode($chartProfit)
        ];

        $this->view('admin/reports/index', $data);
    }
}
?>