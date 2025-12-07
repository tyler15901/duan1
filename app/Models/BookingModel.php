<?php
require_once '../app/Core/Model.php';

class BookingModel extends Model {

    // 1. Lấy danh sách đơn hàng (Có phân trang & Lọc)
    public function getAllBookings($filters = [], $limit = 10, $offset = 0) {
        $sql = "SELECT b.*, kh.HoTen as TenKhach, kh.SoDienThoai, t.TenTour, lkh.LichCode 
                FROM booking b
                JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang
                LEFT JOIN lichkhoihanh lkh ON b.MaLichKhoiHanh = lkh.MaLichKhoiHanh 
                LEFT JOIN tour t ON lkh.MaTour = t.MaTour 
                WHERE 1=1";

        $params = [];
        
        if (!empty($filters['keyword'])) {
            $sql .= " AND (b.MaBookingCode LIKE :kw OR kh.HoTen LIKE :kw OR kh.SoDienThoai LIKE :kw)";
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

        $sql .= " ORDER BY b.NgayDat DESC LIMIT $limit OFFSET $offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số lượng (để phân trang)
    public function countBookings($filters = []) {
        $sql = "SELECT COUNT(*) as total 
                FROM booking b 
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

    // --- [QUAN TRỌNG] LẤY LỊCH THEO TOUR ĐỂ ĐỔ VÀO FORM ---
    public function getSchedulesByTour($tourId) {
        // [ĐÃ SỬA LỖI] Lấy các cột SoChoToiDa, GiaNguoiLon, GiaTreEm từ bảng lichkhoihanh
        $sql = "SELECT l.MaLichKhoiHanh, l.LichCode, l.NgayKhoiHanh, l.SoKhachHienTai, 
                       l.SoChoToiDa,      -- Cột này quan trọng
                       l.GiaNguoiLon,     -- Cột này quan trọng
                       l.GiaTreEm         -- Cột này quan trọng
                FROM lichkhoihanh l
                WHERE l.MaTour = ? 
                AND l.TrangThai IN ('Nhận khách', 'Đang chuẩn bị', 'Đang chạy')
                ORDER BY l.NgayKhoiHanh ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tourId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Lấy danh sách khách đi kèm của 1 booking
    public function getGuestList($bookingId) {
        $sql = "SELECT ct.*, kh.HoTen, kh.SoDienThoai
                FROM chitietkhachbooking ct
                JOIN khachhang kh ON ct.MaKhachHang = kh.MaKhachHang
                WHERE ct.MaBooking = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Tạo hoặc lấy thông tin khách hàng (Người đặt)
    public function getOrCreateCustomer($name, $phone, $email = '', $address = '') {
        // Kiểm tra xem SĐT đã tồn tại chưa
        $stmt = $this->conn->prepare("SELECT MaKhachHang FROM khachhang WHERE SoDienThoai = ? LIMIT 1");
        $stmt->execute([$phone]);
        $cust = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cust) {
            return $cust['MaKhachHang']; 
        } else {
            // Nếu chưa có thì tạo mới
            $sql = "INSERT INTO khachhang (HoTen, SoDienThoai, Email, DiaChi, NgayTao) VALUES (?, ?, ?, ?, NOW())";
            $stmtInsert = $this->conn->prepare($sql);
            $stmtInsert->execute([$name, $phone, $email, $address]);
            return $this->conn->lastInsertId();
        }
    }

    // 5. Tạo Booking mới và trả về ID
    public function createBookingReturnId($data) {
        $sql = "INSERT INTO booking (
                    MaTour, MaLichKhoiHanh, MaKhachHang, 
                    NgayKhoiHanh, SoLuongKhach, TongTien, TienCoc, 
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
            // Tạo mã booking code (VD: BK2025000123)
            $code = 'BK' . date('Y') . str_pad($newId, 6, '0', STR_PAD_LEFT);
            $this->conn->prepare("UPDATE booking SET MaBookingCode = ? WHERE MaBooking = ?")
                       ->execute([$code, $newId]);
            return $newId;
        }
        return false;
    }

    // 6. Thêm khách vào chi tiết booking (Transaction an toàn)
    public function addGuestToBooking($bookingId, $data) {
        try {
            $this->conn->beginTransaction();

            // B1: Lưu thông tin cá nhân khách vào bảng khachhang
            $sqlKhach = "INSERT INTO khachhang (HoTen, SoDienThoai, SoGiayTo, NgayTao) 
                         VALUES (:hoten, :sdt, :giayto, NOW())";
            
            $stmt1 = $this->conn->prepare($sqlKhach);
            $stmt1->execute([
                'hoten'  => $data['ho_ten'], 
                'sdt'    => $data['sdt'],
                'giayto' => $data['so_giay_to']
            ]);
            $newKhachId = $this->conn->lastInsertId();

            // B2: Liên kết khách đó vào booking trong bảng chitietkhachbooking
            $sqlDetail = "INSERT INTO chitietkhachbooking (MaBooking, MaKhachHang, LoaiKhach, SoGiayTo, GhiChu) 
                          VALUES (:bk, :kh, :loai, :giayto, :note)";
            
            $stmt2 = $this->conn->prepare($sqlDetail);
            $stmt2->execute([
                'bk'     => $bookingId,
                'kh'     => $newKhachId,
                'loai'   => $data['loai_khach'],
                'giayto' => $data['so_giay_to'],
                'note'   => $data['ghi_chu']
            ]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // 7. Cập nhật thông tin khách (Sửa)
    public function updateGuestInfo($guestDetailId, $data) {
        try {
            $this->conn->beginTransaction();

            // Lấy ID khách hàng gốc
            $stmtGet = $this->conn->prepare("SELECT MaKhachHang FROM chitietkhachbooking WHERE MaChiTiet = ?");
            $stmtGet->execute([$guestDetailId]);
            $res = $stmtGet->fetch(PDO::FETCH_ASSOC);
            $maKhachHang = $res['MaKhachHang'];

            if ($maKhachHang) {
                // Update bảng KhachHang
                $sqlKhach = "UPDATE khachhang SET HoTen = :ten, SoDienThoai = :sdt, SoGiayTo = :giayto WHERE MaKhachHang = :id";
                $this->conn->prepare($sqlKhach)->execute([
                    'ten'    => $data['ho_ten'],
                    'sdt'    => $data['sdt'],
                    'giayto' => $data['so_giay_to'],
                    'id'     => $maKhachHang
                ]);
            }

            // Update bảng ChiTiet
            $sqlChiTiet = "UPDATE chitietkhachbooking SET LoaiKhach = :loai, SoGiayTo = :giayto, GhiChu = :note WHERE MaChiTiet = :id";
            $this->conn->prepare($sqlChiTiet)->execute([
                'loai'   => $data['loai_khach'],
                'giayto' => $data['so_giay_to'],
                'note'   => $data['ghi_chu'],
                'id'     => $guestDetailId
            ]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // 8. Cập nhật thông tin đơn hàng (Status, Thanh toán)
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

    // 9. Xóa khách khỏi booking
    public function removeGuestFromBooking($maChiTiet) {
        $stmt = $this->conn->prepare("DELETE FROM chitietkhachbooking WHERE MaChiTiet = ?");
        return $stmt->execute([$maChiTiet]);
    }

    // Helper: Lấy danh sách tour hoạt động (cho dropdown)
    public function getActiveTours() {
        return $this->conn->query("SELECT MaTour, TenTour FROM tour WHERE TrangThai='Hoạt động'")->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>