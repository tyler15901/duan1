<?php
require_once '../app/Core/Model.php';

class BookingModel extends Model {

    // 1. Lấy danh sách đơn hàng (Kèm thông tin Khách, Tour, Lịch)
    public function getAllBookings($filters = [], $limit = 10, $offset = 0) {
        $sql = "SELECT b.*, kh.HoTen as TenKhach, kh.SoDienThoai, t.TenTour, lkh.LichCode 
                FROM booking b
                JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang
                JOIN lichkhoihanh lkh ON b.MaLichKhoiHanh = lkh.MaLichKhoiHanh
                JOIN tour t ON lkh.MaTour = t.MaTour
                WHERE 1=1";

        $params = [];
        
        // Lọc theo mã đơn hoặc tên khách/sđt
        if (!empty($filters['keyword'])) {
            $sql .= " AND (b.MaBookingCode LIKE :kw OR kh.HoTen LIKE :kw OR kh.SoDienThoai LIKE :kw)";
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }

        $sql .= " ORDER BY b.NgayDat DESC LIMIT $limit OFFSET $offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng để phân trang
    public function countBookings($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM booking b 
                JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang 
                WHERE 1=1";
        $params = [];
        if (!empty($filters['keyword'])) {
            $sql .= " AND (b.MaBookingCode LIKE :kw OR kh.HoTen LIKE :kw)";
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['total'];
    }

    // 2. Lấy chi tiết 1 đơn hàng
    public function getBookingById($id) {
        $sql = "SELECT b.*, kh.HoTen as TenKhach, kh.SoDienThoai, kh.Email, kh.DiaChi,
                       t.TenTour, t.SoNgay, lkh.LichCode, lkh.NgayKhoiHanh, lkh.MaLichKhoiHanh
                FROM booking b
                JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang
                JOIN lichkhoihanh lkh ON b.MaLichKhoiHanh = lkh.MaLichKhoiHanh
                JOIN tour t ON lkh.MaTour = t.MaTour
                WHERE b.MaBooking = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Lấy danh sách khách đi kèm (Đã sửa để lấy thêm Họ Tên)
    public function getGuestList($bookingId) {
        // Sử dụng LEFT JOIN để lấy tên từ bảng khachhang dựa vào MaKhachHang
        $sql = "SELECT ct.*, kh.HoTen, kh.SoGiayTo as CCCD_Goc
                FROM chitietkhachbooking ct
                LEFT JOIN khachhang kh ON ct.MaKhachHang = kh.MaKhachHang
                WHERE ct.MaBooking = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Cập nhật trạng thái & File
    public function updateBooking($id, $data) {
        $sql = "UPDATE booking SET TrangThai = :status, TrangThaiThanhToan = :payment_status";
        
        // Nếu có upload file mới thì cập nhật, không thì thôi
        if (!empty($data['file'])) {
            $sql .= ", FileDanhSachKhach = :file";
        }
        
        $sql .= " WHERE MaBooking = :id";
        
        $params = [
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'id' => $id
        ];
        
        if (!empty($data['file'])) {
            $params['file'] = $data['file'];
        }

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    // --- CÁC HÀM HỖ TRỢ TẠO ĐƠN ---

    // Lấy Tour đang hoạt động
    public function getActiveTours() {
        return $this->conn->query("SELECT MaTour, TenTour FROM tour WHERE TrangThai='Hoạt động'")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy Lịch theo Tour (Dùng cho AJAX)
   public function getSchedulesByTour($tourId) {
        
        $sql = "SELECT l.MaLichKhoiHanh, l.LichCode, l.NgayKhoiHanh, l.SoKhachHienTai, t.SoChoToiDa 
                FROM lichkhoihanh l
                JOIN tour t ON l.MaTour = t.MaTour
                WHERE l.MaTour = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tourId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Kiểm tra và lấy ID khách hàng (Nếu chưa có thì tạo mới)
    public function getOrCreateCustomer($name, $phone) {
        // 1. Tìm xem SĐT đã có chưa
        $stmt = $this->conn->prepare("SELECT MaKhachHang FROM khachhang WHERE SoDienThoai = ? LIMIT 1");
        $stmt->execute([$phone]);
        $cust = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cust) {
            return $cust['MaKhachHang']; // Đã có -> Trả về ID
        } else {
            // 2. Chưa có -> Tạo mới
            $sql = "INSERT INTO khachhang (HoTen, SoDienThoai, NgayTao) VALUES (?, ?, NOW())";
            $stmtInsert = $this->conn->prepare($sql);
            $stmtInsert->execute([$name, $phone]);
            return $this->conn->lastInsertId(); // Trả về ID mới
        }
    }

    // Tạo Booking mới
    public function createBooking($data) {
        // Mã Booking Code sẽ được tạo tự động bằng PHP thay vì Trigger để an toàn hơn
        // Tuy nhiên ở bài trước đã fix Trigger rồi nên ta cứ insert bình thường để Trigger làm việc
        
        $sql = "INSERT INTO booking (MaTour, MaLichKhoiHanh, MaKhachHang, SoLuongKhach, TongTien, TienCoc, TrangThai, TrangThaiThanhToan, NgayDat, GhiChu) 
                VALUES (:tour, :lich, :khach, :sl, :tien, :coc, 'Đã xác nhận', :tt_thanhtoan, NOW(), :ghichu)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }
}
?>