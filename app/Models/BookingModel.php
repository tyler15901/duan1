<?php
class BookingModel extends BaseModel {
    
    // 1. Lấy thông tin Lịch khởi hành + Tour + Giá (Để hiển thị lại ở trang xác nhận)
    public function getBookingInfo($lichId) {
        $sql = "SELECT lkh.*, t.TenTour, t.SoNgay, t.HinhAnh,
                       (SELECT Gia FROM giatour WHERE MaTour = t.MaTour AND DoiTuong = 'Người lớn' LIMIT 1) as GiaNguoiLon
                FROM lichkhoihanh lkh
                JOIN tour t ON lkh.MaTour = t.MaTour
                WHERE lkh.MaLichKhoiHanh = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $lichId]);
        return $stmt->fetch();
    }

    // 2. Thêm Khách Hàng Mới
    public function addCustomer($data) {
        $sql = "INSERT INTO khachhang (HoTen, Email, SoDienThoai, DiaChi) 
                VALUES (:ten, :email, :sdt, :diachi)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'ten' => $data['fullname'],
            'email' => $data['email'],
            'sdt' => $data['phone'],
            'diachi' => $data['address']
        ]);
        return $this->conn->lastInsertId(); // Trả về ID khách vừa tạo
    }

    // 3. Tạo Booking
    public function createBooking($data) {
        // Trigger trong DB sẽ tự sinh MaBookingCode (BK...)
        $sql = "INSERT INTO booking (MaTour, MaKhachHang, NgayKhoiHanh, SoLuongKhach, TongTien, TienCoc, TrangThai, GhiChu) 
                VALUES (:matour, :makhach, :ngaydi, :soluong, :tongtien, 0, 'Chờ xác nhận', :ghichu)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'matour' => $data['tour_id'],
            'makhach' => $data['customer_id'],
            'ngaydi' => $data['start_date'],
            'soluong' => $data['total_pax'],
            'tongtien' => $data['total_amount'],
            'ghichu' => $data['note']
        ]);
        
        return $this->conn->lastInsertId(); // Trả về ID Booking
    }

    // 4. Liên kết Booking với Lịch (Để trừ chỗ trống)
    public function addBookingSchedule($bookingId, $scheduleId) {
        $sql = "INSERT INTO booking_lichkhoihanh (MaBooking, MaLichKhoiHanh) VALUES (:bk, :lkh)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['bk' => $bookingId, 'lkh' => $scheduleId]);
    }
    // --- PHẦN DÀNH CHO ADMIN ---

    // 5. Lấy danh sách tất cả booking (Có thể lọc theo trạng thái)
    public function getAllBookings() {
        $sql = "SELECT b.*, t.TenTour, k.HoTen as TenKhach, k.SoDienThoai 
                FROM booking b
                JOIN tour t ON b.MaTour = t.MaTour
                LEFT JOIN khachhang k ON b.MaKhachHang = k.MaKhachHang
                ORDER BY b.NgayDat DESC"; // Mới nhất lên đầu
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 6. Lấy chi tiết 1 đơn hàng (Kèm thông tin khách và tour)
    public function getBookingDetail($id) {
        $sql = "SELECT b.*, t.TenTour, t.HinhAnh, k.HoTen, k.Email, k.SoDienThoai, k.DiaChi 
                FROM booking b
                JOIN tour t ON b.MaTour = t.MaTour
                LEFT JOIN khachhang k ON b.MaKhachHang = k.MaKhachHang
                WHERE b.MaBooking = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // 7. Cập nhật trạng thái đơn hàng (Duyệt / Hủy / Đã thanh toán)    
    public function updateStatus($id, $status) {
        $sql = "UPDATE booking SET TrangThai = :stt WHERE MaBooking = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['stt' => $status, 'id' => $id]);
    }
}



