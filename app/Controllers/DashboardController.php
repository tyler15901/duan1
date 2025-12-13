<?php
class DashboardController extends Controller
{

    public function __construct()
    {
        require_once '../app/Core/Auth.php';
        Auth::checkAdmin();
    }

    public function index()
    {
        $scheduleModel = $this->model('ScheduleModel');
        if (method_exists($scheduleModel, 'autoUpdateTourStatus')) {
            $scheduleModel->autoUpdateTourStatus();
        }

        $tourModel = $this->model('TourModel');
        $bookingModel = $this->model('BookingModel');
        $reportModel = $this->model('ReportModel');

        $total_tours = $tourModel->countTours(['status' => 'Hoạt động']);
        $total_bookings = $bookingModel->countBookings([]);
        $new_orders = $bookingModel->countBookings(['keyword' => 'Chờ xác nhận']);

        $chartDataRaw = $reportModel->getRevenueByTime();
        $chartDataRaw = array_reverse($chartDataRaw);

        $chartLabels = [];
        $chartRevenue = [];
        $chartProfit = [];
        $total_revenue_year = 0;

        foreach ($chartDataRaw as $row) {
            $chartLabels[] = "T" . $row['Thang'] . "/" . $row['Nam'];
            $chartRevenue[] = $row['DoanhThu'];
            $chartProfit[] = $row['LoiNhuan'];
            $total_revenue_year += $row['DoanhThu'];
        }

        $recent_bookings = $bookingModel->getAllBookings([], 5, 0);
        $notifications = $bookingModel->getRecentNotifications();

        $data = [
            'total_tours' => $total_tours,
            'total_bookings' => $total_bookings,
            'new_orders' => $new_orders,
            'total_revenue_year' => $total_revenue_year,
            'recent_bookings' => $recent_bookings,
            'notifications' => $notifications,
            'chart_labels' => json_encode($chartLabels),
            'chart_revenue' => json_encode($chartRevenue),
            'chart_profit' => json_encode($chartProfit)
        ];

        $this->view('admin/dashboard/index', $data);
    }

    // --- [SỬA] Xử lý click vào thông báo (Chưa đánh dấu xem ngay, chỉ chuyển hướng) ---
    public function handle_notification($notiId)
    {
        $bookingModel = $this->model('BookingModel');

        // SỬA Ở ĐÂY: Gọi qua Model thay vì $this->db->prepare(...)
        $noti = $bookingModel->getNotificationLink($notiId);

        if ($noti) {
            $link = $noti['LienKet'];
            $separator = (strpos($link, '?') !== false) ? '&' : '?';
            $redirectUrl = BASE_URL . $link . $separator . 'noti_id=' . $notiId;
            
            header("Location: " . $redirectUrl);
            exit;
        } else {
            header("Location: " . BASE_URL . "/dashboard/index");
        }
    }

    // --- [MỚI] Xác nhận đã xử lý xong (Gọi từ trang Chi tiết Lịch) ---
    public function confirm_done($notiId) {
        $bookingModel = $this->model('BookingModel');
        $bookingModel->markNotificationAsRead($notiId);
        
        // Quay về Dashboard để thấy thông báo đã mất (hoặc ở lại trang tùy logic)
        echo "<script>alert('Đã xác nhận hoàn thành công việc!'); window.location.href='" . BASE_URL . "/dashboard/index';</script>";
    }
}
?>