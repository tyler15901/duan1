<?php
require_once '../app/Core/Model.php';

class BookingModel extends Model {

    // 1. Lấy danh sách đơn hàng
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
        // Lấy thêm SoDienThoai từ bảng khachhang
        $sql = "SELECT ct.*, kh.HoTen, kh.SoDienThoai
                FROM chitietkhachbooking ct
                JOIN khachhang kh ON ct.MaKhachHang = kh.MaKhachHang
                WHERE ct.MaBooking = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Cập nhật đơn hàng (Status, File, Tiền cọc)
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

    // --- THÊM KHÁCH VÀO BOOKING (TRANSACTION) ---
    public function addGuestToBooking($bookingId, $data) {
        try {
            $this->conn->beginTransaction();

            // B1: Tạo khách hàng mới trong bảng `khachhang`
            $sqlKhach = "INSERT INTO khachhang (HoTen, SoDienThoai, SoGiayTo, NgayTao) 
                         VALUES (:hoten, :sdt, :giayto, NOW())";
            
            $stmt1 = $this->conn->prepare($sqlKhach);
            $stmt1->execute([
                'hoten'  => $data['ho_ten'], 
                'sdt'    => $data['sdt'],
                'giayto' => $data['so_giay_to']
            ]);
            $newKhachId = $this->conn->lastInsertId();

            // B2: Thêm vào bảng `chitietkhachbooking`
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

    // --- [MỚI] CẬP NHẬT THÔNG TIN KHÁCH HÀNG (TRANSACTION) ---
    public function updateGuestInfo($guestDetailId, $data) {
        try {
            $this->conn->beginTransaction();

            // 1. Lấy MaKhachHang gốc từ bảng chi tiết
            $stmtGet = $this->conn->prepare("SELECT MaKhachHang FROM chitietkhachbooking WHERE MaChiTiet = ?");
            $stmtGet->execute([$guestDetailId]);
            $res = $stmtGet->fetch(PDO::FETCH_ASSOC);
            $maKhachHang = $res['MaKhachHang'];

            if ($maKhachHang) {
                // 2. Cập nhật bảng KHACHHANG (Tên, SĐT)
                $sqlKhach = "UPDATE khachhang SET HoTen = :ten, SoDienThoai = :sdt, SoGiayTo = :giayto WHERE MaKhachHang = :id";
                $stmt1 = $this->conn->prepare($sqlKhach);
                $stmt1->execute([
                    'ten'    => $data['ho_ten'],
                    'sdt'    => $data['sdt'],
                    'giayto' => $data['so_giay_to'], // Cập nhật cả CCCD ở đây cho đồng bộ
                    'id'     => $maKhachHang
                ]);
            }

            // 3. Cập nhật bảng CHITIET (Loại, Ghi chú, Giấy tờ)
            $sqlChiTiet = "UPDATE chitietkhachbooking SET LoaiKhach = :loai, SoGiayTo = :giayto, GhiChu = :note WHERE MaChiTiet = :id";
            $stmt2 = $this->conn->prepare($sqlChiTiet);
            $stmt2->execute([
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

    // --- XÓA KHÁCH KHỎI BOOKING ---
    public function removeGuestFromBooking($maChiTiet) {
        $stmt = $this->conn->prepare("DELETE FROM chitietkhachbooking WHERE MaChiTiet = ?");
        return $stmt->execute([$maChiTiet]);
    }

    // --- CÁC HÀM HỖ TRỢ KHÁC ---

    public function getActiveTours() {
        return $this->conn->query("SELECT MaTour, TenTour FROM tour WHERE TrangThai='Hoạt động'")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSchedulesByTour($tourId) {
        $sql = "SELECT l.MaLichKhoiHanh, l.LichCode, l.NgayKhoiHanh, l.SoKhachHienTai, 
                       t.SoChoToiDa, l.GiaNguoiLon, l.GiaTreEm 
                FROM lichkhoihanh l
                JOIN tour t ON l.MaTour = t.MaTour
                WHERE l.MaTour = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tourId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrCreateCustomer($name, $phone, $email = '', $address = '') {
        $stmt = $this->conn->prepare("SELECT MaKhachHang FROM khachhang WHERE SoDienThoai = ? LIMIT 1");
        $stmt->execute([$phone]);
        $cust = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cust) {
            return $cust['MaKhachHang']; 
        } else {
            $sql = "INSERT INTO khachhang (HoTen, SoDienThoai, Email, DiaChi, NgayTao) VALUES (?, ?, ?, ?, NOW())";
            $stmtInsert = $this->conn->prepare($sql);
            $stmtInsert->execute([$name, $phone, $email, $address]);
            return $this->conn->lastInsertId();
        }
    }

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
            $code = 'BK' . date('Y') . str_pad($newId, 6, '0', STR_PAD_LEFT);
            $this->conn->prepare("UPDATE booking SET MaBookingCode = ? WHERE MaBooking = ?")
                       ->execute([$code, $newId]);
            return $newId;
        }
        return false;
    }
}
?>