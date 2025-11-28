<?php
require_once '../app/Core/Model.php';

class ScheduleModel extends Model {

    // Lấy danh sách Tour để đổ vào select box
    public function getTours() {
        return $this->conn->query("SELECT MaTour, TenTour, SoNgay FROM tour WHERE TrangThai='Hoạt động'")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách Nhân sự (HDV)
    public function getStaffs() {
        return $this->conn->query("SELECT * FROM nhansu WHERE LoaiNhanSu='HDV'")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách Tài nguyên theo nhóm Nhà cung cấp
    public function getResources() {
        $sql = "SELECT tn.*, ncc.TenNhaCungCap, ncc.LoaiCungCap 
                FROM tai_nguyen_ncc tn 
                JOIN nha_cung_cap ncc ON tn.MaNhaCungCap = ncc.MaNhaCungCap";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- HÀM TẠO LỊCH (QUAN TRỌNG) ---
    public function createSchedule($data, $resources = [], $staffs = []) {
        try {
            // 1. Bắt đầu Transaction
            $this->conn->beginTransaction();

            // 2. Tạo mã lịch tự động (VD: LKH + Timestamp)
            $lichCode = 'LKH' . time();

            // 3. Insert vào bảng Lịch Khởi Hành
            $sql = "INSERT INTO lichkhoihanh (MaTour, LichCode, NgayKhoiHanh, NgayKetThuc, GioTapTrung, DiaDiemTapTrung, SoChoHienTai, TrangThai) 
                    VALUES (:tour_id, :code, :start, :end, :time, :place, :seats, 'Nhận khách')";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                'tour_id' => $data['tour_id'],
                'code'    => $lichCode,
                'start'   => $data['start_date'],
                'end'     => $data['end_date'],
                'time'    => $data['meeting_time'],
                'place'   => $data['meeting_place'],
                'seats'   => 0 // Mới tạo chưa có khách
            ]);
            
            $lichId = $this->conn->lastInsertId();

            // 4. Insert Phân bổ Tài nguyên (Xe, Khách sạn)
            if (!empty($resources)) {
                $sqlRes = "INSERT INTO phan_bo_tai_nguyen (MaLichKhoiHanh, MaTaiNguyen) VALUES (?, ?)";
                $stmtRes = $this->conn->prepare($sqlRes);
                foreach ($resources as $resId) {
                    $stmtRes->execute([$lichId, $resId]);
                }
            }

            // 5. Insert Phân bổ Nhân sự (HDV)
            if (!empty($staffs)) {
                $sqlStaff = "INSERT INTO phanbonhansu (MaLichKhoiHanh, MaNhanSu, VaiTro) VALUES (?, ?, 'Hướng dẫn viên')";
                $stmtStaff = $this->conn->prepare($sqlStaff);
                foreach ($staffs as $staffId) {
                    $stmtStaff->execute([$lichId, $staffId]);
                }
            }

            // 6. Hoàn tất Transaction
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // Nếu lỗi thì hoàn tác tất cả
            $this->conn->rollBack();
            return false;
        }
    }
    
    // Hàm lấy danh sách lịch để hiển thị trang Index (Giai đoạn sau)
    public function getAllSchedules($tour_id = null) {
        $sql = "SELECT l.*, t.TenTour, t.SoNgay FROM lichkhoihanh l 
                JOIN tour t ON l.MaTour = t.MaTour";
        
        $params = [];

        // Nếu có truyền tour_id thì thêm điều kiện WHERE
        if ($tour_id) {
            $sql .= " WHERE l.MaTour = :id";
            $params['id'] = $tour_id;
        }

        $sql .= " ORDER BY l.NgayKhoiHanh DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm hàm phụ để lấy tên Tour (dùng hiển thị tiêu đề cho đẹp)
    public function getTourName($id) {
        $stmt = $this->conn->prepare("SELECT TenTour FROM tour WHERE MaTour = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['TenTour'] : '';
    }
    // --- LẤY CHI TIẾT LỊCH ---
    public function getScheduleById($id) {
        $sql = "SELECT l.*, t.TenTour, t.SoNgay, t.MaTour 
                FROM lichkhoihanh l 
                JOIN tour t ON l.MaTour = t.MaTour 
                WHERE l.MaLichKhoiHanh = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách ID tài nguyên đã gán (để check vào checkbox khi sửa)
    public function getAssignedResourceIds($scheduleId) {
        $sql = "SELECT MaTaiNguyen FROM phan_bo_tai_nguyen WHERE MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Chỉ lấy mảng [1, 5, 8...]
    }

    // Lấy danh sách ID nhân sự đã gán
    public function getAssignedStaffIds($scheduleId) {
        $sql = "SELECT MaNhanSu FROM phanbonhansu WHERE MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Lấy chi tiết tài nguyên (để hiển thị trang Show)
    public function getAssignedResourcesDetail($scheduleId) {
        $sql = "SELECT tn.*, ncc.TenNhaCungCap 
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

    // --- CẬP NHẬT LỊCH (Update) ---
    public function updateSchedule($id, $data, $resources = [], $staffs = []) {
        try {
            $this->conn->beginTransaction();

            // 1. Update thông tin chính
            $sql = "UPDATE lichkhoihanh SET 
                    MaTour = :tour_id, NgayKhoiHanh = :start, NgayKetThuc = :end, 
                    GioTapTrung = :time, DiaDiemTapTrung = :place, TrangThai = :status 
                    WHERE MaLichKhoiHanh = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                'tour_id' => $data['tour_id'],
                'start'   => $data['start_date'],
                'end'     => $data['end_date'],
                'time'    => $data['meeting_time'],
                'place'   => $data['meeting_place'],
                'status'  => $data['status'],
                'id'      => $id
            ]);

            // 2. Xóa phân bổ cũ -> Thêm phân bổ mới (Cách an toàn nhất để update quan hệ nhiều-nhiều)
            
            // a. Tài nguyên
            $this->conn->prepare("DELETE FROM phan_bo_tai_nguyen WHERE MaLichKhoiHanh = ?")->execute([$id]);
            if (!empty($resources)) {
                $stmtRes = $this->conn->prepare("INSERT INTO phan_bo_tai_nguyen (MaLichKhoiHanh, MaTaiNguyen) VALUES (?, ?)");
                foreach ($resources as $resId) $stmtRes->execute([$id, $resId]);
            }

            // b. Nhân sự
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

    // --- XÓA LỊCH (Delete) ---
    public function deleteSchedule($id) {
        // Do đã cài đặt ON DELETE CASCADE trong DB (nếu chưa thì phải xóa bảng con trước)
        // Lệnh xóa này sẽ tự động xóa các dòng bên phan_bo_tai_nguyen và phanbonhansu
        $stmt = $this->conn->prepare("DELETE FROM lichkhoihanh WHERE MaLichKhoiHanh = ?");
        return $stmt->execute([$id]);
    }
}
?>