<?php
class ReportController extends Controller {

    public function __construct() {
        require_once '../app/Core/Auth.php';
        Auth::checkAdmin();
    }

    public function index() {
        $model = $this->model('ReportModel');

        // Lấy tham số lọc thời gian
        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

        // Lấy dữ liệu bảng chi tiết
        $profitData = $model->getProfitBySchedule($month, $year);

        // Lấy dữ liệu biểu đồ
        $chartDataRaw = $model->getRevenueByTime();
        
        // Chuẩn bị JSON cho Chart.js
        $chartLabels = [];
        $chartRevenue = [];
        $chartProfit = [];
        foreach($chartDataRaw as $row) {
            $chartLabels[] = "Tháng " . $row['Thang'] . "/" . $row['Nam'];
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