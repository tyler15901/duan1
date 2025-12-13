<?php
class TourController extends Controller {

    public function __construct() {
        // Gọi hàm kiểm tra quyền ngay khi khởi tạo Controller
        require_once '../app/Core/Auth.php';
        Auth::checkAdmin();
    }

    // 1. Hàm hiển thị danh sách (URL: /tour/index)
    public function index() {
        $tourModel = $this->model('TourModel');
        
        // 1. Lấy tham số từ URL
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

        // 2. Cấu hình phân trang
        $limit = 5; // Số tour trên 1 trang
        $offset = ($page - 1) * $limit;

        // 3. Chuẩn bị bộ lọc
        $filters = [
            'category_id' => $cat_id,
            'keyword' => $keyword
        ];

        // 4. Gọi Model lấy dữ liệu
        $tours = $tourModel->getToursFiltered($filters, $limit, $offset);
        $total_records = $tourModel->countTours($filters);
        $total_pages = ceil($total_records / $limit);
        $categories = $tourModel->getCategories(); // Lấy danh mục để đổ vào dropdown

        // 5. Gửi dữ liệu sang View
        $data = [
            'tours' => $tours,
            'categories' => $categories,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'cat_id' => $cat_id, // Gửi lại để giữ trạng thái filter
                'keyword' => $keyword
            ]
        ];
        
        $this->view('admin/tours/index', $data);
    }

    // 2. Hàm hiển thị Form thêm mới (URL: /tour/create)
    public function create() {
        $tourModel = $this->model('TourModel');
        // Lấy danh mục để đổ vào thẻ select trong form
        $categories = $tourModel->getCategories();
        
        // Gọi view hiển thị form (file create.php mà bạn đã tạo)
        $this->view('admin/tours/create', ['categories' => $categories]);
    }

    // 3. Hàm xử lý khi bấm nút Lưu (URL: /tour/store)
    // Form action sẽ trỏ về đây: action=".../tour/store"
    // Xử lý khi bấm nút Lưu (URL: /tour/store)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tourModel = $this->model('TourModel');

            // 1. XỬ LÝ ẢNH ĐẠI DIỆN (Thumbnail)
            $thumbName = null;
            if (!empty($_FILES['hinhanh']['name'])) {
                // Tạo tên file ngẫu nhiên để tránh trùng
                $thumbName = time() . '_thumb_' . $_FILES['hinhanh']['name'];
                move_uploaded_file($_FILES['hinhanh']['tmp_name'], '../public/assets/uploads/' . $thumbName);
            }

            // 2. GOM DỮ LIỆU CƠ BẢN (Tour)
            // Lưu ý: Các key này phải khớp với tham số trong TourModel -> createTour
            $dataTour = [
                'TenTour'    => $_POST['ten_tour'],
                'MaLoaiTour' => $_POST['loai_tour'],
                'HinhAnh'    => $thumbName,
                'SoNgay'     => !empty($_POST['so_ngay']) ? $_POST['so_ngay'] : 1,
                'MoTa'       => $_POST['mo_ta'],
                'ChinhSach'  => isset($_POST['chinh_sach']) ? $_POST['chinh_sach'] : '',
                'TrangThai'  => 'Hoạt động'
            ];

            // 3. GỌI MODEL TẠO TOUR VÀ LẤY ID MỚI
            // Hàm createTour trong Model bắt buộc phải return $this->conn->lastInsertId();
            $newTourId = $tourModel->createTour($dataTour);

            if ($newTourId) {
                
                // A. LƯU LỊCH TRÌNH (Schedule)
                if (isset($_POST['schedule']) && is_array($_POST['schedule'])) {
                    foreach ($_POST['schedule'] as $index => $day) {
                        // Chỉ lưu nếu có tiêu đề
                        if(!empty($day['title'])) {
                            // index + 1 vì mảng bắt đầu từ 0 nhưng ngày bắt đầu từ 1
                            $tourModel->addSchedule($newTourId, $index + 1, $day['title'], $day['content']);
                        }
                    }
                }

                // B. LƯU GIÁ (QUAN TRỌNG: Xóa dấu chấm/phẩy)
                if (!empty($_POST['gia_nguoi_lon'])) {
                    // Biến '1.000.000' thành '1000000'
                    $priceAdult = str_replace(['.', ','], '', $_POST['gia_nguoi_lon']);
                    $tourModel->addPrice($newTourId, 'Người lớn', $priceAdult);
                }
                
                if (!empty($_POST['gia_tre_em'])) {
                    $priceChild = str_replace(['.', ','], '', $_POST['gia_tre_em']);
                    $tourModel->addPrice($newTourId, 'Trẻ em', $priceChild);
                }

                // C. LƯU GALLERY ẢNH (Album)
                if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                    $count = count($_FILES['gallery']['name']);
                    
                    for ($i = 0; $i < $count; $i++) {
                        // Kiểm tra lỗi upload (0 là không lỗi)
                        if ($_FILES['gallery']['error'][$i] == 0) {
                            $galName = time() . '_' . $i . '_' . $_FILES['gallery']['name'][$i];
                            
                            if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], '../public/assets/uploads/' . $galName)) {
                                // Gọi Model lưu tên ảnh vào bảng hinhanhtour
                                $tourModel->addGalleryImage($newTourId, $galName);
                            }
                        }
                    }
                }

                // Lưu thành công -> Chuyển hướng về trang danh sách
                header("Location: " . BASE_URL . "/tour/index");
                exit;
            } else {
                // Nếu createTour trả về false (Lỗi SQL)
                echo "<script>alert('Lỗi: Không thể tạo Tour. Vui lòng kiểm tra lại dữ liệu!'); window.history.back();</script>";
            }
        }
    }

    // 4. XEM CHI TIẾT (URL: /tour/show/1)
    public function show($id) {
        $tourModel = $this->model('TourModel');
        $data = [
            'tour' => $tourModel->getTourById($id),
            'gallery' => $tourModel->getGallery($id),
            'schedule' => $tourModel->getSchedule($id),
            'prices' => $tourModel->getPrices($id)
        ];
        $this->view('admin/tours/show', $data);
    }

    // 5. HIỂN THỊ FORM SỬA (URL: /tour/edit/1)
    public function edit($id) {
        $tourModel = $this->model('TourModel');
        $data = [
            'tour' => $tourModel->getTourById($id),
            'categories' => $tourModel->getCategories(),
            'schedule' => $tourModel->getSchedule($id),
            'prices' => $tourModel->getPrices($id) // Lấy giá để hiển thị lại nếu cần
        ];
        $this->view('admin/tours/edit', $data);
    }

    // 6. XỬ LÝ CẬP NHẬT (URL: /tour/update/1)
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tourModel = $this->model('TourModel');

            // --- XỬ LÝ ẢNH ĐẠI DIỆN MỚI (NẾU CÓ) ---
            $thumbName = "";
            if (!empty($_FILES['hinhanh']['name'])) {
                $thumbName = time() . '_' . $_FILES['hinhanh']['name'];
                move_uploaded_file($_FILES['hinhanh']['tmp_name'], '../public/assets/uploads/' . $thumbName);
            }

            // --- GOM DỮ LIỆU ---
            $dataTour = [
                'TenTour' => $_POST['ten_tour'],
                'MaLoaiTour' => $_POST['loai_tour'],
                'SoNgay' => $_POST['so_ngay'],
                'MoTa' => $_POST['mo_ta'],
                'ChinhSach' => $_POST['chinh_sach'],
                'TrangThai' => $_POST['trang_thai']
            ];
            if($thumbName) $dataTour['HinhAnh'] = $thumbName;

            // --- CẬP NHẬT TOUR ---
            if ($tourModel->updateTour($id, $dataTour)) {
                
                // A. Cập nhật Lịch trình (Xóa cũ - Thêm mới cho nhanh gọn)
                if (isset($_POST['schedule']) && is_array($_POST['schedule'])) {
                    $tourModel->resetSchedule($id); // Xóa hết cái cũ
                    foreach ($_POST['schedule'] as $index => $day) {
                        if(!empty($day['title'])) {
                            $tourModel->addSchedule($id, $index + 1, $day['title'], $day['content']);
                        }
                    }
                }

                // B. Thêm ảnh Gallery mới (Ảnh cũ vẫn giữ nguyên)
                if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                    $count = count($_FILES['gallery']['name']);
                    for ($i = 0; $i < $count; $i++) {
                        if ($_FILES['gallery']['error'][$i] == 0) {
                            $galName = time() . '_' . $i . '_' . $_FILES['gallery']['name'][$i];
                            move_uploaded_file($_FILES['gallery']['tmp_name'][$i], '../public/assets/uploads/' . $galName);
                            $tourModel->addGalleryImage($id, $galName);
                        }
                    }
                }
                
                // Cập nhật xong về trang chi tiết
                header("Location: " . BASE_URL . "/tour/show/" . $id);
            }
        }
    }

    public function trash() {
        $tourModel = $this->model('TourModel');
        $data = [
            'tours' => $tourModel->getTrashedTours()
        ];
        $this->view('admin/tours/trash', $data); // Tạo view này ở bước 3
    }

    // --- [THÊM MỚI] KHÔI PHỤC TOUR ---
    public function restore($id) {
        $tourModel = $this->model('TourModel');
        $tourModel->restoreTour($id);
        header("Location: " . BASE_URL . "/tour/trash");
    }

    // 7. XÓA TOUR (URL: /tour/delete/1)
    public function delete($id) {
        $tourModel = $this->model('TourModel');
        // Bây giờ nó là xóa mềm, nên không sợ lỗi khóa ngoại nữa
        if ($tourModel->deleteTour($id)) {
            echo "<script>alert('Đã chuyển Tour vào thùng rác!'); window.location.href='" . BASE_URL . "/tour/index';</script>";
        } else {
            echo "Lỗi hệ thống!";
        }
    }
}
?>