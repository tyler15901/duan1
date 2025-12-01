<?php
require_once '../app/Core/Model.php';

class BookingModel extends Model {

    // 1. Lấy danh sách đơn hàng (Kèm thông tin Khách, Tour, Lịch)
    public function getAllBookings($filters = [], $limit = 10, $offset = 0) {
        $sql = "SELECT b.*, kh.HoTen as TenKhach, kh.SoDienThoai, t.TenTour, lkh.LichCode 
                FROM booking b
                JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang
                LEFT JOIN lichkhoihanh lkh ON b.MaLichKhoiHanh = lkh.MaLichKhoiHanh 
                LEFT JOIN tour t ON lkh.MaTour = t.MaTour 
                WHERE 1=1";

        $params = [];
        
        // Lọc theo mã đơn hoặc tên khách/sđt
        if (!empty($filters['keyword'])) {
            $sql .= " AND (b.MaBookingCode LIKE :kw OR kh.HoTen LIKE :kw OR kh.SoDienThoai LIKE :kw)";
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }

        // Lọc theo trạng thái xử lý
        if (!empty($filters['status'])) {
            $sql .= " AND b.TrangThai = :status";
            $params['status'] = $filters['status'];
        }

        // Lọc theo trạng thái thanh toán
        if (!empty($filters['payment_status'])) {
            $sql .= " AND b.TrangThaiThanhToan = :pay_status";
            $params['pay_status'] = $filters['payment_status'];
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
        if (!empty($filters['status'])) {
            $sql .= " AND b.TrangThai = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['payment_status'])) {
            $sql .= " AND b.TrangThaiThanhToan = :pay_status";
            $params['pay_status'] = $filters['payment_status'];
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

    // 3. Lấy danh sách khách đi kèm
    public function getGuestList($bookingId) {
        // JOIN bảng chitiet với bảng khachhang để lấy tên
        $sql = "SELECT ct.*, kh.HoTen
                FROM chitietkhachbooking ct
                LEFT JOIN khachhang kh ON ct.MaKhachHang = kh.MaKhachHang
                WHERE ct.MaBooking = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Cập nhật trạng thái, File & Tiền cọc
    public function updateBooking($id, $data) {
        $sql = "UPDATE booking SET 
                TrangThai = :status, 
                TrangThaiThanhToan = :payment_status,
                TienCoc = :tien_coc"; 
        
        if (!empty($data['file'])) {
            $sql .= ", FileDanhSachKhach = :file";
        }
        
        $sql .= " WHERE MaBooking = :id";
        
        $params = [
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'tien_coc' => $data['tien_coc'],
            'id' => $id
        ];
        
        if (!empty($data['file'])) {
            $params['file'] = $data['file'];
        }

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    // --- [MỚI] THÊM KHÁCH VÀO BOOKING (TRANSACTION) ---
    public function addGuestToBooking($bookingId, $data) {
        try {
            $this->conn->beginTransaction();

            // B1: Tạo khách hàng mới trong bảng `khachhang`
            // (Vì bảng chitietkhachbooking yêu cầu MaKhachHang)
            $sqlKhach = "INSERT INTO khachhang (HoTen, SoGiayTo, NgayTao) VALUES (:hoten, :giayto, NOW())";
            $stmt1 = $this->conn->prepare($sqlKhach);
            $stmt1->execute([
                'hoten' => $data['ho_ten'], 
                'giayto' => $data['so_giay_to']
            ]);
            $newKhachId = $this->conn->lastInsertId();

            // B2: Thêm vào bảng `chitietkhachbooking`
            $sqlDetail = "INSERT INTO chitietkhachbooking (MaBooking, MaKhachHang, LoaiKhach, SoGiayTo, GhiChu) 
                          VALUES (:bk, :kh, :loai, :giayto, :note)";
            $stmt2 = $this->conn->prepare($sqlDetail);
            $stmt2->execute([
                'bk' => $bookingId,
                'kh' => $newKhachId,
                'loai' => $data['loai_khach'],
                'giayto' => $data['so_giay_to'],
                'note' => $data['ghi_chu']
            ]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // --- [MỚI] XÓA KHÁCH KHỎI BOOKING ---
    public function removeGuestFromBooking($maChiTiet) {
        // Chỉ cần xóa trong bảng chi tiết, thông tin khách hàng gốc vẫn giữ để lịch sử (hoặc xóa tùy chính sách)
        $stmt = $this->conn->prepare("DELETE FROM chitietkhachbooking WHERE MaChiTiet = ?");
        return $stmt->execute([$maChiTiet]);
    }

    // --- CÁC HÀM HỖ TRỢ TẠO ĐƠN ---

    public function getActiveTours() {
        return $this->conn->query("SELECT MaTour, TenTour FROM tour WHERE TrangThai='Hoạt động'")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy Lịch theo Tour (Dùng cho AJAX)
   public function getSchedulesByTour($tourId) {
        // [CẬP NHẬT] Thêm GiaNguoiLon, GiaTreEm vào câu SELECT
        $sql = "SELECT l.MaLichKhoiHanh, l.LichCode, l.NgayKhoiHanh, l.SoKhachHienTai, 
                       t.SoChoToiDa, 
                       l.GiaNguoiLon, l.GiaTreEm 
                FROM lichkhoihanh l
                JOIN tour t ON l.MaTour = t.MaTour
                WHERE l.MaTour = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tourId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // [ĐÃ CẬP NHẬT] Thêm Email và Địa chỉ
    public function getOrCreateCustomer($name, $phone, $email = '', $address = '') {
        // 1. Tìm xem SĐT đã có chưa
        $stmt = $this->conn->prepare("SELECT MaKhachHang FROM khachhang WHERE SoDienThoai = ? LIMIT 1");
        $stmt->execute([$phone]);
        $cust = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cust) {
            return $cust['MaKhachHang']; 
        } else {
            // 2. Chưa có -> Tạo mới kèm Email, Địa chỉ
            $sql = "INSERT INTO khachhang (HoTen, SoDienThoai, Email, DiaChi, NgayTao) VALUES (?, ?, ?, ?, NOW())";
            $stmtInsert = $this->conn->prepare($sql);
            $stmtInsert->execute([$name, $phone, $email, $address]);
            return $this->conn->lastInsertId();
        }
    }

    public function createBooking($data) {
        $sql = "INSERT INTO booking (
                    MaTour, MaLichKhoiHanh, MaKhachHang, 
                    NgayKhoiHanh, 
                    SoLuongKhach, TongTien, TienCoc, 
                    TrangThai, TrangThaiThanhToan, NgayDat, GhiChu
                ) 
                VALUES (
                    :tour, :lich, :khach, 
                    (SELECT NgayKhoiHanh FROM lichkhoihanh WHERE MaLichKhoiHanh = :lich), 
                    :sl, :tien, :coc, 
                    'Đã xác nhận', :tt_thanhtoan, NOW(), :ghichu
                )";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt->execute($data)) {
            $newId = $this->conn->lastInsertId();
            // Mã Code: BK + Năm + ID (6 số)
            $code = 'BK' . date('Y') . str_pad($newId, 6, '0', STR_PAD_LEFT);
            
            $sqlUpdate = "UPDATE booking SET MaBookingCode = :code WHERE MaBooking = :id";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->execute(['code' => $code, 'id' => $newId]);
            
            return true;
        }
        return false;
    }

    
}
?>