<?php
class HomeController extends Controller {
    
    public function index() {
        $tourModel = $this->model('TourModel');

        // 1. Lấy dữ liệu thô từ Model
        $rawUuDai = $tourModel->getFeaturedTours(4);
        $rawTrongNuoc = $tourModel->getToursByType(1, 8);
        $rawQuocTe = $tourModel->getToursByType(2, 8);

        // 2. Chuẩn hóa dữ liệu (Format tiền, Ảnh) ngay tại Controller
        // View sẽ không phải làm việc này nữa
        $data = [
            'tourUuDai'     => $this->formatTours($rawUuDai),
            'tourTrongNuoc' => $this->formatTours($rawTrongNuoc),
            'tourQuocTe'    => $this->formatTours($rawQuocTe),
            'title'         => 'Du Lịch Việt - Trang Chủ',
        ];

        $this->view('home/index', $data);
    }

    /**
     * Hàm phụ: Xử lý dữ liệu tour trước khi gửi sang View
     * (Logic này nằm ở Controller là chuẩn MVC)
     */
    private function formatTours($tours) {
        foreach ($tours as &$tour) {
            // 1. Xử lý ảnh: Tạo đường dẫn đầy đủ
            $imgName = $tour['HinhAnh'] ?? '';
            $tour['final_image'] = BASE_URL . 'uploads/' . $imgName;

            // 2. Xử lý giá: Format sang VND
            if (!empty($tour['GiaHienTai']) && $tour['GiaHienTai'] > 0) {
                $tour['final_price'] = number_format($tour['GiaHienTai'], 0, ',', '.') . ' đ';
            } else {
                $tour['final_price'] = 'Liên hệ';
            }

            // 3. Xử lý Link chi tiết
            $tour['detail_link'] = BASE_URL . 'tour/detail/' . $tour['MaTour'];
            
            // 4. Xử lý nhãn (Nếu chưa có thì mặc định là Hot)
            $tour['label'] = $tour['TenLoai'] ?? 'Hot';
        }
        return $tours;
    }
}