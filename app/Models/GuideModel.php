<?php
require_once '../app/Core/Model.php';

class GuideModel extends Model {

    // 1. Lấy danh sách lịch trình phân công cho HDV
    public function getMySchedules($staffId) {
        $sql = "SELECT l.*, t.TenTour, t.SoNgay, t.HinhAnh,
                       (SELECT COUNT(*) FROM booking b WHERE b.MaLichKhoiHanh = l.MaLichKhoiHanh AND b.TrangThai != 'Đã hủy') as TongDon
                FROM lichkhoihanh l
                JOIN tour t ON l.MaTour = t.MaTour
                JOIN phanbonhansu p ON l.MaLichKhoiHanh = p.MaLichKhoiHanh
                WHERE p.MaNhanSu = ? 
                AND l.TrangThai IN ('Nhận khách', 'Đã đóng sổ', 'Đang chuẩn bị', 'Đang chạy', 'Hoàn tất')
                ORDER BY l.NgayKhoiHanh DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staffId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. [FIX LỖI] Lấy thông tin cơ bản của 1 lịch trình (để hiển thị tiêu đề, breadcrumb...)
    public function getScheduleDetail($scheduleId) {
        $sql = "SELECT l.*, t.MaTour, t.TenTour, t.SoNgay 
                FROM lichkhoihanh l 
                JOIN tour t ON l.MaTour = t.MaTour 
                WHERE l.MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$scheduleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Lấy danh sách khách toàn đoàn (cho trang Guest Manifest)
    public function getGuestsBySchedule($scheduleId) {
        $staffId = $_SESSION['staff_id'] ?? 0;

        // Kiểm tra HDV có phụ trách xe nào không
        $stmtXe = $this->conn->prepare("SELECT MaTaiNguyen FROM phanbonhansu WHERE MaLichKhoiHanh = ? AND MaNhanSu = ?");
        $stmtXe->execute([$scheduleId, $staffId]);
        $myVehicleId = $stmtXe->fetchColumn(); 

        $sql = "SELECT ct.*, kh.HoTen, kh.SoDienThoai, kh.GioiTinh, 
                       b.MaBookingCode, tn.TenTaiNguyen as TenXe,
                       (SELECT COUNT(*) FROM checkinkhach ck 
                        WHERE ck.MaChiTietKhach = ct.MaChiTiet AND ck.MaLichKhoiHanh = :lid AND ck.NgayCheckIn = CURDATE()) as IsCheckedIn,
                       (SELECT NoiDung FROM yeucaudacbiet yc 
                        WHERE yc.MaChiTietKhach = ct.MaChiTiet ORDER BY MaYeuCau DESC LIMIT 1) as YeuCauDacBiet
                FROM chitietkhachbooking ct
                JOIN booking b ON ct.MaBooking = b.MaBooking
                JOIN khachhang kh ON ct.MaKhachHang = kh.MaKhachHang
                LEFT JOIN tai_nguyen_ncc tn ON ct.MaTaiNguyen = tn.MaTaiNguyen
                WHERE b.MaLichKhoiHanh = :lid AND b.TrangThai != 'Đã hủy'";

        if ($myVehicleId) {
            $sql .= " AND ct.MaTaiNguyen = " . $myVehicleId;
        }

        $sql .= " ORDER BY ct.MaTaiNguyen DESC, kh.HoTen ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['lid' => $scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Lấy Timeline (Lịch trình các ngày)
    public function getScheduleWithTimeline($scheduleId) {
        $schedule = $this->getScheduleDetail($scheduleId);
        if (!$schedule) return null;

        $stmtDays = $this->conn->prepare("SELECT * FROM lichtrinhtour WHERE MaTour = ? ORDER BY NgayThu ASC");
        $stmtDays->execute([$schedule['MaTour']]);
        $timeline = $stmtDays->fetchAll(PDO::FETCH_ASSOC);

        foreach ($timeline as &$day) {
            $startDate = new DateTime($schedule['NgayKhoiHanh']);
            $startDate->modify('+' . ($day['NgayThu'] - 1) . ' days');
            $day['RealDate'] = $startDate->format('Y-m-d'); 
        }

        return ['info' => $schedule, 'timeline' => $timeline];
    }

    // 5. Lấy dữ liệu CHI TIẾT CỦA 1 NGÀY (Check-in Sáng/Chiều, Nhật ký, Ảnh)
    public function getDayDetail($scheduleId, $dayNum) {
        $stmtDate = $this->conn->prepare("SELECT NgayKhoiHanh, MaTour FROM lichkhoihanh WHERE MaLichKhoiHanh = ?");
        $stmtDate->execute([$scheduleId]);
        $row = $stmtDate->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) return null;

        $realDate = date('Y-m-d', strtotime($row['NgayKhoiHanh'] . ' + ' . ($dayNum - 1) . ' days'));

        // Lấy nội dung công việc (Lịch trình)
        $stmtPlan = $this->conn->prepare("SELECT TieuDe, NoiDung FROM lichtrinhtour WHERE MaTour = ? AND NgayThu = ?");
        $stmtPlan->execute([$row['MaTour'], $dayNum]);
        $dayPlan = $stmtPlan->fetch(PDO::FETCH_ASSOC);

        // Lấy danh sách khách & Check-in AM/PM
        $sqlGuests = "SELECT ct.*, kh.HoTen, kh.SoDienThoai, b.MaBookingCode,
                             (SELECT COUNT(*) FROM checkinkhach ck WHERE ck.MaChiTietKhach = ct.MaChiTiet AND ck.MaLichKhoiHanh = :lid AND ck.NgayCheckIn = :rDate AND ck.LoaiCheckIn = 'AM') as CheckInAM,
                             (SELECT COUNT(*) FROM checkinkhach ck WHERE ck.MaChiTietKhach = ct.MaChiTiet AND ck.MaLichKhoiHanh = :lid AND ck.NgayCheckIn = :rDate AND ck.LoaiCheckIn = 'PM') as CheckInPM
                      FROM chitietkhachbooking ct
                      JOIN booking b ON ct.MaBooking = b.MaBooking
                      JOIN khachhang kh ON ct.MaKhachHang = kh.MaKhachHang
                      WHERE b.MaLichKhoiHanh = :lid AND b.TrangThai != 'Đã hủy'
                      ORDER BY kh.HoTen ASC";
        
        $stmtG = $this->conn->prepare($sqlGuests);
        $stmtG->execute(['lid' => $scheduleId, 'rDate' => $realDate]);
        $guests = $stmtG->fetchAll(PDO::FETCH_ASSOC);

        // Lấy Nhật ký & Ảnh
        $stmtD = $this->conn->prepare("SELECT * FROM nhatkytour WHERE MaLichKhoiHanh = ? AND NgayThu = ?");
        $stmtD->execute([$scheduleId, $dayNum]);
        $diary = $stmtD->fetch(PDO::FETCH_ASSOC);

        $stmtP = $this->conn->prepare("SELECT * FROM hinhanh_hoatdong WHERE MaLichKhoiHanh = ? AND NgayThu = ? ORDER BY MaHinhAnh DESC");
        $stmtP->execute([$scheduleId, $dayNum]);
        $photos = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        return [
            'realDate' => $realDate,
            'guests'   => $guests,
            'diary'    => $diary,
            'photos'   => $photos,
            'dayPlan'  => $dayPlan
        ];
    }

    // 6. Check-in khách theo ngày & buổi (AM/PM)
    public function toggleCheckInDay($scheduleId, $guestDetailId, $status, $dateCheckIn, $type) {
        if ($status == 'check') {
            $sql = "INSERT INTO checkinkhach (MaLichKhoiHanh, MaChiTietKhach, NgayCheckIn, GioCheckIn, LoaiCheckIn, TrangThai) VALUES (?, ?, ?, CURTIME(), ?, 'Đã đến')";
            return $this->conn->prepare($sql)->execute([$scheduleId, $guestDetailId, $dateCheckIn, $type]);
        } else {
            $sql = "DELETE FROM checkinkhach WHERE MaLichKhoiHanh = ? AND MaChiTietKhach = ? AND NgayCheckIn = ? AND LoaiCheckIn = ?";
            return $this->conn->prepare($sql)->execute([$scheduleId, $guestDetailId, $dateCheckIn, $type]);
        }
    }

    // 7. Check-in đơn giản (Legacy - cho trang Guest List cũ)
    public function toggleCheckIn($scheduleId, $guestDetailId, $status) {
        if ($status == 'check') {
            $sql = "INSERT INTO checkinkhach (MaLichKhoiHanh, MaChiTietKhach, NgayCheckIn, GioCheckIn, TrangThai) VALUES (?, ?, CURDATE(), CURTIME(), 'Đã đến')";
            return $this->conn->prepare($sql)->execute([$scheduleId, $guestDetailId]);
        } else {
            $sql = "DELETE FROM checkinkhach WHERE MaLichKhoiHanh = ? AND MaChiTietKhach = ? AND NgayCheckIn = CURDATE()";
            return $this->conn->prepare($sql)->execute([$scheduleId, $guestDetailId]);
        }
    }

    // 8. Cập nhật yêu cầu đặc biệt
    public function updateSpecialRequest($guestDetailId, $content, $userId) {
        $sql = "INSERT INTO yeucaudacbiet (MaChiTietKhach, LoaiYeuCau, NoiDung, MucDo, TrangThaiXuLy, NguoiXuLy, ThoiGianXuLy) VALUES (?, 'Ghi chú HDV', ?, 'Trung bình', 'Đang xử lý', ?, NOW())";
        return $this->conn->prepare($sql)->execute([$guestDetailId, $content, $userId]);
    }

    // 9. Lưu nhật ký ngày
    public function saveDailyDiary($data) {
        $check = $this->conn->prepare("SELECT MaNhatKy FROM nhatkytour WHERE MaLichKhoiHanh = ? AND NgayThu = ?");
        $check->execute([$data['lid'], $data['day']]);
        $existId = $check->fetchColumn();

        if ($existId) {
            $sql = "UPDATE nhatkytour SET TieuDe = ?, NoiDung = ?, NgayGhi = NOW() WHERE MaNhatKy = ?";
            return $this->conn->prepare($sql)->execute([$data['title'], $data['content'], $existId]);
        } else {
            $sql = "INSERT INTO nhatkytour (MaLichKhoiHanh, MaNhanSu, NgayThu, TieuDe, NoiDung, NgayGhi) VALUES (?, ?, ?, ?, ?, NOW())";
            return $this->conn->prepare($sql)->execute([$data['lid'], $data['uid'], $data['day'], $data['title'], $data['content']]);
        }
    }

    // 10. Quản lý Ảnh hoạt động
    public function addPhoto($scheduleId, $day, $path) {
        $sql = "INSERT INTO hinhanh_hoatdong (MaLichKhoiHanh, NgayThu, DuongDan, NgayTao) VALUES (?, ?, ?, NOW())";
        return $this->conn->prepare($sql)->execute([$scheduleId, $day, $path]);
    }
    
    public function deletePhoto($id) {
        $stmt = $this->conn->prepare("DELETE FROM hinhanh_hoatdong WHERE MaHinhAnh = ?");
        return $stmt->execute([$id]);
    }

    // 11. Nhật ký chung (Legacy)
    public function getDiaries($scheduleId) {
        $stmt = $this->conn->prepare("SELECT * FROM nhatkytour WHERE MaLichKhoiHanh = ? ORDER BY NgayGhi DESC");
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function addDiary($data) {
        $sql = "INSERT INTO nhatkytour (MaLichKhoiHanh, MaNhanSu, NgayGhi, TieuDe, NoiDung) VALUES (:lid, :uid, NOW(), :title, :content)";
        return $this->conn->prepare($sql)->execute($data);
    }
    public function deleteDiary($id) {
        $stmt = $this->conn->prepare("DELETE FROM nhatkytour WHERE MaNhatKy = ?");
        return $stmt->execute([$id]);
    }
}
?>