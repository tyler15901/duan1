-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 14, 2025 at 02:45 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `duan1`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `MaBooking` int NOT NULL,
  `MaBookingCode` varchar(20) DEFAULT NULL,
  `MaTour` int NOT NULL,
  `MaKhachHang` int DEFAULT NULL,
  `NgayDat` date DEFAULT (curdate()),
  `NgayKhoiHanh` date NOT NULL,
  `SoLuongKhach` int NOT NULL,
  `TongTien` decimal(18,2) DEFAULT NULL,
  `TienCoc` decimal(18,2) DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT 'Chờ xác nhận',
  `NguoiTao` int DEFAULT NULL,
  `GhiChu` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `booking`
--
DELIMITER $$
CREATE TRIGGER `trg_Booking_Code` BEFORE INSERT ON `booking` FOR EACH ROW SET NEW.MaBookingCode = CONCAT('BK', YEAR(CURDATE()), LPAD(NEW.MaBooking, 6, '0'))
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_Update_Booking_TrangThai` AFTER UPDATE ON `booking` FOR EACH ROW IF OLD.TrangThai <> NEW.TrangThai OR OLD.SoLuongKhach <> NEW.SoLuongKhach THEN
    UPDATE LichKhoiHanh lkh
    JOIN (
        SELECT bl.MaLichKhoiHanh, COALESCE(SUM(b.SoLuongKhach),0) AS Tong
        FROM Booking_LichKhoiHanh bl
        JOIN Booking b ON bl.MaBooking = b.MaBooking AND b.TrangThai IN ('Đã thanh toán','Hoàn tất')
        WHERE bl.MaLichKhoiHanh IN (
            SELECT MaLichKhoiHanh FROM Booking_LichKhoiHanh WHERE MaBooking = NEW.MaBooking
        )
        GROUP BY bl.MaLichKhoiHanh
    ) t ON lkh.MaLichKhoiHanh = t.MaLichKhoiHanh
    SET lkh.SoKhachHienTai = t.Tong;
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `booking_lichkhoihanh`
--

CREATE TABLE `booking_lichkhoihanh` (
  `MaBooking` int NOT NULL,
  `MaLichKhoiHanh` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `booking_lichkhoihanh`
--
DELIMITER $$
CREATE TRIGGER `trg_Delete_Booking_Lich` AFTER DELETE ON `booking_lichkhoihanh` FOR EACH ROW BEGIN
    UPDATE LichKhoiHanh lkh
    JOIN (
        SELECT bl.MaLichKhoiHanh, COALESCE(SUM(b.SoLuongKhach),0) AS Tong
        FROM Booking_LichKhoiHanh bl
        JOIN Booking b ON bl.MaBooking = b.MaBooking AND b.TrangThai IN ('Đã thanh toán','Hoàn tất')
        WHERE bl.MaLichKhoiHanh = OLD.MaLichKhoiHanh
        GROUP BY bl.MaLichKhoiHanh
    ) t ON lkh.MaLichKhoiHanh = t.MaLichKhoiHanh
    SET lkh.SoKhachHienTai = t.Tong;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_Insert_Booking_Lich` AFTER INSERT ON `booking_lichkhoihanh` FOR EACH ROW BEGIN
    UPDATE LichKhoiHanh lkh
    JOIN (
        SELECT bl.MaLichKhoiHanh, COALESCE(SUM(b.SoLuongKhach),0) AS Tong
        FROM Booking_LichKhoiHanh bl
        JOIN Booking b ON bl.MaBooking = b.MaBooking AND b.TrangThai IN ('Đã thanh toán','Hoàn tất')
        WHERE bl.MaLichKhoiHanh = NEW.MaLichKhoiHanh
        GROUP BY bl.MaLichKhoiHanh
    ) t ON lkh.MaLichKhoiHanh = t.MaLichKhoiHanh
    SET lkh.SoKhachHienTai = t.Tong;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `checkinkhach`
--

CREATE TABLE `checkinkhach` (
  `MaCheckIn` int NOT NULL,
  `MaLichKhoiHanh` int NOT NULL,
  `MaChiTietKhach` int NOT NULL,
  `NgayCheckIn` date DEFAULT (curdate()),
  `GioCheckIn` time DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT 'Đã đến',
  `GhiChu` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chiphi`
--

CREATE TABLE `chiphi` (
  `MaChiPhi` int NOT NULL,
  `MaLichKhoiHanh` int NOT NULL,
  `LoaiChiPhi` varchar(100) NOT NULL,
  `MoTa` varchar(300) DEFAULT NULL,
  `SoTien` decimal(18,2) NOT NULL,
  `NgayChi` date DEFAULT (curdate()),
  `NguoiChi` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chitietkhachbooking`
--

CREATE TABLE `chitietkhachbooking` (
  `MaChiTiet` int NOT NULL,
  `MaBooking` int NOT NULL,
  `MaKhachHang` int NOT NULL,
  `LoaiKhach` varchar(20) DEFAULT 'Người lớn',
  `PhongKhachSan` varchar(50) DEFAULT NULL,
  `GhiChu` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `filedinhkem`
--

CREATE TABLE `filedinhkem` (
  `MaFile` int NOT NULL,
  `LoaiDoiTuong` varchar(50) NOT NULL,
  `MaDoiTuong` int NOT NULL,
  `TenFile` varchar(200) DEFAULT NULL,
  `DuongDan` varchar(500) NOT NULL,
  `NguoiUpload` int DEFAULT NULL,
  `NgayUpload` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `giatour`
--

CREATE TABLE `giatour` (
  `MaGia` int NOT NULL,
  `MaTour` int NOT NULL,
  `DoiTuong` varchar(50) DEFAULT 'Người lớn',
  `Gia` decimal(18,2) NOT NULL,
  `NgayBatDau` date NOT NULL,
  `NgayKetThuc` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goidichvu`
--

CREATE TABLE `goidichvu` (
  `MaGoi` int NOT NULL,
  `MaTour` int NOT NULL,
  `TenGoi` varchar(200) NOT NULL,
  `GiaThem` decimal(18,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hinhanhtour`
--

CREATE TABLE `hinhanhtour` (
  `MaHinhAnh` int NOT NULL,
  `MaTour` int NOT NULL,
  `DuongDan` varchar(500) NOT NULL,
  `LaAnhChinh` tinyint(1) DEFAULT '0',
  `ThuTu` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `MaKhachHang` int NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `GioiTinh` varchar(10) DEFAULT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `DiaChi` varchar(300) DEFAULT NULL,
  `SoGiayTo` varchar(20) DEFAULT NULL,
  `NgayTao` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lichkhoihanh`
--

CREATE TABLE `lichkhoihanh` (
  `MaLichKhoiHanh` int NOT NULL,
  `MaTour` int NOT NULL,
  `LichCode` varchar(20) DEFAULT NULL,
  `NgayKhoiHanh` date NOT NULL,
  `GioTapTrung` time DEFAULT NULL,
  `DiaDiemTapTrung` varchar(300) DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT 'Đang chuẩn bị',
  `SoKhachHienTai` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `lichkhoihanh`
--
DELIMITER $$
CREATE TRIGGER `trg_Lich_Code` BEFORE INSERT ON `lichkhoihanh` FOR EACH ROW SET NEW.LichCode = CONCAT('LKH', YEAR(NEW.NgayKhoiHanh), LPAD(NEW.MaLichKhoiHanh, 5, '0'))
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `lichtrinhtour`
--

CREATE TABLE `lichtrinhtour` (
  `MaLichTrinh` int NOT NULL,
  `MaTour` int NOT NULL,
  `NgayThu` int NOT NULL,
  `TieuDe` varchar(200) DEFAULT NULL,
  `NoiDung` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loaitour`
--

CREATE TABLE `loaitour` (
  `MaLoaiTour` int NOT NULL,
  `TenLoai` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MoTa` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ;

--
-- Dumping data for table `loaitour`
--

INSERT INTO `loaitour` (`MaLoaiTour`, `TenLoai`, `MoTa`) VALUES
(1, 'Tour trong nước', NULL),
(2, 'Tour quốc tế', NULL),
(3, 'Tour theo yêu cầu', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `nguoidung`
--

CREATE TABLE `nguoidung` (
  `MaNguoiDung` int NOT NULL,
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `VaiTro` enum('ADMIN','Nhân viên','HDV') NOT NULL,
  `MaNhanSu` int DEFAULT NULL,
  `Avatar` varchar(500) DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `nguoidung`
--

INSERT INTO `nguoidung` (`MaNguoiDung`, `TenDangNhap`, `MatKhau`, `HoTen`, `VaiTro`, `MaNhanSu`, `Avatar`, `TrangThai`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên', 'ADMIN', NULL, NULL, 'Hoạt động');

-- --------------------------------------------------------

--
-- Table structure for table `nhansu`
--

CREATE TABLE `nhansu` (
  `MaNhanSu` int NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `LoaiNhanSu` varchar(50) NOT NULL COMMENT 'HDV,Tài xế,Nhân viên',
  `TrangThai` varchar(50) DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nhatkytour`
--

CREATE TABLE `nhatkytour` (
  `MaNhatKy` int NOT NULL,
  `MaLichKhoiHanh` int NOT NULL,
  `MaNhanSu` int NOT NULL,
  `NgayGhi` date NOT NULL,
  `TieuDe` varchar(200) DEFAULT NULL,
  `NoiDung` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phanbonhansu`
--

CREATE TABLE `phanbonhansu` (
  `MaPhanBo` int NOT NULL,
  `MaLichKhoiHanh` int NOT NULL,
  `MaNhanSu` int NOT NULL,
  `VaiTro` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour`
--

CREATE TABLE `tour` (
  `MaTour` int NOT NULL,
  `MaLoaiTour` int NOT NULL,
  `TenTour` varchar(200) NOT NULL,
  `MoTa` text,
  `SoNgay` int NOT NULL,
  `SoChoToiDa` int DEFAULT '40',
  `TrangThai` varchar(50) DEFAULT 'Hoạt động',
  `NgayTao` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_baocaodoanhthu_thoigian`
-- (See below for the actual view)
--
CREATE TABLE `v_baocaodoanhthu_thoigian` (
`ChiPhi` decimal(40,2)
,`DoanhThu` decimal(40,2)
,`LoiNhuan` decimal(41,2)
,`Nam` year
,`Quy` int
,`SoDoan` bigint
,`Thang` int
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_baocaoloinhuan`
-- (See below for the actual view)
--
CREATE TABLE `v_baocaoloinhuan` (
`ChiPhi` decimal(40,2)
,`DoanhThu` decimal(40,2)
,`LichCode` varchar(20)
,`LoiNhuan` decimal(41,2)
,`NgayKhoiHanh` date
,`TenTour` varchar(200)
);

-- --------------------------------------------------------

--
-- Table structure for table `yeucaudacbiet`
--

CREATE TABLE `yeucaudacbiet` (
  `MaYeuCau` int NOT NULL,
  `MaChiTietKhach` int NOT NULL,
  `LoaiYeuCau` varchar(100) NOT NULL,
  `NoiDung` text NOT NULL,
  `MucDo` varchar(50) DEFAULT 'Trung bình',
  `TrangThaiXuLy` varchar(50) DEFAULT 'Chưa xử lý',
  `NguoiXuLy` int DEFAULT NULL,
  `ThoiGianXuLy` datetime DEFAULT NULL,
  `GhiChuXuLy` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`MaBooking`),
  ADD UNIQUE KEY `MaBookingCode` (`MaBookingCode`),
  ADD KEY `MaTour` (`MaTour`),
  ADD KEY `MaKhachHang` (`MaKhachHang`),
  ADD KEY `NguoiTao` (`NguoiTao`),
  ADD KEY `IX_Booking_Ngay` (`NgayKhoiHanh`);

--
-- Indexes for table `booking_lichkhoihanh`
--
ALTER TABLE `booking_lichkhoihanh`
  ADD PRIMARY KEY (`MaBooking`,`MaLichKhoiHanh`),
  ADD KEY `MaLichKhoiHanh` (`MaLichKhoiHanh`);

--
-- Indexes for table `checkinkhach`
--
ALTER TABLE `checkinkhach`
  ADD PRIMARY KEY (`MaCheckIn`),
  ADD KEY `MaLichKhoiHanh` (`MaLichKhoiHanh`),
  ADD KEY `MaChiTietKhach` (`MaChiTietKhach`);

--
-- Indexes for table `chiphi`
--
ALTER TABLE `chiphi`
  ADD PRIMARY KEY (`MaChiPhi`),
  ADD KEY `NguoiChi` (`NguoiChi`),
  ADD KEY `IX_ChiPhi_Lich` (`MaLichKhoiHanh`);

--
-- Indexes for table `chitietkhachbooking`
--
ALTER TABLE `chitietkhachbooking`
  ADD PRIMARY KEY (`MaChiTiet`),
  ADD KEY `MaBooking` (`MaBooking`),
  ADD KEY `MaKhachHang` (`MaKhachHang`);

--
-- Indexes for table `filedinhkem`
--
ALTER TABLE `filedinhkem`
  ADD PRIMARY KEY (`MaFile`),
  ADD KEY `NguoiUpload` (`NguoiUpload`),
  ADD KEY `IX_File` (`LoaiDoiTuong`,`MaDoiTuong`);

--
-- Indexes for table `giatour`
--
ALTER TABLE `giatour`
  ADD PRIMARY KEY (`MaGia`),
  ADD KEY `MaTour` (`MaTour`);

--
-- Indexes for table `goidichvu`
--
ALTER TABLE `goidichvu`
  ADD PRIMARY KEY (`MaGoi`),
  ADD KEY `MaTour` (`MaTour`);

--
-- Indexes for table `hinhanhtour`
--
ALTER TABLE `hinhanhtour`
  ADD PRIMARY KEY (`MaHinhAnh`),
  ADD KEY `MaTour` (`MaTour`);

--
-- Indexes for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MaKhachHang`);

--
-- Indexes for table `lichkhoihanh`
--
ALTER TABLE `lichkhoihanh`
  ADD PRIMARY KEY (`MaLichKhoiHanh`),
  ADD UNIQUE KEY `LichCode` (`LichCode`),
  ADD KEY `MaTour` (`MaTour`),
  ADD KEY `IX_Lich_TrangThai` (`TrangThai`);

--
-- Indexes for table `lichtrinhtour`
--
ALTER TABLE `lichtrinhtour`
  ADD PRIMARY KEY (`MaLichTrinh`),
  ADD KEY `MaTour` (`MaTour`);

--
-- Indexes for table `loaitour`
--
ALTER TABLE `loaitour`
  ADD PRIMARY KEY (`MaLoaiTour`);

--
-- Indexes for table `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`MaNguoiDung`),
  ADD UNIQUE KEY `TenDangNhap` (`TenDangNhap`),
  ADD KEY `MaNhanSu` (`MaNhanSu`);

--
-- Indexes for table `nhansu`
--
ALTER TABLE `nhansu`
  ADD PRIMARY KEY (`MaNhanSu`);

--
-- Indexes for table `nhatkytour`
--
ALTER TABLE `nhatkytour`
  ADD PRIMARY KEY (`MaNhatKy`),
  ADD KEY `MaLichKhoiHanh` (`MaLichKhoiHanh`),
  ADD KEY `MaNhanSu` (`MaNhanSu`);

--
-- Indexes for table `phanbonhansu`
--
ALTER TABLE `phanbonhansu`
  ADD PRIMARY KEY (`MaPhanBo`),
  ADD KEY `MaLichKhoiHanh` (`MaLichKhoiHanh`),
  ADD KEY `MaNhanSu` (`MaNhanSu`);

--
-- Indexes for table `tour`
--
ALTER TABLE `tour`
  ADD PRIMARY KEY (`MaTour`),
  ADD KEY `MaLoaiTour` (`MaLoaiTour`);

--
-- Indexes for table `yeucaudacbiet`
--
ALTER TABLE `yeucaudacbiet`
  ADD PRIMARY KEY (`MaYeuCau`),
  ADD KEY `MaChiTietKhach` (`MaChiTietKhach`),
  ADD KEY `NguoiXuLy` (`NguoiXuLy`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `MaBooking` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `checkinkhach`
--
ALTER TABLE `checkinkhach`
  MODIFY `MaCheckIn` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chiphi`
--
ALTER TABLE `chiphi`
  MODIFY `MaChiPhi` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chitietkhachbooking`
--
ALTER TABLE `chitietkhachbooking`
  MODIFY `MaChiTiet` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `filedinhkem`
--
ALTER TABLE `filedinhkem`
  MODIFY `MaFile` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `giatour`
--
ALTER TABLE `giatour`
  MODIFY `MaGia` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goidichvu`
--
ALTER TABLE `goidichvu`
  MODIFY `MaGoi` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hinhanhtour`
--
ALTER TABLE `hinhanhtour`
  MODIFY `MaHinhAnh` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `MaKhachHang` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lichkhoihanh`
--
ALTER TABLE `lichkhoihanh`
  MODIFY `MaLichKhoiHanh` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lichtrinhtour`
--
ALTER TABLE `lichtrinhtour`
  MODIFY `MaLichTrinh` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loaitour`
--
ALTER TABLE `loaitour`
  MODIFY `MaLoaiTour` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `MaNguoiDung` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `nhansu`
--
ALTER TABLE `nhansu`
  MODIFY `MaNhanSu` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nhatkytour`
--
ALTER TABLE `nhatkytour`
  MODIFY `MaNhatKy` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phanbonhansu`
--
ALTER TABLE `phanbonhansu`
  MODIFY `MaPhanBo` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour`
--
ALTER TABLE `tour`
  MODIFY `MaTour` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `yeucaudacbiet`
--
ALTER TABLE `yeucaudacbiet`
  MODIFY `MaYeuCau` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `v_baocaodoanhthu_thoigian`
--
DROP TABLE IF EXISTS `v_baocaodoanhthu_thoigian`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_baocaodoanhthu_thoigian`  AS SELECT year(`lkh`.`NgayKhoiHanh`) AS `Nam`, month(`lkh`.`NgayKhoiHanh`) AS `Thang`, quarter(`lkh`.`NgayKhoiHanh`) AS `Quy`, count(distinct `lkh`.`MaLichKhoiHanh`) AS `SoDoan`, coalesce(sum(`b`.`TongTien`),0) AS `DoanhThu`, coalesce(sum(`cp`.`SoTien`),0) AS `ChiPhi`, (coalesce(sum(`b`.`TongTien`),0) - coalesce(sum(`cp`.`SoTien`),0)) AS `LoiNhuan` FROM (((`lichkhoihanh` `lkh` left join `booking_lichkhoihanh` `bl` on((`lkh`.`MaLichKhoiHanh` = `bl`.`MaLichKhoiHanh`))) left join `booking` `b` on(((`bl`.`MaBooking` = `b`.`MaBooking`) and (`b`.`TrangThai` in ('Hoàn tất','Đã thanh toán'))))) left join `chiphi` `cp` on((`lkh`.`MaLichKhoiHanh` = `cp`.`MaLichKhoiHanh`))) GROUP BY year(`lkh`.`NgayKhoiHanh`), month(`lkh`.`NgayKhoiHanh`) ORDER BY `Nam` DESC, `Thang` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_baocaoloinhuan`
--
DROP TABLE IF EXISTS `v_baocaoloinhuan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_baocaoloinhuan`  AS SELECT `lkh`.`LichCode` AS `LichCode`, `t`.`TenTour` AS `TenTour`, `lkh`.`NgayKhoiHanh` AS `NgayKhoiHanh`, coalesce(sum(`b`.`TongTien`),0) AS `DoanhThu`, coalesce(sum(`cp`.`SoTien`),0) AS `ChiPhi`, (coalesce(sum(`b`.`TongTien`),0) - coalesce(sum(`cp`.`SoTien`),0)) AS `LoiNhuan` FROM ((((`lichkhoihanh` `lkh` join `tour` `t` on((`lkh`.`MaTour` = `t`.`MaTour`))) left join `booking_lichkhoihanh` `bl` on((`lkh`.`MaLichKhoiHanh` = `bl`.`MaLichKhoiHanh`))) left join `booking` `b` on(((`bl`.`MaBooking` = `b`.`MaBooking`) and (`b`.`TrangThai` in ('Hoàn tất','Đã thanh toán'))))) left join `chiphi` `cp` on((`lkh`.`MaLichKhoiHanh` = `cp`.`MaLichKhoiHanh`))) GROUP BY `lkh`.`LichCode`, `t`.`TenTour`, `lkh`.`NgayKhoiHanh` ORDER BY `lkh`.`NgayKhoiHanh` DESC ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`MaTour`) REFERENCES `tour` (`MaTour`) ON DELETE RESTRICT,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`MaKhachHang`) REFERENCES `khachhang` (`MaKhachHang`) ON DELETE SET NULL,
  ADD CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`NguoiTao`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE SET NULL;

--
-- Constraints for table `booking_lichkhoihanh`
--
ALTER TABLE `booking_lichkhoihanh`
  ADD CONSTRAINT `booking_lichkhoihanh_ibfk_1` FOREIGN KEY (`MaBooking`) REFERENCES `booking` (`MaBooking`) ON DELETE RESTRICT,
  ADD CONSTRAINT `booking_lichkhoihanh_ibfk_2` FOREIGN KEY (`MaLichKhoiHanh`) REFERENCES `lichkhoihanh` (`MaLichKhoiHanh`) ON DELETE RESTRICT;

--
-- Constraints for table `checkinkhach`
--
ALTER TABLE `checkinkhach`
  ADD CONSTRAINT `checkinkhach_ibfk_1` FOREIGN KEY (`MaLichKhoiHanh`) REFERENCES `lichkhoihanh` (`MaLichKhoiHanh`) ON DELETE CASCADE,
  ADD CONSTRAINT `checkinkhach_ibfk_2` FOREIGN KEY (`MaChiTietKhach`) REFERENCES `chitietkhachbooking` (`MaChiTiet`) ON DELETE RESTRICT;

--
-- Constraints for table `chiphi`
--
ALTER TABLE `chiphi`
  ADD CONSTRAINT `chiphi_ibfk_1` FOREIGN KEY (`MaLichKhoiHanh`) REFERENCES `lichkhoihanh` (`MaLichKhoiHanh`) ON DELETE CASCADE,
  ADD CONSTRAINT `chiphi_ibfk_2` FOREIGN KEY (`NguoiChi`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE SET NULL;

--
-- Constraints for table `chitietkhachbooking`
--
ALTER TABLE `chitietkhachbooking`
  ADD CONSTRAINT `chitietkhachbooking_ibfk_1` FOREIGN KEY (`MaBooking`) REFERENCES `booking` (`MaBooking`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietkhachbooking_ibfk_2` FOREIGN KEY (`MaKhachHang`) REFERENCES `khachhang` (`MaKhachHang`) ON DELETE RESTRICT;

--
-- Constraints for table `filedinhkem`
--
ALTER TABLE `filedinhkem`
  ADD CONSTRAINT `filedinhkem_ibfk_1` FOREIGN KEY (`NguoiUpload`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE SET NULL;

--
-- Constraints for table `giatour`
--
ALTER TABLE `giatour`
  ADD CONSTRAINT `giatour_ibfk_1` FOREIGN KEY (`MaTour`) REFERENCES `tour` (`MaTour`) ON DELETE CASCADE;

--
-- Constraints for table `goidichvu`
--
ALTER TABLE `goidichvu`
  ADD CONSTRAINT `goidichvu_ibfk_1` FOREIGN KEY (`MaTour`) REFERENCES `tour` (`MaTour`) ON DELETE CASCADE;

--
-- Constraints for table `hinhanhtour`
--
ALTER TABLE `hinhanhtour`
  ADD CONSTRAINT `hinhanhtour_ibfk_1` FOREIGN KEY (`MaTour`) REFERENCES `tour` (`MaTour`) ON DELETE CASCADE;

--
-- Constraints for table `lichkhoihanh`
--
ALTER TABLE `lichkhoihanh`
  ADD CONSTRAINT `lichkhoihanh_ibfk_1` FOREIGN KEY (`MaTour`) REFERENCES `tour` (`MaTour`) ON DELETE RESTRICT;

--
-- Constraints for table `lichtrinhtour`
--
ALTER TABLE `lichtrinhtour`
  ADD CONSTRAINT `lichtrinhtour_ibfk_1` FOREIGN KEY (`MaTour`) REFERENCES `tour` (`MaTour`) ON DELETE CASCADE;

--
-- Constraints for table `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD CONSTRAINT `nguoidung_ibfk_1` FOREIGN KEY (`MaNhanSu`) REFERENCES `nhansu` (`MaNhanSu`) ON DELETE SET NULL;

--
-- Constraints for table `nhatkytour`
--
ALTER TABLE `nhatkytour`
  ADD CONSTRAINT `nhatkytour_ibfk_1` FOREIGN KEY (`MaLichKhoiHanh`) REFERENCES `lichkhoihanh` (`MaLichKhoiHanh`) ON DELETE CASCADE,
  ADD CONSTRAINT `nhatkytour_ibfk_2` FOREIGN KEY (`MaNhanSu`) REFERENCES `nhansu` (`MaNhanSu`) ON DELETE RESTRICT;

--
-- Constraints for table `phanbonhansu`
--
ALTER TABLE `phanbonhansu`
  ADD CONSTRAINT `phanbonhansu_ibfk_1` FOREIGN KEY (`MaLichKhoiHanh`) REFERENCES `lichkhoihanh` (`MaLichKhoiHanh`) ON DELETE CASCADE,
  ADD CONSTRAINT `phanbonhansu_ibfk_2` FOREIGN KEY (`MaNhanSu`) REFERENCES `nhansu` (`MaNhanSu`) ON DELETE RESTRICT;

--
-- Constraints for table `tour`
--
ALTER TABLE `tour`
  ADD CONSTRAINT `tour_ibfk_1` FOREIGN KEY (`MaLoaiTour`) REFERENCES `loaitour` (`MaLoaiTour`) ON DELETE RESTRICT;

--
-- Constraints for table `yeucaudacbiet`
--
ALTER TABLE `yeucaudacbiet`
  ADD CONSTRAINT `yeucaudacbiet_ibfk_1` FOREIGN KEY (`MaChiTietKhach`) REFERENCES `chitietkhachbooking` (`MaChiTiet`) ON DELETE CASCADE,
  ADD CONSTRAINT `yeucaudacbiet_ibfk_2` FOREIGN KEY (`NguoiXuLy`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
