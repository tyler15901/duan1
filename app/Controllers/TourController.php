<?php
class TourController extends Controller {
    
    /**
     * 1. HIỂN THỊ DANH SÁCH TOUR (Trang /tour/list)
     */
    public function list() {
        $tourModel = $this->model('TourModel');

        // 1. Lấy tham số từ URL
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $sort = $_GET['sort'] ?? 'newest';
        $search = $_GET['q'] ?? '';
        $perPage = 5; // Số tour mỗi trang

        // 2. Lấy dữ liệu từ Model
        $tours = $tourModel->getToursPaginated($page, $perPage, $sort, $search);
        $totalTours = $tourModel->countTotalTours($search);
        $totalPages = ceil($totalTours / $perPage);

        // 3. Xử lý hiển thị (Format tiền, Ảnh)
        foreach ($tours as &$t) {
            $t['final_price'] = (!empty($t['GiaHienTai']) && $t['GiaHienTai'] > 0) 
                                ? number_format($t['GiaHienTai'], 0, ',', '.') . ' ₫' 
                                : 'Liên hệ';
            $t['img_url'] = BASE_URL . 'uploads/' . ($t['HinhAnh'] ?? '');
            $t['detail_link'] = BASE_URL . 'tour/detail/' . $t['MaTour'];
        }

        // 4. Gửi sang View
        $this->view('tour/list', [
            'tours' => $tours,
            'totalTours' => $totalTours,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'sort' => $sort,
            'search' => $search,
            'title' => 'Danh Sách Tour - Du Lịch Việt'
        ]);
    }

    /**
     * 2. HIỂN THỊ CHI TIẾT TOUR (Trang /tour/detail/ID)
     */
    public function detail($id = null) {
        if (!$id) { header('Location: ' . BASE_URL); exit; }

        $tourModel = $this->model('TourModel');
        $tour = $tourModel->getTourDetail($id);
        
        if (!$tour) die("Tour không tồn tại!"); 

        // --- 1. XỬ LÝ DỮ LIỆU TOUR (Chuẩn bị cho View) ---
        // Format giá tiền
        $tour['formatted_price'] = (!empty($tour['GiaHienTai'])) ? number_format($tour['GiaHienTai'], 0, ',', '.') . ' đ' : 'Liên hệ';
        
        // Xử lý Gallery: Tạo mảng đường dẫn đầy đủ
        $tour['gallery_urls'] = [];
        if (!empty($tour['gallery'])) {
            foreach ($tour['gallery'] as $img) {
                $tour['gallery_urls'][] = BASE_URL . 'uploads/' . $img;
            }
        } else {
            // Nếu không có ảnh, dùng ảnh bìa
            $tour['gallery_urls'][] = BASE_URL . 'uploads/' . ($tour['HinhAnh'] ?? '');
        }
        // Ảnh chính đầu tiên
        $tour['main_image'] = $tour['gallery_urls'][0] ?? 'https://placehold.co/800x500';


        // --- 2. XỬ LÝ LỊCH KHỞI HÀNH ---
        $schedules = $tourModel->getSchedules($id);
        foreach ($schedules as &$lich) {
            // Format ngày đi (VD: 15/04)
            $lich['display_date'] = date('d/m', strtotime($lich['NgayKhoiHanh']));
            
            // Tính chỗ trống
            $conCho = $tour['SoChoToiDa'] - $lich['SoKhachHienTai'];
            $lich['remaining_seats'] = max(0, $conCho);
            $lich['is_full'] = ($conCho <= 0);
            $lich['class_status'] = ($conCho <= 0) ? 'disabled' : '';
        }

        // --- 3. XỬ LÝ TOUR LIÊN QUAN ---
        $relatedTours = $tourModel->getRelatedTours($id, $tour['MaLoaiTour'], 4);
        foreach ($relatedTours as &$r) {
            $r['img_url'] = BASE_URL . 'uploads/' . ($r['HinhAnh'] ?? '');
            $r['price_show'] = (!empty($r['GiaHienTai'])) ? number_format($r['GiaHienTai'], 0, ',', '.') . ' đ' : 'Liên hệ';
            $r['link'] = BASE_URL . 'tour/detail/' . $r['MaTour'];
        }

        // Lấy lịch trình (không cần xử lý nhiều)
        $itinerary = $tourModel->getItinerary($id);

        // Gửi sang View
        $this->view('tour/detail', [
            'tour' => $tour,
            'itinerary' => $itinerary,
            'schedules' => $schedules,
            'relatedTours' => $relatedTours,
            'title' => $tour['TenTour'] . ' - Chi tiết'
        ]);
    }
}