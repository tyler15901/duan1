<?php
require_once '../app/Core/Model.php';

class ScheduleModel extends Model {

    public function getTours() {
        return $this->conn->query("SELECT MaTour, TenTour, SoNgay FROM tour WHERE TrangThai='Hoạt động'")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStaffs() {
        return $this->conn->query("SELECT * FROM nhansu WHERE LoaiNhanSu='HDV'")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResources() {
        $sql = "SELECT tn.*, ncc.TenNhaCungCap, ncc.LoaiCungCap 
                FROM tai_nguyen_ncc tn 
                JOIN nha_cung_cap ncc ON tn.MaNhaCungCap = ncc.MaNhaCungCap";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- HÀM TẠO LỊCH ---
    public function createSchedule($data, $resources = [], $staffs = []) {
        try {
            $this->conn->beginTransaction();
            $lichCode = 'LKH' . time();

            // Insert Lịch (Sử dụng SoChoToiDa của bảng lịch)
            $sql = "INSERT INTO lichkhoihanh (
                        MaTour, LichCode, NgayKhoiHanh, NgayKetThuc, 
                        GioTapTrung, DiaDiemTapTrung, SoKhachHienTai, SoChoToiDa, 
                        TrangThai, GiaNguoiLon, GiaTreEm
                    ) 
                    VALUES (
                        :tour_id, :code, :start, :end, 
                        :time, :place, 0, :max_pax, 
                        'Nhận khách', :p_adult, :p_child
                    )";
            
            $stmt = $this->conn->prepare($sql);
            
            $params = [
                'tour_id' => $data['tour_id'],
                'code'    => $lichCode,
                'start'   => $data['start_date'],
                'end'     => $data['end_date'],
                'time'    => $data['meeting_time'],
                'place'   => $data['meeting_place'],
                'max_pax' => $data['max_pax'],
                'p_adult' => $data['price_adult'],
                'p_child' => $data['price_child']
            ];

            if (!$stmt->execute($params)) {
                $error = $stmt->errorInfo();
                throw new Exception("Lỗi SQL Insert: " . $error[2]);
            }
            
            $lichId = $this->conn->lastInsertId();

            // Insert Tài nguyên
            if (!empty($resources)) {
                $sqlRes = "INSERT INTO phan_bo_tai_nguyen (MaLichKhoiHanh, MaTaiNguyen) VALUES (?, ?)";
                $stmtRes = $this->conn->prepare($sqlRes);
                foreach ($resources as $resId) $stmtRes->execute([$lichId, $resId]);
            }

            // Insert Nhân sự
            if (!empty($staffs)) {
                $sqlStaff = "INSERT INTO phanbonhansu (MaLichKhoiHanh, MaNhanSu, VaiTro) VALUES (?, ?, 'Hướng dẫn viên')";
                $stmtStaff = $this->conn->prepare($sqlStaff);
                foreach ($staffs as $staffId) $stmtStaff->execute([$lichId, $staffId]);
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // --- CẬP NHẬT LỊCH ---
    public function updateSchedule($id, $data, $resources = [], $staffs = []) {
        try {
            $this->conn->beginTransaction();

            $sql = "UPDATE lichkhoihanh SET 
                    MaTour = :tour_id, NgayKhoiHanh = :start, NgayKetThuc = :end, 
                    GioTapTrung = :time, DiaDiemTapTrung = :place, TrangThai = :status,
                    GiaNguoiLon = :p_adult, GiaTreEm = :p_child, SoChoToiDa = :max_pax
                    WHERE MaLichKhoiHanh = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                'tour_id' => $data['tour_id'],
                'start'   => $data['start_date'],
                'end'     => $data['end_date'],
                'time'    => $data['meeting_time'],
                'place'   => $data['meeting_place'],
                'status'  => $data['status'],
                'p_adult' => $data['price_adult'],
                'p_child' => $data['price_child'],
                'max_pax' => $data['max_pax'],
                'id'      => $id
            ]);

            // Cập nhật phân bổ (Reset và thêm mới)
            $this->conn->prepare("DELETE FROM phan_bo_tai_nguyen WHERE MaLichKhoiHanh = ?")->execute([$id]);
            if (!empty($resources)) {
                $stmtRes = $this->conn->prepare("INSERT INTO phan_bo_tai_nguyen (MaLichKhoiHanh, MaTaiNguyen) VALUES (?, ?)");
                foreach ($resources as $resId) $stmtRes->execute([$id, $resId]);
            }

            $this->conn->prepare("DELETE FROM phanbonhansu WHERE MaLichKhoiHanh = ?")->execute([$id]);
            if (!empty($staffs)) {
                $stmtStaff = $this->conn->prepare("INSERT INTO phanbonhansu (MaLichKhoiHanh, MaNhanSu, VaiTro) VALUES (?, ?, 'Hướng dẫn viên')");
                foreach ($staffs as $staffId) $stmtStaff->execute([$id, $staffId]);
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // --- LẤY CHI TIẾT LỊCH (Đã xóa t.SoChoToiDa) ---
    public function getScheduleById($id) {
        $sql = "SELECT l.*, t.TenTour, t.SoNgay, t.MaTour
                FROM lichkhoihanh l 
                JOIN tour t ON l.MaTour = t.MaTour 
                WHERE l.MaLichKhoiHanh = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- LẤY DANH SÁCH LỊCH LỌC (Đã xóa t.SoChoToiDa) ---
    public function getSchedulesFiltered($filters = [], $limit = 10, $offset = 0) {
        // [QUAN TRỌNG] Chỉ lấy l.* (đã có SoChoToiDa) và các cột cần thiết từ tour
        $sql = "SELECT l.*, t.TenTour, t.SoNgay 
                FROM lichkhoihanh l 
                JOIN tour t ON l.MaTour = t.MaTour 
                WHERE 1=1";

        $params = [];

        if (!empty($filters['tour_id'])) {
            $sql .= " AND l.MaTour = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }

        if (!empty($filters['keyword'])) {
            $sql .= " AND (l.LichCode LIKE :kw OR t.TenTour LIKE :kw)";
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['date'])) {
            $sql .= " AND l.NgayKhoiHanh >= :date";
            $params['date'] = $filters['date'];
        }

        $sql .= " ORDER BY l.NgayKhoiHanh DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countSchedules($filters = []) {
        $sql = "SELECT COUNT(*) as total 
                FROM lichkhoihanh l 
                JOIN tour t ON l.MaTour = t.MaTour 
                WHERE 1=1";
        
        $params = [];
        if (!empty($filters['tour_id'])) {
            $sql .= " AND l.MaTour = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }
        if (!empty($filters['keyword'])) {
            $sql .= " AND (l.LichCode LIKE :kw OR t.TenTour LIKE :kw)";
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }
        if (!empty($filters['date'])) {
            $sql .= " AND l.NgayKhoiHanh >= :date";
            $params['date'] = $filters['date'];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Các hàm phụ trợ
    public function getTourName($id) {
        $stmt = $this->conn->prepare("SELECT TenTour FROM tour WHERE MaTour = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['TenTour'] : '';
    }

    public function deleteSchedule($id) {
        $stmt = $this->conn->prepare("DELETE FROM lichkhoihanh WHERE MaLichKhoiHanh = ?");
        return $stmt->execute([$id]);
    }

    public function getBookingsBySchedule($scheduleId) {
        $sql = "SELECT b.*, kh.HoTen, kh.SoDienThoai, kh.Email 
                FROM booking b 
                JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang 
                WHERE b.MaLichKhoiHanh = :id 
                ORDER BY b.NgayDat DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAssignedResourceIds($scheduleId) {
        $sql = "SELECT MaTaiNguyen FROM phan_bo_tai_nguyen WHERE MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getAssignedStaffIds($scheduleId) {
        $sql = "SELECT MaNhanSu FROM phanbonhansu WHERE MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getAssignedResourcesDetail($scheduleId) {
        $sql = "SELECT tn.*, ncc.TenNhaCungCap, ncc.LoaiCungCap 
                FROM phan_bo_tai_nguyen p 
                JOIN tai_nguyen_ncc tn ON p.MaTaiNguyen = tn.MaTaiNguyen
                JOIN nha_cung_cap ncc ON tn.MaNhaCungCap = ncc.MaNhaCungCap
                WHERE p.MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAssignedStaffsDetail($scheduleId) {
        $sql = "SELECT ns.* FROM phanbonhansu p 
                JOIN nhansu ns ON p.MaNhanSu = ns.MaNhanSu
                WHERE p.MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGuidesAvailability($startDate, $endDate, $excludeScheduleId = 0) {
        $sqlBusy = "SELECT DISTINCT p.MaNhanSu 
                    FROM phanbonhansu p
                    JOIN lichkhoihanh l ON p.MaLichKhoiHanh = l.MaLichKhoiHanh
                    WHERE l.TrangThai IN ('Nhận khách', 'Đang chuẩn bị', 'Đang chạy')
                    AND l.MaLichKhoiHanh != :excludeId
                    AND ((l.NgayKhoiHanh <= :end_date) AND (l.NgayKetThuc >= :start_date))";
        
        $stmt = $this->conn->prepare($sqlBusy);
        $stmt->execute([
            'end_date' => $endDate,
            'start_date' => $startDate,
            'excludeId' => $excludeScheduleId
        ]);
        $busyIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $allGuides = $this->conn->query("SELECT * FROM nhansu WHERE LoaiNhanSu='HDV'")->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach ($allGuides as $guide) {
            $guide['is_busy'] = in_array($guide['MaNhanSu'], $busyIds);
            $result[] = $guide;
        }
        return $result;
    }
}
?>