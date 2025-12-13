<?php
require_once '../app/Core/Model.php';

class ScheduleModel extends Model {

    // --- CÁC HÀM GET DỮ LIỆU CƠ BẢN ---
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


    private function createWarningNotification($lichId, $code, $pax, $min) {
        // Link trỏ thẳng đến danh sách khách để Admin tiện gọi điện
        $link = "/schedule/guests/$lichId"; 
        
        // Kiểm tra tránh spam thông báo trùng
        $check = $this->conn->prepare("SELECT COUNT(*) FROM thongbao WHERE LienKet = ? AND TieuDe LIKE '%Cảnh báo%'");
        $check->execute([$link]);
        
        if ($check->fetchColumn() == 0) {
            $title = "⚠️ Cảnh báo: Lịch [$code] thiếu khách (T-6)";
            $content = "Còn 6 ngày nữa khởi hành nhưng chỉ có $pax/$min khách. Vui lòng liên hệ Khách hàng để tư vấn chuyển lịch ngay.";
            
            $sql = "INSERT INTO thongbao (TieuDe, NoiDung, LoaiThongBao, LienKet, NgayTao) VALUES (?, ?, 'System', ?, NOW())";
            $this->conn->prepare($sql)->execute([$title, $content, $link]);
        }
    }

    public function autoUpdateTourStatus() {
        $today = date('Y-m-d');

        // 1. [MỚI - ƯU TIÊN 1] XỬ LÝ MỐC 6 NGÀY (Cảnh báo & Đóng sổ để xử lý)
        // Tìm các lịch còn đúng 6 ngày nữa đi + Chưa đủ khách
        $sqlCheck6 = "SELECT * FROM lichkhoihanh 
                      WHERE DATEDIFF(NgayKhoiHanh, :today) = 6 
                      AND SoKhachHienTai < SoChoMin 
                      AND TrangThai IN ('Nhận khách', 'Đang gom khách')";
        $stmt6 = $this->conn->prepare($sqlCheck6);
        $stmt6->execute(['today' => $today]);
        $list6 = $stmt6->fetchAll(PDO::FETCH_ASSOC);

        // Gửi thông báo cho từng lịch
        foreach ($list6 as $sch) {
            $this->createWarningNotification($sch['MaLichKhoiHanh'], $sch['LichCode'], $sch['SoKhachHienTai'], $sch['SoChoMin']);
        }

        // Chuyển trạng thái sang 'Đã đóng sổ' (để không nhận thêm đơn rác, tập trung xử lý khách cũ)
        $sqlUpd6 = "UPDATE lichkhoihanh SET TrangThai = 'Đã đóng sổ' 
                    WHERE DATEDIFF(NgayKhoiHanh, :today) = 6 
                    AND SoKhachHienTai < SoChoMin 
                    AND TrangThai IN ('Nhận khách', 'Đang gom khách')";
        $this->conn->prepare($sqlUpd6)->execute(['today' => $today]);


        // 2. TỰ ĐỘNG HỦY CHUYẾN (MỐC 5 NGÀY - NHƯ CŨ)
        // Nếu đến mốc 5 ngày mà vẫn chưa xử lý xong (vẫn < Min) -> Hủy
        $sqlCancel = "UPDATE lichkhoihanh 
                      SET TrangThai = 'Hủy chuyến' 
                      WHERE DATEDIFF(NgayKhoiHanh, :today) <= 5 
                      AND DATEDIFF(NgayKhoiHanh, :today) > 1
                      AND SoKhachHienTai < SoChoMin
                      AND TrangThai IN ('Nhận khách', 'Đang gom khách', 'Đã đóng sổ')"; // Thêm Đã đóng sổ vào để hủy cái ở bước 1 nếu ko ai xử lý
        $this->conn->prepare($sqlCancel)->execute(['today' => $today]);


        // 3. CÁC LOGIC KHÁC (ĐÓNG SỔ SÁT NGÀY / FULL / CHẠY / HOÀN TẤT) - GIỮ NGUYÊN
        // Đóng sổ khi còn <= 1 ngày
        $sqlCloseTime = "UPDATE lichkhoihanh SET TrangThai = 'Đã đóng sổ' 
                         WHERE DATEDIFF(NgayKhoiHanh, :today) <= 1 AND DATEDIFF(NgayKhoiHanh, :today) >= 0
                         AND TrangThai IN ('Nhận khách', 'Đang gom khách')";
        $this->conn->prepare($sqlCloseTime)->execute(['today' => $today]);

        // Đóng sổ khi Full
        $sqlCloseFull = "UPDATE lichkhoihanh SET TrangThai = 'Đã đóng sổ' 
                         WHERE SoKhachHienTai >= SoChoToiDa AND SoChoToiDa > 0
                         AND TrangThai IN ('Nhận khách', 'Đang gom khách')";
        $this->conn->query($sqlCloseFull);

        // Đang chạy
        $sqlRun = "UPDATE lichkhoihanh SET TrangThai = 'Đang chạy' 
                   WHERE NgayKhoiHanh <= :today AND NgayKetThuc >= :today 
                   AND TrangThai IN ('Nhận khách', 'Đã đóng sổ', 'Đang chuẩn bị', 'Đang gom khách')";
        $this->conn->prepare($sqlRun)->execute(['today' => $today]);

        // Hoàn tất
        $sqlFinish = "UPDATE lichkhoihanh SET TrangThai = 'Hoàn tất' 
                      WHERE NgayKetThuc < :today 
                      AND TrangThai NOT IN ('Hoàn tất', 'Đã hủy', 'Hủy chuyến')";
        $this->conn->prepare($sqlFinish)->execute(['today' => $today]);
    }
    // --- HÀM TẠO LỊCH ---
    public function createSchedule($data, $resources = [], $staffs = []) {
        try {
            $this->conn->beginTransaction();
            $lichCode = 'LKH' . time();

            $sql = "INSERT INTO lichkhoihanh (
                        MaTour, LichCode, NgayKhoiHanh, NgayKetThuc, 
                        GioTapTrung, DiaDiemTapTrung, SoKhachHienTai, SoChoToiDa, SoChoMin,
                        TrangThai, GiaNguoiLon, GiaTreEm, NgayTao
                    ) 
                    VALUES (
                        :tour_id, :code, :start, :end, 
                        :time, :place, 0, :max_pax, :min_pax,
                        'Nhận khách', :p_adult, :p_child, NOW()
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
                'min_pax' => $data['min_pax'],
                'p_adult' => $data['price_adult'],
                'p_child' => $data['price_child']
            ];

            if (!$stmt->execute($params)) {
                $error = $stmt->errorInfo();
                throw new Exception("Lỗi SQL Insert: " . $error[2]);
            }
            
            $lichId = $this->conn->lastInsertId();

            if (!empty($resources)) {
                $sqlRes = "INSERT INTO phan_bo_tai_nguyen (MaLichKhoiHanh, MaTaiNguyen) VALUES (?, ?)";
                $stmtRes = $this->conn->prepare($sqlRes);
                foreach ($resources as $resId) $stmtRes->execute([$lichId, $resId]);
            }

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
                    GiaNguoiLon = :p_adult, GiaTreEm = :p_child, SoChoToiDa = :max_pax, SoChoMin = :min_pax
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
                'min_pax' => $data['min_pax'],
                'id'      => $id
            ]);

            // Cập nhật Tài nguyên
            $this->conn->prepare("DELETE FROM phan_bo_tai_nguyen WHERE MaLichKhoiHanh = ?")->execute([$id]);
            if (!empty($resources)) {
                $stmtRes = $this->conn->prepare("INSERT INTO phan_bo_tai_nguyen (MaLichKhoiHanh, MaTaiNguyen) VALUES (?, ?)");
                foreach ($resources as $resId) $stmtRes->execute([$id, $resId]);
            }

            // Cập nhật Nhân sự (HDV)
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

    // --- LẤY DANH SÁCH LỊCH LỌC ---
    public function getSchedulesFiltered($filters = [], $limit = 10, $offset = 0) {
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

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

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
                    WHERE l.TrangThai NOT IN ('Đã hủy', 'Hủy chuyến', 'Hoàn tất') 
                    AND l.MaLichKhoiHanh != :excludeId
                    AND ((l.NgayKhoiHanh <= :end_date) AND (l.NgayKetThuc >= :start_date))";
        
        $stmt = $this->conn->prepare($sqlBusy);
        $stmt->execute([
            'end_date' => $endDate,
            'start_date' => $startDate,
            'excludeId' => $excludeScheduleId
        ]);
        $busyIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $allGuides = $this->conn->query("SELECT * FROM nhansu WHERE LoaiNhanSu='HDV' AND TrangThai='Hoạt động'")->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach ($allGuides as $guide) {
            $guide['is_busy'] = in_array($guide['MaNhanSu'], $busyIds);
            $result[] = $guide;
        }
        return $result;
    }

    public function isGuideBusy($staffId, $startDate, $endDate, $excludeScheduleId = 0) {
        $sql = "SELECT COUNT(*) FROM phanbonhansu p
                JOIN lichkhoihanh l ON p.MaLichKhoiHanh = l.MaLichKhoiHanh
                WHERE p.MaNhanSu = :staffId
                AND l.MaLichKhoiHanh != :excludeId
                AND l.TrangThai NOT IN ('Đã hủy', 'Hủy chuyến', 'Hoàn tất')
                AND (l.NgayKhoiHanh <= :end_date AND l.NgayKetThuc >= :start_date)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'staffId' => $staffId,
            'excludeId' => $excludeScheduleId,
            'end_date' => $endDate,
            'start_date' => $startDate
        ]);
        
        return $stmt->fetchColumn() > 0; 
    }

    public function saveAssignedGuides($lichId, $guideIds) {
        try {
            $this->conn->beginTransaction();
            $stmtDel = $this->conn->prepare("DELETE FROM phanbonhansu WHERE MaLichKhoiHanh = ? AND VaiTro = 'Hướng dẫn viên'");
            $stmtDel->execute([$lichId]);

            if (!empty($guideIds)) {
                $sql = "INSERT INTO phanbonhansu (MaLichKhoiHanh, MaNhanSu, VaiTro) VALUES (?, ?, 'Hướng dẫn viên')";
                $stmt = $this->conn->prepare($sql);
                foreach ($guideIds as $guideId) {
                    $stmt->execute([$lichId, $guideId]);
                }
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // --- [QUAN TRỌNG] HÀM QUÉT VÀ CẬP NHẬT TRẠNG THÁI TỰ ĐỘNG ---
    public function autoCheckAndCloseSchedules() {
        $today = date('Y-m-d');

        // 1. TỰ ĐỘNG HỦY CHUYẾN (NẾU KHÁCH < MIN TRƯỚC 5 NGÀY)
        // Logic: Còn <= 5 ngày VÀ > 1 ngày nữa đi MÀ chưa đủ khách Min -> Hủy
        $sqlCancel = "UPDATE lichkhoihanh 
                      SET TrangThai = 'Hủy chuyến' 
                      WHERE DATEDIFF(NgayKhoiHanh, :today) <= 5 
                      AND DATEDIFF(NgayKhoiHanh, :today) > 1
                      AND SoKhachHienTai < SoChoMin
                      AND TrangThai IN ('Nhận khách', 'Đang gom khách')";
        $this->conn->prepare($sqlCancel)->execute(['today' => $today]);

        // 2. CHUYỂN SANG 'ĐÃ ĐÓNG SỔ' (CHUẨN BỊ CHẠY)
        // Logic: Còn <= 1 ngày nữa đi (hoặc đã quá ngày đi nhưng chưa chạy) -> Đóng sổ
        $sqlClose = "UPDATE lichkhoihanh 
                     SET TrangThai = 'Đã đóng sổ' 
                     WHERE DATEDIFF(NgayKhoiHanh, :today) <= 1 
                     AND DATEDIFF(NgayKhoiHanh, :today) >= 0
                     AND TrangThai IN ('Nhận khách', 'Đang gom khách')";
        $this->conn->prepare($sqlClose)->execute(['today' => $today]);

        // 3. CHUYỂN SANG 'ĐANG CHẠY'
        // Logic: Hôm nay nằm trong khoảng thời gian Tour
        $sqlRun = "UPDATE lichkhoihanh 
                   SET TrangThai = 'Đang chạy' 
                   WHERE NgayKhoiHanh <= :today AND NgayKetThuc >= :today 
                   AND TrangThai IN ('Nhận khách', 'Đã đóng sổ', 'Đang chuẩn bị', 'Đang gom khách')";
        $this->conn->prepare($sqlRun)->execute(['today' => $today]);

        // 4. CHUYỂN SANG 'HOÀN TẤT'
        // Logic: Ngày kết thúc < Hôm nay
        $sqlFinish = "UPDATE lichkhoihanh 
                      SET TrangThai = 'Hoàn tất' 
                      WHERE NgayKetThuc < :today 
                      AND TrangThai NOT IN ('Hoàn tất', 'Đã hủy', 'Hủy chuyến')";
        $this->conn->prepare($sqlFinish)->execute(['today' => $today]);
    }

    public function clearScheduleNotifications($lichId) {
        // Link trong DB có dạng: /schedule/detail/ID
        // Ta dùng LIKE để tìm tất cả thông báo trỏ đến trang chi tiết của lịch này
        $linkCheck = "%/schedule/detail/$lichId%";
        
        $sql = "UPDATE thongbao 
                SET DaXem = 1 
                WHERE LienKet LIKE ? 
                AND LoaiThongBao = 'LichKhoiHanh' -- Chỉ tắt loại thông báo Lịch
                AND DaXem = 0";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$linkCheck]);
    }
    
}
?>