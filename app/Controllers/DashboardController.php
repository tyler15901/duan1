<?php
class DashboardController extends Controller {
    
    public function __construct() {
        require_once '../app/Core/Auth.php';
        Auth::checkAdmin();
    }

    public function index() {
        $tourModel = $this->model('TourModel');
        $bookingModel = $this->model('BookingModel');
        $reportModel = $this->model('ReportModel');

        // 1. Số liệu thẻ (Cards)
        $total_tours = $tourModel->countTours(['status' => 'Hoạt động']);
        $total_bookings = $bookingModel->countBookings([]);
        $new_orders = $bookingModel->countBookings(['keyword' => 'Chờ xác nhận']);

        // 2. Dữ liệu Biểu đồ
        $chartDataRaw = $reportModel->getRevenueByTime();
        // Đảo ngược mảng để tháng cũ bên trái, tháng mới bên phải
        $chartDataRaw = array_reverse($chartDataRaw); 

        $chartLabels = []; $chartRevenue = []; $chartProfit = [];
        $total_revenue_year = 0; // Tính tổng doanh thu năm để hiển thị

        foreach($chartDataRaw as $row) {
            $chartLabels[] = "T" . $row['Thang'] . "/" . $row['Nam'];
            $chartRevenue[] = $row['DoanhThu'];
            $chartProfit[] = $row['LoiNhuan'];
            $total_revenue_year += $row['DoanhThu'];
        }

        // 3. Đơn hàng mới nhất
        $recent_bookings = $bookingModel->getAllBookings([], 5, 0);

        $data = [
            'total_tours' => $total_tours,
            'total_bookings' => $total_bookings,
            'new_orders' => $new_orders,
            'total_revenue_year' => $total_revenue_year,
            'recent_bookings' => $recent_bookings,
            'chart_labels' => json_encode($chartLabels),
            'chart_revenue' => json_encode($chartRevenue),
            'chart_profit' => json_encode($chartProfit)
        ];

        $this->view('admin/dashboard/index', $data);
    }
}
?>