<?php
require_once '../app/Core/Model.php';

class GuideModel extends Model {

    // 1. Lấy lịch trình được phân công cho HDV (Chỉ lấy lịch Sắp tới hoặc Đang chạy)
    public function getMySchedules($staffId) {
        // JOIN qua bảng phanbonhansu để lấy đúng lịch của HDV đó
        $sql = "SELECT l.*, t.TenTour, t.SoNgay, t.HinhAnh,
                       (SELECT COUNT(*) FROM booking b WHERE b.MaLichKhoiHanh = l.MaLichKhoiHanh AND b.TrangThai != 'Đã hủy') as TongDon
                FROM lichkhoihanh l
                JOIN tour t ON l.MaTour = t.MaTour
                JOIN phanbonhansu p ON l.MaLichKhoiHanh = p.MaLichKhoiHanh
                WHERE p.MaNhanSu = :id 
                AND l.TrangThai IN ('Nhận khách', 'Đang chạy', 'Đang chuẩn bị', 'Hoàn tất')
                ORDER BY l.NgayKhoiHanh DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $staffId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy danh sách khách (Kèm trạng thái Check-in hôm nay & Yêu cầu đặc biệt)
    public function getGuestsBySchedule($scheduleId) {
        $sql = "SELECT ct.*, kh.HoTen, kh.SoDienThoai, kh.GioiTinh,
                       b.MaBookingCode,
                       -- Kiểm tra xem khách đã check-in hôm nay chưa
                       (SELECT COUNT(*) FROM checkinkhach ck 
                        WHERE ck.MaChiTietKhach = ct.MaChiTiet 
                        AND ck.MaLichKhoiHanh = :lid 
                        AND ck.NgayCheckIn = CURDATE()) as IsCheckedIn,
                       -- Lấy yêu cầu đặc biệt (Ghi chú xử lý)
                       (SELECT NoiDung FROM yeucaudacbiet yc 
                        WHERE yc.MaChiTietKhach = ct.MaChiTiet 
                        ORDER BY MaYeuCau DESC LIMIT 1) as YeuCauDacBiet
                FROM chitietkhachbooking ct
                JOIN booking b ON ct.MaBooking = b.MaBooking
                JOIN khachhang kh ON ct.MaKhachHang = kh.MaKhachHang
                WHERE b.MaLichKhoiHanh = :lid AND b.TrangThai != 'Đã hủy'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['lid' => $scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Xử lý Check-in / Hủy Check-in (AJAX)
    public function toggleCheckIn($scheduleId, $guestDetailId, $status) {
        if ($status == 'check') {
            // Thêm vào bảng check-in
            $sql = "INSERT INTO checkinkhach (MaLichKhoiHanh, MaChiTietKhach, NgayCheckIn, GioCheckIn, TrangThai) 
                    VALUES (?, ?, CURDATE(), CURTIME(), 'Đã đến')";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$scheduleId, $guestDetailId]);
        } else {
            // Xóa check-in hôm nay
            $sql = "DELETE FROM checkinkhach WHERE MaLichKhoiHanh = ? AND MaChiTietKhach = ? AND NgayCheckIn = CURDATE()";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$scheduleId, $guestDetailId]);
        }
    }

    // 4. Cập nhật yêu cầu đặc biệt (Ghi chú sức khỏe/Ăn uống)
    public function updateSpecialRequest($guestDetailId, $content, $userId) {
        // Thêm mới yêu cầu (Lịch sử)
        $sql = "INSERT INTO yeucaudacbiet (MaChiTietKhach, LoaiYeuCau, NoiDung, MucDo, TrangThaiXuLy, NguoiXuLy, ThoiGianXuLy) 
                VALUES (?, 'Ghi chú HDV', ?, 'Trung bình', 'Đang xử lý', ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$guestDetailId, $content, $userId]);
    }

    // 5. Quản lý Nhật ký Tour
    public function getDiaries($scheduleId) {
        $stmt = $this->conn->prepare("SELECT * FROM nhatkytour WHERE MaLichKhoiHanh = ? ORDER BY NgayGhi DESC, MaNhatKy DESC");
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addDiary($data) {
        $sql = "INSERT INTO nhatkytour (MaLichKhoiHanh, MaNhanSu, NgayGhi, TieuDe, NoiDung) 
                VALUES (:lid, :uid, NOW(), :title, :content)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function deleteDiary($id) {
        $stmt = $this->conn->prepare("DELETE FROM nhatkytour WHERE MaNhatKy = ?");
        return $stmt->execute([$id]);
    }
}
?>