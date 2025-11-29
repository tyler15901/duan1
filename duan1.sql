-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 29, 2025 at 06:48 AM
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
  `MaLichKhoiHanh` int NOT NULL,
  `MaTour` int NOT NULL,
  `MaKhachHang` int DEFAULT NULL,
  `NgayDat` date DEFAULT (curdate()),
  `NgayKhoiHanh` date NOT NULL,
  `SoLuongKhach` int NOT NULL,
  `TongTien` decimal(18,2) DEFAULT NULL,
  `TienCoc` decimal(18,2) DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT 'Chờ xác nhận',
  `TrangThaiThanhToan` varchar(50) DEFAULT 'Chưa thanh toán',
  `NguoiTao` int DEFAULT NULL,
  `GhiChu` text,
  `FileDanhSachKhach` varchar(500) DEFAULT NULL COMMENT 'Lưu đường dẫn file Excel/PDF'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`MaBooking`, `MaBookingCode`, `MaLichKhoiHanh`, `MaTour`, `MaKhachHang`, `NgayDat`, `NgayKhoiHanh`, `SoLuongKhach`, `TongTien`, `TienCoc`, `TrangThai`, `TrangThaiThanhToan`, `NguoiTao`, `GhiChu`, `FileDanhSachKhach`) VALUES
(1, 'BK2025000000', 0, 3, 1, '2025-11-21', '2025-12-01', 1, 5990000.00, 0.00, 'Đã xác nhận', 'Chưa thanh toán', NULL, '13311331 (Người lớn: 1, Trẻ em: 0)', NULL),
(4, 'BK2025000004', 1, 1, 3, '2025-11-28', '2025-11-26', 2, 5000000.00, 0.00, 'Chờ xác nhận', 'Chưa thanh toán', NULL, 'Khách yêu cầu ghế đầu xe', NULL),
(5, 'BK2025000005', 4, 2, 4, '2025-11-26', '2025-11-28', 4, 18000000.00, 5000000.00, 'Đã xác nhận', 'Đã cọc', NULL, 'Đoàn có 1 trẻ em 5 tuổi', NULL),
(6, 'BK2025000006', 7, 3, 5, '2025-11-18', '2025-12-01', 1, 5990000.00, 5990000.00, 'Đã xác nhận', 'Đã thanh toán', NULL, 'Khách VIP, xếp phòng view biển', NULL),
(7, 'BK2025000007', 1, 1, 6, '2025-11-23', '2025-11-26', 2, 5000000.00, 0.00, 'Đã hủy', 'Chưa thanh toán', NULL, 'Khách bận việc đột xuất', NULL),
(8, 'BK2025000008', 4, 2, 7, '2025-11-28', '2025-11-28', 1, 4500000.00, 0.00, 'Đã xác nhận', 'Chưa thanh toán', NULL, NULL, NULL);

--
-- Triggers `booking`
--
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
  `SoGiayTo` varchar(50) DEFAULT NULL COMMENT 'CCCD/Passport',
  `PhongKhachSan` varchar(50) DEFAULT NULL,
  `GhiChu` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chitietkhachbooking`
--

INSERT INTO `chitietkhachbooking` (`MaChiTiet`, `MaBooking`, `MaKhachHang`, `LoaiKhach`, `SoGiayTo`, `PhongKhachSan`, `GhiChu`) VALUES
(1, 4, 3, 'Người lớn', '001099001234', NULL, 'Trưởng đoàn'),
(2, 1, 1, 'Người lớn', '079088005678', NULL, 'Ăn chay');

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

--
-- Dumping data for table `giatour`
--

INSERT INTO `giatour` (`MaGia`, `MaTour`, `DoiTuong`, `Gia`, `NgayBatDau`, `NgayKetThuc`) VALUES
(1, 1, 'Người lớn', 2500000.00, '2024-01-01', '2025-12-31'),
(2, 1, 'Trẻ em', 1250000.00, '2024-01-01', '2025-12-31'),
(3, 2, 'Người lớn', 4500000.00, '2024-01-01', '2025-12-31'),
(4, 2, 'Trẻ em', 2250000.00, '2024-01-01', '2025-12-31'),
(5, 3, 'Người lớn', 5990000.00, '2024-01-01', '2025-12-31'),
(6, 3, 'Trẻ em', 3000000.00, '2024-01-01', '2025-12-31'),
(7, 4, 'Người lớn', 12900000.00, '2024-01-01', '2025-12-31'),
(8, 4, 'Trẻ em', 10900000.00, '2024-01-01', '2025-12-31'),
(9, 5, 'Người lớn', 15500000.00, '2024-01-01', '2025-12-31'),
(10, 6, 'Người lớn', 0.00, '2024-01-01', '2025-12-31'),
(11, 7, 'Người lớn', 2222222222222.00, '2025-11-28', '2026-11-28'),
(12, 7, 'Trẻ em', 22222222222.00, '2025-11-28', '2026-11-28');

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
  `DuongDan` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hinhanhtour`
--

INSERT INTO `hinhanhtour` (`MaHinhAnh`, `MaTour`, `DuongDan`) VALUES
(1, 4, 'anhtour/hanquoc/seoul/seoul1.jpg'),
(2, 4, 'anhtour/hanquoc/seoul/seoul2.jpg'),
(3, 4, 'anhtour/hanquoc/seoul/seoul3.jpg'),
(4, 4, 'anhtour/hanquoc/seoul/seoul4.jpg'),
(5, 4, 'anhtour/hanquoc/seoul/seoul5.jpg'),
(6, 5, 'anhtour/trungquoc/backinh/backinh1.jpg'),
(7, 5, 'anhtour/trungquoc/backinh/backinh2.jpg'),
(8, 5, 'anhtour/trungquoc/backinh/backinh3.jpg'),
(9, 5, 'anhtour/trungquoc/backinh/backinh4.jpg'),
(10, 5, 'anhtour/trungquoc/backinh/backinh5.jpg'),
(11, 5, 'anhtour/trungquoc/backinh/backinh6.jpg'),
(23, 1, 'anhtour/vietnam/mienbac/yenbai/yenbai1.jpg'),
(24, 1, 'anhtour/vietnam/mienbac/yenbai/yenbai2.jpg'),
(25, 1, 'anhtour/vietnam/mienbac/yenbai/yenbai3.jpg'),
(26, 1, 'anhtour/vietnam/mienbac/yenbai/yenbai4.jpg'),
(27, 1, 'anhtour/vietnam/mienbac/yenbai/yenbai5.jpg'),
(28, 1, 'anhtour/vietnam/mienbac/yenbai/yenbai6.jpg'),
(29, 7, 'assets/uploads/1764314071_0_backinh1.jpg'),
(30, 7, 'assets/uploads/1764314071_1_backinh2.jpg'),
(31, 7, 'assets/uploads/1764314071_2_backinh3.jpg'),
(32, 7, 'assets/uploads/1764314071_3_backinh4.jpg');

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

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`MaKhachHang`, `HoTen`, `GioiTinh`, `SoDienThoai`, `Email`, `DiaChi`, `SoGiayTo`, `NgayTao`) VALUES
(1, 'aaaa', NULL, '0822501239', 'quangthai15901@gmail.com', '133113311331', NULL, '2025-11-21 14:49:02'),
(2, 'thuan', NULL, '0822501239', 'quangthai15901@gmail.com', 'hanoi', NULL, '2025-11-26 18:25:28'),
(3, 'Nguyễn Văn An', NULL, '0901234567', 'an.nguyen@gmail.com', '123 Cầu Giấy, Hà Nội', NULL, '2025-11-28 15:47:37'),
(4, 'Trần Thị Bích', NULL, '0912345678', 'bich.tran@yahoo.com', '45 Lê Lợi, Đà Nẵng', NULL, '2025-11-28 15:47:37'),
(5, 'Lê Hoàng Nam', NULL, '0988777666', 'nam.le@company.com', '10 Trần Hưng Đạo, HCM', NULL, '2025-11-28 15:47:37'),
(6, 'Phạm Thu Hà', NULL, '0905555444', 'ha.pham@gmail.com', '12 Nguyễn Văn Cừ, Hà Nội', NULL, '2025-11-28 15:47:37'),
(7, 'Hoàng Tuấn Kiệt', NULL, '0344555666', 'kiet.hoang@outlook.com', '88 Láng Hạ, Hà Nội', NULL, '2025-11-28 15:47:37'),
(8, 'Nguyễn Văn An', NULL, '0901234567', 'an.nguyen@gmail.com', '123 Cầu Giấy, Hà Nội', NULL, '2025-11-28 15:48:37'),
(9, 'Trần Thị Bích', NULL, '0912345678', 'bich.tran@yahoo.com', '45 Lê Lợi, Đà Nẵng', NULL, '2025-11-28 15:48:37'),
(10, 'Lê Hoàng Nam', NULL, '0988777666', 'nam.le@company.com', '10 Trần Hưng Đạo, HCM', NULL, '2025-11-28 15:48:37'),
(11, 'Phạm Thu Hà', NULL, '0905555444', 'ha.pham@gmail.com', '12 Nguyễn Văn Cừ, Hà Nội', NULL, '2025-11-28 15:48:37'),
(12, 'Hoàng Tuấn Kiệt', NULL, '0344555666', 'kiet.hoang@outlook.com', '88 Láng Hạ, Hà Nội', NULL, '2025-11-28 15:48:37');

-- --------------------------------------------------------

--
-- Table structure for table `lichkhoihanh`
--

CREATE TABLE `lichkhoihanh` (
  `MaLichKhoiHanh` int NOT NULL,
  `MaTour` int NOT NULL,
  `LichCode` varchar(20) DEFAULT NULL,
  `NgayKhoiHanh` date NOT NULL,
  `NgayKetThuc` date DEFAULT NULL,
  `GioTapTrung` time DEFAULT NULL,
  `DiaDiemTapTrung` varchar(300) DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT 'Đang chuẩn bị',
  `SoKhachHienTai` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lichkhoihanh`
--

INSERT INTO `lichkhoihanh` (`MaLichKhoiHanh`, `MaTour`, `LichCode`, `NgayKhoiHanh`, `NgayKetThuc`, `GioTapTrung`, `DiaDiemTapTrung`, `TrangThai`, `SoKhachHienTai`) VALUES
(1, 1, 'LKH202500001', '2025-11-26', '2025-11-28', '05:30:00', 'Nhà Hát Lớn, Hà Nội', 'Nhận khách', 2),
(2, 1, 'LKH202500002', '2025-12-03', '2025-12-05', '05:30:00', 'Trung tâm Hội nghị Quốc gia', 'Nhận khách', 0),
(3, 1, 'LKH202500003', '2025-12-10', '2025-12-12', '06:00:00', 'Sân bay Nội Bài (Sảnh E)', 'Nhận khách', 10),
(4, 2, 'LKH202500004', '2025-11-28', '2025-12-01', '19:00:00', 'Ga Hà Nội (Cửa Lê Duẩn)', 'Nhận khách', 5),
(5, 2, 'LKH202500005', '2025-12-05', '2025-12-08', '19:00:00', 'Ga Hà Nội (Cửa Trần Quý Cáp)', 'Nhận khách', 0),
(6, 3, 'LKH202500006', '2025-11-24', '2025-11-26', '07:00:00', 'Sân bay Tân Sơn Nhất (Ga Quốc nội)', 'Sắp đóng', 28),
(7, 3, 'LKH202500007', '2025-12-01', '2025-12-03', '08:30:00', 'Sân bay Nội Bài (Sảnh A)', 'Nhận khách', 0),
(8, 4, 'LKH202500008', '2025-12-11', '2025-12-15', '22:00:00', 'Sân bay Nội Bài (Ga Quốc tế T2 - Cột 10)', 'Nhận khách', 15),
(9, 5, 'LKH202500009', '2025-12-16', '2025-12-20', '09:00:00', 'Sân bay Tân Sơn Nhất (Ga Quốc tế)', 'Nhận khách', 5);

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

--
-- Dumping data for table `lichtrinhtour`
--

INSERT INTO `lichtrinhtour` (`MaLichTrinh`, `MaTour`, `NgayThu`, `TieuDe`, `NoiDung`) VALUES
(1, 1, 1, 'Hà Nội - Nghĩa Lộ', 'Xe đón đoàn khởi hành đi Nghĩa Lộ. Ăn trưa, nhận phòng.'),
(2, 1, 2, 'Mù Cang Chải - Ruộng Bậc Thang', 'Tham quan đồi Mâm Xôi, chụp ảnh mùa lúa chín.'),
(3, 1, 3, 'Tú Lệ - Hà Nội', 'Thưởng thức cốm Tú Lệ, mua quà và trở về Hà Nội.'),
(4, 7, 1, 'aaaaaaaaaaa', 'aaaaaaaaaaaaaa');

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
  `VaiTro` enum('ADMIN','KhachHang','HDV') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `MaNhanSu` int DEFAULT NULL,
  `Avatar` varchar(500) DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `nguoidung`
--

INSERT INTO `nguoidung` (`MaNguoiDung`, `TenDangNhap`, `MatKhau`, `HoTen`, `VaiTro`, `MaNhanSu`, `Avatar`, `TrangThai`) VALUES
(1, 'admin', '123456', '', 'ADMIN', NULL, NULL, 'Hoạt động'),
(2, 'huongdanvien', '123456', 'Hướng dẫn viên', 'HDV', NULL, NULL, 'Hoạt động');

-- --------------------------------------------------------

--
-- Table structure for table `nhansu`
--

CREATE TABLE `nhansu` (
  `MaNhanSu` int NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `AnhDaiDien` varchar(255) DEFAULT NULL,
  `LoaiNhanSu` varchar(50) DEFAULT 'HDV',
  `PhanLoai` varchar(50) DEFAULT 'Tour trong nước' COMMENT 'Tour trong nước, Quốc tế, Theo yêu cầu',
  `TrangThai` varchar(50) DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `nhansu`
--

INSERT INTO `nhansu` (`MaNhanSu`, `HoTen`, `NgaySinh`, `SoDienThoai`, `Email`, `DiaChi`, `AnhDaiDien`, `LoaiNhanSu`, `PhanLoai`, `TrangThai`) VALUES
(1, 'Nguyễn Văn Hùng', NULL, '0988111222', 'hungnv@tourviet.com', NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(2, 'Trần Thị Mai', NULL, '0977333444', 'maitt@tourviet.com', NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(3, 'Lê Tuấn Anh', NULL, '0912555666', 'anhlt@tourviet.com', NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(4, 'Phạm Thùy Linh', NULL, '0909777888', 'linhpt@tourviet.com', NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động');

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
-- Table structure for table `nha_cung_cap`
--

CREATE TABLE `nha_cung_cap` (
  `MaNhaCungCap` int NOT NULL,
  `TenNhaCungCap` varchar(200) NOT NULL,
  `LoaiCungCap` varchar(50) NOT NULL COMMENT 'Vận chuyển, Lưu trú, Ăn uống',
  `DiaChi` varchar(300) DEFAULT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `nha_cung_cap`
--

INSERT INTO `nha_cung_cap` (`MaNhaCungCap`, `TenNhaCungCap`, `LoaiCungCap`, `DiaChi`, `SoDienThoai`, `TrangThai`) VALUES
(1, 'Nhà xe Thành Bưởi', 'Vận chuyển', NULL, NULL, 'Hoạt động'),
(2, 'Khách sạn Mường Thanh', 'Lưu trú', 'aaaaaaaaaaaaaa', '77777777777777', 'Hoạt động');

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

--
-- Dumping data for table `phanbonhansu`
--

INSERT INTO `phanbonhansu` (`MaPhanBo`, `MaLichKhoiHanh`, `MaNhanSu`, `VaiTro`) VALUES
(1, 9, 1, 'Hướng dẫn viên');

-- --------------------------------------------------------

--
-- Table structure for table `phan_bo_tai_nguyen`
--

CREATE TABLE `phan_bo_tai_nguyen` (
  `MaPhanBo` int NOT NULL,
  `MaLichKhoiHanh` int NOT NULL,
  `MaTaiNguyen` int NOT NULL,
  `NgaySuDung` date DEFAULT NULL,
  `GhiChu` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `phan_bo_tai_nguyen`
--

INSERT INTO `phan_bo_tai_nguyen` (`MaPhanBo`, `MaLichKhoiHanh`, `MaTaiNguyen`, `NgaySuDung`, `GhiChu`) VALUES
(1, 9, 2, NULL, NULL),
(2, 9, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tai_nguyen_ncc`
--

CREATE TABLE `tai_nguyen_ncc` (
  `MaTaiNguyen` int NOT NULL,
  `MaNhaCungCap` int NOT NULL,
  `TenTaiNguyen` varchar(200) NOT NULL COMMENT 'VD: Xe 45 chỗ - 29B.12345',
  `SoLuongCho` int DEFAULT '0' COMMENT 'Sức chứa xe hoặc số người/phòng',
  `GhiChu` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tai_nguyen_ncc`
--

INSERT INTO `tai_nguyen_ncc` (`MaTaiNguyen`, `MaNhaCungCap`, `TenTaiNguyen`, `SoLuongCho`, `GhiChu`) VALUES
(1, 1, 'Xe 45 chỗ - 29B.99999', 0, NULL),
(2, 1, 'Xe Limousine - 29B.88888', 0, NULL),
(3, 2, 'Phòng Hội nghị A', 0, NULL),
(4, 2, 'Block phòng Deluxe (20 phòng)', 0, NULL),
(5, 2, 'aaaaaaaaa', 2222, 'aaa');

-- --------------------------------------------------------

--
-- Table structure for table `tour`
--

CREATE TABLE `tour` (
  `MaTour` int NOT NULL,
  `MaLoaiTour` int NOT NULL,
  `TenTour` varchar(200) NOT NULL,
  `HinhAnh` varchar(500) DEFAULT NULL,
  `MoTa` text,
  `ChinhSach` text,
  `SoNgay` int NOT NULL,
  `SoChoToiDa` int DEFAULT '40',
  `TrangThai` varchar(50) DEFAULT 'Hoạt động',
  `NgayTao` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tour`
--

INSERT INTO `tour` (`MaTour`, `MaLoaiTour`, `TenTour`, `HinhAnh`, `MoTa`, `ChinhSach`, `SoNgay`, `SoChoToiDa`, `TrangThai`, `NgayTao`) VALUES
(1, 1, 'Khám phá Mù Cang Chải - Yên Bái', 'anhtour/vietnam/mienbac/yenbai/yenbai1.jpg', 'Ngắm ruộng bậc thang mùa lúa chín tuyệt đẹp.', NULL, 3, 20, 'Hoạt động', '2025-11-21 05:26:42'),
(2, 1, 'Thám hiểm Hang Động Quảng Bình', 'anhtour/vietnam/mientrung/quangbinh/quangbinh1.jpg', 'Khám phá Phong Nha Kẻ Bàng và hang Sơn Đoòng.', NULL, 4, 15, 'Hoạt động', '2025-11-21 05:26:42'),
(3, 1, 'Nghỉ dưỡng Đảo Ngọc Phú Quốc', 'anhtour/vietnam/miennam/phuquoc/phuquoc1.jpg', 'Tận hưởng bãi biển xanh ngát và VinWonders.', NULL, 3, 30, 'Hoạt động', '2025-11-21 05:26:42'),
(4, 2, 'Mùa thu lá đỏ Seoul - Hàn Quốc', 'anhtour/hanquoc/seoul/seoul1.jpg', 'Tham quan đảo Nami, tháp Namsan và cung điện.', NULL, 5, 25, 'Hoạt động', '2025-11-21 05:26:42'),
(5, 2, 'Vạn Lý Trường Thành - Bắc Kinh', 'anhtour/trungquoc/backinh/backinh1.jpg', 'Khám phá Tử Cấm Thành và văn hóa Trung Hoa.', NULL, 5, 25, 'Hoạt động', '2025-11-21 05:26:42'),
(6, 3, 'Teambuilding Công ty ABC', 'anhtour/teambuilding/team1.jpg', 'Tour thiết kế riêng cho công ty tổng kết cuối năm.', NULL, 2, 100, 'Hoạt động', '2025-11-21 05:26:42'),
(7, 1, 'aaaaaaaaa', '1764314071_backinh1.jpg', 'aaaaaaaaaaaaaa', 'aaaaaaaaaaaaa', 1, 20, 'Hoạt động', '2025-11-28 14:14:31');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_baocaodoanhthu_thoigian`
-- (See below for the actual view)
--
CREATE TABLE `v_baocaodoanhthu_thoigian` (
`Nam` year
,`Thang` int
,`Quy` int
,`SoDoan` bigint
,`DoanhThu` decimal(40,2)
,`ChiPhi` decimal(40,2)
,`LoiNhuan` decimal(41,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_baocaoloinhuan`
-- (See below for the actual view)
--
CREATE TABLE `v_baocaoloinhuan` (
`LichCode` varchar(20)
,`TenTour` varchar(200)
,`NgayKhoiHanh` date
,`DoanhThu` decimal(40,2)
,`ChiPhi` decimal(40,2)
,`LoiNhuan` decimal(41,2)
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
-- Indexes for table `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  ADD PRIMARY KEY (`MaNhaCungCap`);

--
-- Indexes for table `phanbonhansu`
--
ALTER TABLE `phanbonhansu`
  ADD PRIMARY KEY (`MaPhanBo`),
  ADD KEY `MaLichKhoiHanh` (`MaLichKhoiHanh`),
  ADD KEY `MaNhanSu` (`MaNhanSu`);

--
-- Indexes for table `phan_bo_tai_nguyen`
--
ALTER TABLE `phan_bo_tai_nguyen`
  ADD PRIMARY KEY (`MaPhanBo`),
  ADD KEY `MaLichKhoiHanh` (`MaLichKhoiHanh`),
  ADD KEY `MaTaiNguyen` (`MaTaiNguyen`);

--
-- Indexes for table `tai_nguyen_ncc`
--
ALTER TABLE `tai_nguyen_ncc`
  ADD PRIMARY KEY (`MaTaiNguyen`),
  ADD KEY `MaNhaCungCap` (`MaNhaCungCap`);

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
  MODIFY `MaBooking` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `MaChiTiet` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `filedinhkem`
--
ALTER TABLE `filedinhkem`
  MODIFY `MaFile` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `giatour`
--
ALTER TABLE `giatour`
  MODIFY `MaGia` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `goidichvu`
--
ALTER TABLE `goidichvu`
  MODIFY `MaGoi` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hinhanhtour`
--
ALTER TABLE `hinhanhtour`
  MODIFY `MaHinhAnh` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `MaKhachHang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lichkhoihanh`
--
ALTER TABLE `lichkhoihanh`
  MODIFY `MaLichKhoiHanh` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lichtrinhtour`
--
ALTER TABLE `lichtrinhtour`
  MODIFY `MaLichTrinh` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loaitour`
--
ALTER TABLE `loaitour`
  MODIFY `MaLoaiTour` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `MaNguoiDung` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nhansu`
--
ALTER TABLE `nhansu`
  MODIFY `MaNhanSu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `nhatkytour`
--
ALTER TABLE `nhatkytour`
  MODIFY `MaNhatKy` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  MODIFY `MaNhaCungCap` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `phanbonhansu`
--
ALTER TABLE `phanbonhansu`
  MODIFY `MaPhanBo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phan_bo_tai_nguyen`
--
ALTER TABLE `phan_bo_tai_nguyen`
  MODIFY `MaPhanBo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tai_nguyen_ncc`
--
ALTER TABLE `tai_nguyen_ncc`
  MODIFY `MaTaiNguyen` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tour`
--
ALTER TABLE `tour`
  MODIFY `MaTour` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_baocaodoanhthu_thoigian`  AS SELECT year(`lkh`.`NgayKhoiHanh`) AS `Nam`, month(`lkh`.`NgayKhoiHanh`) AS `Thang`, quarter(`lkh`.`NgayKhoiHanh`) AS `Quy`, count(distinct `lkh`.`MaLichKhoiHanh`) AS `SoDoan`, coalesce(sum(`b`.`TongTien`),0) AS `DoanhThu`, coalesce(sum(`cp`.`SoTien`),0) AS `ChiPhi`, (coalesce(sum(`b`.`TongTien`),0) - coalesce(sum(`cp`.`SoTien`),0)) AS `LoiNhuan` FROM ((`lichkhoihanh` `lkh` left join `booking` `b` on(((`lkh`.`MaLichKhoiHanh` = `b`.`MaLichKhoiHanh`) and (`b`.`TrangThai` in ('Hoàn tất','Đã thanh toán','Đã xác nhận'))))) left join `chiphi` `cp` on((`lkh`.`MaLichKhoiHanh` = `cp`.`MaLichKhoiHanh`))) GROUP BY year(`lkh`.`NgayKhoiHanh`), month(`lkh`.`NgayKhoiHanh`), quarter(`lkh`.`NgayKhoiHanh`) ORDER BY `Nam` DESC, `Thang` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_baocaoloinhuan`
--
DROP TABLE IF EXISTS `v_baocaoloinhuan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_baocaoloinhuan`  AS SELECT `lkh`.`LichCode` AS `LichCode`, `t`.`TenTour` AS `TenTour`, `lkh`.`NgayKhoiHanh` AS `NgayKhoiHanh`, coalesce(sum(`b`.`TongTien`),0) AS `DoanhThu`, coalesce(sum(`cp`.`SoTien`),0) AS `ChiPhi`, (coalesce(sum(`b`.`TongTien`),0) - coalesce(sum(`cp`.`SoTien`),0)) AS `LoiNhuan` FROM (((`lichkhoihanh` `lkh` join `tour` `t` on((`lkh`.`MaTour` = `t`.`MaTour`))) left join `booking` `b` on(((`lkh`.`MaLichKhoiHanh` = `b`.`MaLichKhoiHanh`) and (`b`.`TrangThai` in ('Hoàn tất','Đã thanh toán','Đã xác nhận'))))) left join `chiphi` `cp` on((`lkh`.`MaLichKhoiHanh` = `cp`.`MaLichKhoiHanh`))) GROUP BY `lkh`.`MaLichKhoiHanh`, `lkh`.`LichCode`, `t`.`TenTour`, `lkh`.`NgayKhoiHanh` ORDER BY `lkh`.`NgayKhoiHanh` DESC ;

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
-- Constraints for table `phan_bo_tai_nguyen`
--
ALTER TABLE `phan_bo_tai_nguyen`
  ADD CONSTRAINT `phan_bo_tai_nguyen_ibfk_1` FOREIGN KEY (`MaLichKhoiHanh`) REFERENCES `lichkhoihanh` (`MaLichKhoiHanh`) ON DELETE CASCADE,
  ADD CONSTRAINT `phan_bo_tai_nguyen_ibfk_2` FOREIGN KEY (`MaTaiNguyen`) REFERENCES `tai_nguyen_ncc` (`MaTaiNguyen`) ON DELETE RESTRICT;

--
-- Constraints for table `tai_nguyen_ncc`
--
ALTER TABLE `tai_nguyen_ncc`
  ADD CONSTRAINT `tai_nguyen_ncc_ibfk_1` FOREIGN KEY (`MaNhaCungCap`) REFERENCES `nha_cung_cap` (`MaNhaCungCap`) ON DELETE CASCADE;

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
