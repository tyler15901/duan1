<?php
require_once '../app/Core/Model.php';

class BookingModel extends Model
{

    // 1. Lấy danh sách đơn hàng (Có phân trang & Lọc)
    public function getAllBookings($filters = [], $limit = 10, $offset = 0)
    {
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

        $sql .= " ORDER BY b.MaBooking DESC LIMIT $limit OFFSET $offset"; // Sửa: Sắp xếp theo ID

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số lượng (để phân trang)
    public function countBookings($filters = [])
    {
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

    // 2. Lấy chi tiết 1 đơn hàng (ĐÃ SỬA JOIN BẢNG HDV)
    public function getBookingById($id)
    {
        $sql = "SELECT b.*, 
                       kh.HoTen as TenKhach, kh.SoDienThoai, kh.Email, kh.DiaChi,
                       t.TenTour, t.SoNgay, 
                       lkh.LichCode, lkh.NgayKhoiHanh, lkh.MaLichKhoiHanh,
                       lkh.GiaNguoiLon, lkh.GiaTreEm,
                       
                       ns.HoTen as TenHuongDanVien  -- Lấy tên từ bảng nhansu
                FROM booking b
                JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang
                JOIN lichkhoihanh lkh ON b.MaLichKhoiHanh = lkh.MaLichKhoiHanh
                JOIN tour t ON lkh.MaTour = t.MaTour
                
                LEFT JOIN nhansu ns ON b.MaHuongDanVien = ns.MaNhanSu -- SỬA TÊN BẢNG VÀ KHÓA CHÍNH
                
                WHERE b.MaBooking = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- [MỚI] LẤY DANH SÁCH HƯỚNG DẪN VIÊN ---
    public function getAllGuides()
    {
        // Sử dụng cột 'MaNhanSu' và 'PhanLoai'/'LoaiNhanSu' để filter HDV
        // Giả định dùng cột 'PhanLoai' có giá trị 'Hướng dẫn viên'
        $sql = "SELECT MaNhanSu, HoTen FROM nhansu 
                WHERE PhanLoai = 'Hướng dẫn viên' 
                AND TrangThai = 'Hoạt động'
                ORDER BY HoTen ASC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy lịch theo tour
    public function getSchedulesByTour($tourId)
    {
        $sql = "SELECT l.MaLichKhoiHanh, l.LichCode, l.NgayKhoiHanh, l.SoKhachHienTai, 
                       l.SoChoToiDa, 
                       l.GiaNguoiLon, 
                       l.GiaTreEm
                FROM lichkhoihanh l
                WHERE l.MaTour = ? 
                AND l.TrangThai = 'Nhận khách' 
                ORDER BY l.NgayKhoiHanh ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tourId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Lấy danh sách khách đi kèm
    public function getGuestList($bookingId)
    {
        $sql = "SELECT ct.*, kh.HoTen, kh.SoDienThoai
                FROM chitietkhachbooking ct
                JOIN khachhang kh ON ct.MaKhachHang = kh.MaKhachHang
                WHERE ct.MaBooking = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Tạo hoặc lấy thông tin khách hàng
    public function getOrCreateCustomer($name, $phone, $email = '', $address = '')
    {
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

    // 5. Tạo Booking mới
    public function createBookingReturnId($data)
    {
        // BƯỚC 1: Lấy ngày khởi hành
        $stmtDate = $this->conn->prepare("SELECT NgayKhoiHanh FROM lichkhoihanh WHERE MaLichKhoiHanh = ?");
        $stmtDate->execute([$data['lich']]);
        $ngayKhoiHanh = $stmtDate->fetchColumn();

        // BƯỚC 2: Insert có thêm SLNguoiLon, SLTreEm
        $sql = "INSERT INTO booking (
                    MaTour, MaLichKhoiHanh, MaKhachHang, 
                    NgayKhoiHanh, SoLuongKhach, TongTien, TienCoc, 
                    TrangThai, TrangThaiThanhToan, NgayDat, GhiChu,
                    SLNguoiLon, SLTreEm
                ) 
                VALUES (
                    :tour, :lich, :khach, 
                    :ngay_di, 
                    :sl, :tien, :coc, 
                    'Đã xác nhận', :tt_thanhtoan, NOW(), :ghichu,
                    :sl_nguoi_lon, :sl_tre_em
                )";

        $stmt = $this->conn->prepare($sql);

        // Chuẩn bị params chính xác 
        $params = [
            'tour' => $data['tour'],
            'lich' => $data['lich'],
            'khach' => $data['khach'],
            'ngay_di' => $ngayKhoiHanh,
            'sl' => $data['sl'],
            'tien' => $data['tien'],
            'coc' => $data['coc'],
            'tt_thanhtoan' => $data['tt_thanhtoan'],
            'ghichu' => $data['ghichu'],
            'sl_nguoi_lon' => $data['sl_nguoi_lon'],
            'sl_tre_em' => $data['sl_tre_em']
        ];

        if ($stmt->execute($params)) {
            $newId = $this->conn->lastInsertId();
            $code = 'BK' . date('Y') . str_pad($newId, 6, '0', STR_PAD_LEFT);
            $this->conn->prepare("UPDATE booking SET MaBookingCode = ? WHERE MaBooking = ?")
                ->execute([$code, $newId]);
            return $newId;
        }
        return false;
    }

    // 6. Thêm khách vào booking
    public function addGuestToBooking($bookingId, $data)
    {
        try {
            $this->conn->beginTransaction();

            $sqlKhach = "INSERT INTO khachhang (HoTen, SoDienThoai, SoGiayTo, NgayTao) 
                         VALUES (:hoten, :sdt, :giayto, NOW())";
            $stmt1 = $this->conn->prepare($sqlKhach);
            $stmt1->execute([
                'hoten' => $data['ho_ten'],
                'sdt' => $data['sdt'],
                'giayto' => $data['so_giay_to']
            ]);
            $newKhachId = $this->conn->lastInsertId();

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

    // 7. Cập nhật khách
    public function updateGuestInfo($guestDetailId, $data)
    {
        try {
            $this->conn->beginTransaction();
            $stmtGet = $this->conn->prepare("SELECT MaKhachHang FROM chitietkhachbooking WHERE MaChiTiet = ?");
            $stmtGet->execute([$guestDetailId]);
            $res = $stmtGet->fetch(PDO::FETCH_ASSOC);
            $maKhachHang = $res['MaKhachHang'];

            if ($maKhachHang) {
                $sqlKhach = "UPDATE khachhang SET HoTen = :ten, SoDienThoai = :sdt, SoGiayTo = :giayto WHERE MaKhachHang = :id";
                $this->conn->prepare($sqlKhach)->execute([
                    'ten' => $data['ho_ten'],
                    'sdt' => $data['sdt'],
                    'giayto' => $data['so_giay_to'],
                    'id' => $maKhachHang
                ]);
            }

            $sqlChiTiet = "UPDATE chitietkhachbooking SET LoaiKhach = :loai, SoGiayTo = :giayto, GhiChu = :note WHERE MaChiTiet = :id";
            $this->conn->prepare($sqlChiTiet)->execute([
                'loai' => $data['loai_khach'],
                'giayto' => $data['so_giay_to'],
                'note' => $data['ghi_chu'],
                'id' => $guestDetailId
            ]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // 8. Cập nhật thông tin đơn hàng (ĐÃ SỬA LƯU HDV)
    public function updateBooking($id, $data)
    {
        $sql = "UPDATE booking SET 
                TrangThai = :status, 
                TrangThaiThanhToan = :payment_status,
                TienCoc = :tien_coc,
                SLNguoiLon = :sl_nguoi_lon,
                SLTreEm = :sl_tre_em,
                SoLuongKhach = :so_luong_khach,
                TongTien = :tong_tien,
                GhiChu = :ghi_chu,
                MaHuongDanVien = :ma_hdv,
                MaLichKhoiHanh = :ma_lich_moi
                ";

        if (!empty($data['file'])) {
            $sql .= ", FileDanhSachKhach = :file";
        }
        $sql .= " WHERE MaBooking = :id";

        $params = [
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'tien_coc' => $data['tien_coc'],
            'sl_nguoi_lon' => $data['sl_nguoi_lon'],
            'sl_tre_em' => $data['sl_tre_em'],
            'so_luong_khach' => $data['so_luong_khach'],
            'tong_tien' => $data['tong_tien'],
            'ghi_chu' => $data['ghi_chu'],
            'ma_hdv' => $data['ma_hdv'],
            'ma_lich_moi' => $data['ma_lich_moi'],
            'id' => $id
        ];
        if (!empty($data['file'])) {
            $params['file'] = $data['file'];
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt->execute($params)) {
            
            // --- [MỚI] TỰ ĐỘNG ẨN THÔNG BÁO NẾU ĐÃ THANH TOÁN XONG ---
            // Nếu trạng thái chuyển sang "Đã thanh toán" -> Tìm thông báo cảnh báo cũ và đánh dấu Đã xem
            if ($data['payment_status'] == 'Đã thanh toán') {
                $linkCheck = "%/booking/detail/$id%"; // Tìm theo link của đơn hàng này
                
                // Cập nhật DaXem = 1 cho các thông báo "thanh toán" liên quan đến đơn này
                $sqlClean = "UPDATE thongbao 
                             SET DaXem = 1 
                             WHERE LienKet LIKE ? 
                             AND (TieuDe LIKE '%thanh toán%' OR TieuDe LIKE '%Cảnh báo%')
                             AND DaXem = 0";
                             
                $this->conn->prepare($sqlClean)->execute([$linkCheck]);
            }
            // ---------------------------------------------------------

            return true;
        }
        return false;
    }

    // 9. Xóa khách
    public function removeGuestFromBooking($maChiTiet)
    {
        $stmt = $this->conn->prepare("DELETE FROM chitietkhachbooking WHERE MaChiTiet = ?");
        return $stmt->execute([$maChiTiet]);
    }

    // Helper
    public function getActiveTours()
    {
        return $this->conn->query("SELECT MaTour, TenTour FROM tour WHERE TrangThai='Hoạt động'")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tự động tạo Lịch khởi hành mới từ thông tin Tour
    public function autoCreateScheduleFromTour($tourId, $date, $priceAdult = 0, $priceChild = 0)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tour WHERE MaTour = ?");
        $stmt->execute([$tourId]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tour) return false;

        // Tạo mã lịch: AUTO-IDTour-Date
        $dateCode = date('dmyt', strtotime($date));
        $lichCode = "AUTO-" . $tourId . "-" . $dateCode;

        // Kiểm tra tồn tại
        $check = $this->conn->prepare("SELECT MaLichKhoiHanh FROM lichkhoihanh WHERE MaTour = ? AND NgayKhoiHanh = ?");
        $check->execute([$tourId, $date]);
        $exist = $check->fetch(PDO::FETCH_ASSOC);
        if ($exist) return $exist['MaLichKhoiHanh'];

        // Insert Lịch mới (Trạng thái mặc định: Nhận khách)
        $sql = "INSERT INTO lichkhoihanh (
                MaTour, LichCode, NgayKhoiHanh, NgayKetThuc,
                GiaNguoiLon, GiaTreEm, 
                SoChoToiDa, SoChoMin,
                SoKhachHienTai, TrangThai, NgayTao
            ) VALUES (
                :tour_id, :code, :ngay_di, :ngay_ve,
                :gia_lon, :gia_tre,
                :so_cho_max, :so_cho_min,
                0, 'Nhận khách', NOW()
            )";

        $soNgay = (int) $tour['SoNgay'];
        $ngayVe = date('Y-m-d', strtotime($date . " + $soNgay days"));

        // Lấy giá mặc định từ tour nếu không nhập
        $finalPriceAdult = ($priceAdult > 0) ? $priceAdult : ($tour['GiaNguoiLon'] ?? 0);
        $finalPriceChild = ($priceChild > 0) ? $priceChild : ($tour['GiaTreEm'] ?? 0);

        $stmtInsert = $this->conn->prepare($sql);
        $result = $stmtInsert->execute([
            'tour_id' => $tourId,
            'code' => $lichCode,
            'ngay_di' => $date,
            'ngay_ve' => $ngayVe,
            'gia_lon' => $finalPriceAdult,
            'gia_tre' => $finalPriceChild,
            'so_cho_max' => 40, // Mặc định max
            'so_cho_min' => 20  // Mặc định min
        ]);

        if ($result) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // [CẬP NHẬT] Đồng bộ số khách & Tự động xử lý trạng thái HDV
    public function syncTotalGuests($bookingId)
    {
        // 1. Đếm lại tổng khách trong Booking này
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM chitietkhachbooking WHERE MaBooking = ?");
        $stmt->execute([$bookingId]);
        $guestCount = $stmt->fetchColumn();
        $this->conn->prepare("UPDATE booking SET SoLuongKhach = ? WHERE MaBooking = ?")->execute([$guestCount, $bookingId]);

        // 2. Lấy ID Lịch
        $stmtLich = $this->conn->prepare("SELECT MaLichKhoiHanh FROM booking WHERE MaBooking = ?");
        $stmtLich->execute([$bookingId]);
        $lichId = $stmtLich->fetchColumn();

        if ($lichId) {
            // 3. Tính tổng khách CỦA CẢ LỊCH (Chỉ tính đơn đã xác nhận/cọc/thanh toán)
            $sqlSum = "SELECT COALESCE(SUM(SoLuongKhach),0) FROM booking 
                   WHERE MaLichKhoiHanh = ? AND TrangThai IN ('Đã xác nhận', 'Đã thanh toán', 'Hoàn tất', 'Đã cọc')";
            $stmtSum = $this->conn->prepare($sqlSum);
            $stmtSum->execute([$lichId]);
            $totalPax = $stmtSum->fetchColumn();

            // 4. Update vào bảng Lịch
            $this->conn->prepare("UPDATE lichkhoihanh SET SoKhachHienTai = ? WHERE MaLichKhoiHanh = ?")
                ->execute([$totalPax, $lichId]);

            // 5. Lấy thông tin lịch để kiểm tra điều kiện
            $stmtInfo = $this->conn->prepare("SELECT LichCode, SoChoMin, TrangThai FROM lichkhoihanh WHERE MaLichKhoiHanh = ?");
            $stmtInfo->execute([$lichId]);
            $schedule = $stmtInfo->fetch(PDO::FETCH_ASSOC);

            if ($schedule) {
                // CASE A: Đủ khách -> Tạo thông báo (như cũ)
                if ($totalPax >= $schedule['SoChoMin'] && $schedule['TrangThai'] != 'Hủy chuyến') {
                    $this->createNotificationIfNeeded($lichId, $schedule['LichCode'], $totalPax, $schedule['SoChoMin']);
                }

                // CASE B: [QUAN TRỌNG] Tụt dưới mức Min -> Hủy gán HDV
                // Điều kiện: Khách < Min VÀ Trạng thái không phải là Đang chạy/Hoàn tất
                if ($totalPax < $schedule['SoChoMin'] && !in_array($schedule['TrangThai'], ['Đang chạy', 'Hoàn tất'])) {
                    
                    // 1. Xóa HDV khỏi bảng phân bổ
                    $this->conn->prepare("DELETE FROM phanbonhansu WHERE MaLichKhoiHanh = ? AND VaiTro = 'Hướng dẫn viên'")
                               ->execute([$lichId]);

                    // 2. Reset cột HDV trong các đơn hàng về NULL
                    $this->conn->prepare("UPDATE booking SET MaHuongDanVien = NULL WHERE MaLichKhoiHanh = ?")
                               ->execute([$lichId]);
                    
                    // 3. (Tùy chọn) Cập nhật trạng thái lịch về 'Nhận khách' hoặc 'Hủy chuyến' nếu cần
                    // Ví dụ: Nếu đang là 'Đã đóng sổ' mà tụt khách thì mở lại 'Nhận khách'
                    if ($schedule['TrangThai'] == 'Đã đóng sổ') {
                         $this->conn->prepare("UPDATE lichkhoihanh SET TrangThai = 'Nhận khách' WHERE MaLichKhoiHanh = ?")
                                    ->execute([$lichId]);
                    }
                }
            }
        }
    }

    // [MỚI] Hàm tạo thông báo (tránh trùng lặp)
    private function createNotificationIfNeeded($lichId, $code, $pax, $min) {
        // Kiểm tra xem đã có thông báo "đủ điều kiện" cho lịch này chưa
        $check = $this->conn->prepare("SELECT COUNT(*) FROM thongbao WHERE LienKet LIKE ? AND TieuDe LIKE '%đủ điều kiện%'");
        $linkCheck = "%/schedule/detail/$lichId%";
        $check->execute([$linkCheck]);
        
        if ($check->fetchColumn() == 0) {
            $title = "Lịch khởi hành [$code] đã đủ điều kiện!";
            $content = "Đạt $pax/$min khách (Min). Vui lòng cập nhật Tài nguyên (HDV, Xe, NCC) ngay.";
            $link = "/schedule/detail/$lichId";
            
            $ins = $this->conn->prepare("INSERT INTO thongbao (TieuDe, NoiDung, LoaiThongBao, LienKet, NgayTao) VALUES (?, ?, 'LichKhoiHanh', ?, NOW())");
            $ins->execute([$title, $content, $link]);
        }
    }

    // [MỚI] Hàm Tự động Cập nhật Trạng thái Lịch (State Machine)
    public function autoUpdateScheduleStatus($lichId)
    {
        $sql = "SELECT * FROM lichkhoihanh WHERE MaLichKhoiHanh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$lichId]);
        $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$schedule) return;
    
        $currentDate = date('Y-m-d');
        $startDate = $schedule['NgayKhoiHanh'];
        $endDate = $schedule['NgayKetThuc'];
        $pax = (int)$schedule['SoKhachHienTai'];
        $min = (int)$schedule['SoChoMin'];
        $max = (int)$schedule['SoChoToiDa'];
        
        $newStatus = $schedule['TrangThai']; 
        
        // --- [LOGIC MỚI] TỰ ĐỘNG HỦY GÁN HDV NẾU KHÁCH TỤT DƯỚI MIN ---
        // Nếu số khách hiện tại nhỏ hơn mức tối thiểu VÀ lịch chưa chạy/chưa hoàn tất
        if ($pax < $min && $schedule['TrangThai'] != 'Đang chạy' && $schedule['TrangThai'] != 'Hoàn tất') {
            
            // 1. Xóa HDV khỏi bảng phân bổ nhân sự (Giải phóng lịch cho HDV)
            $this->conn->prepare("DELETE FROM phanbonhansu WHERE MaLichKhoiHanh = ? AND VaiTro = 'Hướng dẫn viên'")
                       ->execute([$lichId]);

            // 2. Reset cột HDV trong bảng Booking về NULL (Để đơn hàng không hiện tên HDV nữa)
            $this->conn->prepare("UPDATE booking SET MaHuongDanVien = NULL WHERE MaLichKhoiHanh = ?")
                       ->execute([$lichId]);
        }
        // ----------------------------------------------------------------------

        // Tính khoảng cách ngày
        $diff = (strtotime($startDate) - strtotime($currentDate)) / (60 * 60 * 24); 
    
        if ($currentDate > $endDate) {
            $newStatus = 'Hoàn tất';
        } elseif ($currentDate >= $startDate && $currentDate <= $endDate) {
            $newStatus = 'Đang chạy';
        } else {
            // Mốc 1 ngày: Đóng sổ
            if ($diff <= 1) {
                $newStatus = 'Đã đóng sổ';
            } 
            // Mốc 2-5 ngày: Hủy nếu thiếu khách
            elseif ($diff <= 5) {
                if ($pax < $min) {
                    $newStatus = 'Hủy chuyến'; 
                } else {
                    $newStatus = ($pax >= $max) ? 'Đã đóng sổ' : 'Nhận khách';
                }
            } 
            // [MỚI] Mốc 6 ngày: Cảnh báo & Đóng sổ tạm để xử lý
            elseif ($diff <= 6) {
                if ($pax < $min) {
                    $newStatus = 'Đã đóng sổ';
                    // Tạo thông báo cảnh báo cho Admin
                    $this->createWarningNotification($lichId, $schedule['LichCode'], $pax, $min);
                } else {
                    $newStatus = ($pax >= $max) ? 'Đã đóng sổ' : 'Nhận khách';
                }
            }
            // Còn xa
            else {
                $newStatus = ($pax >= $max) ? 'Đã đóng sổ' : 'Nhận khách';
            }
        }
    
        if ($newStatus != $schedule['TrangThai']) {
            $upd = $this->conn->prepare("UPDATE lichkhoihanh SET TrangThai = ? WHERE MaLichKhoiHanh = ?");
            $upd->execute([$newStatus, $lichId]);
        }
    }

    // Lấy thông báo cho Dashboard
    // Lấy thông báo cho Dashboard
    public function getRecentNotifications($limit = 10) {
        // Sắp xếp: Chưa xem (0) lên trước, sau đó đến Mới nhất
        $sql = "SELECT * FROM thongbao ORDER BY DaXem ASC, NgayTao DESC LIMIT $limit";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hàm phụ trợ tạo cảnh báo (Bạn cũng thêm hàm này vào BookingModel nhé)
    private function createWarningNotification($lichId, $code, $pax, $min) {
        $link = "/schedule/guests/$lichId"; // Link xem danh sách khách
        $check = $this->conn->prepare("SELECT COUNT(*) FROM thongbao WHERE LienKet = ? AND TieuDe LIKE '%Cảnh báo%'");
        $check->execute([$link]);
        
        if ($check->fetchColumn() == 0) {
            $title = "⚠️ Cảnh báo: Lịch [$code] thiếu khách (T-6)";
            $content = "Còn 6 ngày. Hiện có $pax/$min khách. Vui lòng liên hệ Khách hàng để tư vấn.";
            $sql = "INSERT INTO thongbao (TieuDe, NoiDung, LoaiThongBao, LienKet, NgayTao) VALUES (?, ?, 'System', ?, NOW())";
            $this->conn->prepare($sql)->execute([$title, $content, $link]);
        }
    }

    // --- [BỔ SUNG] Lấy danh sách đơn hàng đã xác nhận của 1 lịch (Để chia HDV) ---
    public function getConfirmedBookingsBySchedule($lichId) {
        $sql = "SELECT MaBooking FROM booking 
                WHERE MaLichKhoiHanh = ? 
                AND TrangThai IN ('Đã xác nhận', 'Đã thanh toán', 'Hoàn tất', 'Đã cọc')"; 
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$lichId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- [BỔ SUNG] Cập nhật người phụ trách cho đơn hàng ---
    public function updateBookingGuide($bookingId, $guideId) {
        $sql = "UPDATE booking SET MaHuongDanVien = ? WHERE MaBooking = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$guideId, $bookingId]);
    }

    public function distributeGuidesForSchedule($lichId) {
        // 1. Lấy danh sách HDV đang được phân công cho lịch này
        $sqlGuide = "SELECT MaNhanSu FROM phanbonhansu WHERE MaLichKhoiHanh = ? AND VaiTro = 'Hướng dẫn viên'";
        $stmtGuide = $this->conn->prepare($sqlGuide);
        $stmtGuide->execute([$lichId]);
        $guideIds = $stmtGuide->fetchAll(PDO::FETCH_COLUMN);

        // Nếu lịch chưa được gán HDV thì dừng, không làm gì cả
        if (empty($guideIds)) return;

        // 2. Lấy tất cả booking "sống" của lịch này
        $bookings = $this->getConfirmedBookingsBySchedule($lichId);
        
        if (empty($bookings)) return;

        // 3. Thuật toán chia đều (Round Robin)
        $totalGuides = count($guideIds);
        $index = 0;

        foreach ($bookings as $booking) {
            // Lấy ID HDV theo vòng lặp
            $currentGuideId = $guideIds[$index % $totalGuides];
            
            // Cập nhật cho booking này
            $this->updateBookingGuide($booking['MaBooking'], $currentGuideId);
            
            $index++;
        }
    }

    public function markNotificationAsRead($notiId) {
    $sql = "UPDATE thongbao SET DaXem = 1 WHERE MaThongBao = :id";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute(['id' => $notiId]);
}
    public function getNotificationLink($notiId) {
        $stmt = $this->conn->prepare("SELECT LienKet FROM thongbao WHERE MaThongBao = ?");
        $stmt->execute([$notiId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function autoCheckPaymentOverdue() {
        $today = date('Y-m-d');
        
        // Điều kiện:
        // 1. Còn <= 5 ngày nữa đi (và chưa qua ngày đi)
        // 2. Trạng thái thanh toán là 'Chưa thanh toán' hoặc 'Đã cọc' (chưa Full)
        // 3. Đơn hàng vẫn đang hoạt động (Không phải Hủy hay Hoàn tất)
        $sql = "SELECT b.MaBooking, b.MaBookingCode, b.NgayKhoiHanh, b.TrangThaiThanhToan,
                       kh.HoTen, kh.SoDienThoai, b.TongTien, b.TienCoc
                FROM booking b
                JOIN khachhang kh ON b.MaKhachHang = kh.MaKhachHang
                WHERE DATEDIFF(b.NgayKhoiHanh, :today) <= 5 
                AND DATEDIFF(b.NgayKhoiHanh, :today) >= 0
                AND b.TrangThaiThanhToan IN ('Chưa thanh toán', 'Đã cọc')
                AND b.TrangThai NOT IN ('Đã hủy', 'Hoàn tất', 'Chờ xác nhận')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['today' => $today]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($list as $booking) {
            $this->createPaymentNotification($booking);
        }
    }

    // Hàm phụ trợ tạo thông báo đòi tiền
    private function createPaymentNotification($booking) {
        $bkId = $booking['MaBooking'];
        $code = $booking['MaBookingCode'];
        $link = "/booking/detail/$bkId"; 
        
        // [SỬA LẠI] Kiểm tra xem đã có thông báo NÀO CHƯA XEM cho đơn này chưa?
        // Nếu có rồi (DaXem = 0) thì KHÔNG tạo thêm nữa.
        $sqlCheck = "SELECT COUNT(*) FROM thongbao 
                     WHERE LienKet = ? 
                     AND TieuDe LIKE '%thanh toán%' 
                     AND DaXem = 0"; // Chỉ check những cái chưa xem
                     
        $check = $this->conn->prepare($sqlCheck);
        $check->execute([$link]);
        
        if ($check->fetchColumn() == 0) {
            // Tính số tiền còn thiếu
            $remain = $booking['TongTien'] - $booking['TienCoc'];
            $remainStr = number_format($remain);
            $ngayDi = date('d/m', strtotime($booking['NgayKhoiHanh']));
            
            $title = "Cảnh báo: Đơn $code sắp khởi hành ($ngayDi)";
            $content = "Khách {$booking['HoTen']} ({$booking['SoDienThoai']}) vẫn '{$booking['TrangThaiThanhToan']}'. Còn thiếu: $remainStr đ.";
            
            $sql = "INSERT INTO thongbao (TieuDe, NoiDung, LoaiThongBao, LienKet, DaXem, NgayTao) 
                    VALUES (?, ?, 'Booking', ?, 0, NOW())"; // DaXem = 0 (Vàng/Chưa xem)
            $this->conn->prepare($sql)->execute([$title, $content, $link]);
        }
    }

}
?>