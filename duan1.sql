-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 07, 2025 at 12:06 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GenerateData` ()   BEGIN
    DECLARE v_TourID INT;
    DECLARE v_LichID INT;
    DECLARE v_KhachID INT;
    DECLARE v_ScheduleCount INT;
    DECLARE v_BookingCount INT;
    DECLARE v_TotalPax INT;
    DECLARE v_NumGuides INT;
    DECLARE v_GuideIndex INT;
    DECLARE v_StartDate DATE;
    DECLARE v_Status VARCHAR(50);
    DECLARE v_Price DECIMAL(18,2);
    
    SET v_TourID = 1;
    WHILE v_TourID <= 6 DO -- Duyệt từng Tour
        
        SET v_ScheduleCount = 1;
        WHILE v_ScheduleCount <= 3 DO -- Mỗi Tour tạo 3 lịch
            
            -- Lịch 1: Quá khứ (Đã xong)
            -- Lịch 2: Sắp đi (Đang nhận khách)
            -- Lịch 3: Tương lai xa
            IF v_ScheduleCount = 1 THEN 
                SET v_StartDate = DATE_SUB(CURDATE(), INTERVAL 1 MONTH); 
                SET v_Status = 'Hoàn tất';
            ELSEIF v_ScheduleCount = 2 THEN
                SET v_StartDate = DATE_ADD(CURDATE(), INTERVAL 7 DAY); 
                SET v_Status = 'Nhận khách';
            ELSE
                SET v_StartDate = DATE_ADD(CURDATE(), INTERVAL 2 MONTH); 
                SET v_Status = 'Nhận khách';
            END IF;

            -- Insert Lịch
            INSERT INTO `lichkhoihanh` (`MaTour`, `LichCode`, `NgayKhoiHanh`, `NgayKetThuc`, `GioTapTrung`, `DiaDiemTapTrung`, `TrangThai`)
            VALUES (v_TourID, CONCAT('LKH', v_TourID, v_ScheduleCount, FLOOR(RAND()*100)), v_StartDate, DATE_ADD(v_StartDate, INTERVAL 3 DAY), '06:00:00', 'Nhà Hát Lớn', v_Status);
            
            SET v_LichID = LAST_INSERT_ID();
            SET v_TotalPax = 0; -- Reset tổng khách của lịch này

            -- Mỗi lịch tạo 5 đơn hàng (Booking)
            SET v_BookingCount = 1;
            WHILE v_BookingCount <= 5 DO
                -- Tạo khách hàng ảo
                INSERT INTO `khachhang` (`HoTen`, `SoDienThoai`) VALUES (CONCAT('Khách ', v_LichID, '_', v_BookingCount), CONCAT('09', FLOOR(RAND()*100000000)));
                SET v_KhachID = LAST_INSERT_ID();

                -- Random số lượng khách (1 - 15 người)
                SET @pax = FLOOR(1 + RAND() * 15);
                SET v_Price = 5000000;
                
                -- Random trạng thái đơn (20% Hủy)
                SET @bStatus = IF(RAND() > 0.2, IF(v_Status='Hoàn tất', 'Hoàn tất', 'Đã xác nhận'), 'Đã hủy');
                
                -- Nếu không hủy thì cộng vào tổng khách để tính HDV
                IF @bStatus != 'Đã hủy' THEN 
                    SET v_TotalPax = v_TotalPax + @pax; 
                END IF;

                -- Insert Booking
                INSERT INTO `booking` (`MaBookingCode`, `MaTour`, `MaLichKhoiHanh`, `MaKhachHang`, `NgayDat`, `NgayKhoiHanh`, `SoLuongKhach`, `TongTien`, `TrangThai`, `TrangThaiThanhToan`)
                VALUES (CONCAT('BK', v_LichID, v_BookingCount), v_TourID, v_LichID, v_KhachID, DATE_SUB(v_StartDate, INTERVAL 10 DAY), v_StartDate, @pax, @pax * v_Price, @bStatus, IF(@bStatus='Đã hủy', 'Chưa thanh toán', 'Đã thanh toán'));
                
                -- Tạo chi tiết khách đi kèm (Để test danh sách đoàn)
                -- (Chỉ tạo tượng trưng 1 dòng đại diện cho Booking đó)
                IF @bStatus != 'Đã hủy' THEN
                    INSERT INTO `chitietkhachbooking` (`MaBooking`, `MaKhachHang`, `LoaiKhach`, `SoGiayTo`, `GhiChu`)
                    VALUES (LAST_INSERT_ID(), v_KhachID, 'Người lớn', '00123456789', 'Trưởng đoàn');
                END IF;

                SET v_BookingCount = v_BookingCount + 1;
            END WHILE;

            -- Cập nhật tổng khách thực tế vào bảng Lịch
            UPDATE `lichkhoihanh` SET `SoKhachHienTai` = v_TotalPax WHERE `MaLichKhoiHanh` = v_LichID;

            -- --- PHÂN BỔ HƯỚNG DẪN VIÊN (LOGIC: 1 HDV / 20 KHÁCH) ---
            SET v_NumGuides = CEIL(v_TotalPax / 20.0);
            IF v_NumGuides < 1 THEN SET v_NumGuides = 1; END IF; -- Tối thiểu 1 người

            SET v_GuideIndex = 1;
            WHILE v_GuideIndex <= v_NumGuides DO
                -- Chọn ngẫu nhiên HDV từ 1-20 (Dùng INSERT IGNORE để tránh lỗi nếu random trùng người cũ trong cùng lịch)
                INSERT IGNORE INTO `phanbonhansu` (`MaLichKhoiHanh`, `MaNhanSu`, `VaiTro`)
                VALUES (v_LichID, FLOOR(1 + RAND() * 20), 'Hướng dẫn viên');
                
                SET v_GuideIndex = v_GuideIndex + 1;
            END WHILE;

            SET v_ScheduleCount = v_ScheduleCount + 1;
        END WHILE;
        SET v_TourID = v_TourID + 1;
    END WHILE;
END$$

DELIMITER ;

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
(1, 'BK11', 1, 1, 1, '2025-10-20', '2025-10-30', 13, 65000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(2, 'BK12', 1, 1, 2, '2025-10-20', '2025-10-30', 8, 40000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(3, 'BK13', 1, 1, 3, '2025-10-20', '2025-10-30', 6, 30000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(4, 'BK14', 1, 1, 4, '2025-10-20', '2025-10-30', 2, 10000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(5, 'BK15', 1, 1, 5, '2025-10-20', '2025-10-30', 11, 55000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(6, 'BK21', 2, 1, 6, '2025-11-27', '2025-12-07', 2, 10000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(7, 'BK22', 2, 1, 7, '2025-11-27', '2025-12-07', 2, 10000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(8, 'BK23', 2, 1, 8, '2025-11-27', '2025-12-07', 13, 65000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(9, 'BK24', 2, 1, 9, '2025-11-27', '2025-12-07', 6, 30000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(10, 'BK25', 2, 1, 10, '2025-11-27', '2025-12-07', 2, 10000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(11, 'BK31', 3, 1, 11, '2026-01-20', '2026-01-30', 6, 30000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(12, 'BK32', 3, 1, 12, '2026-01-20', '2026-01-30', 6, 30000000.00, 30000000.00, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(13, 'BK33', 3, 1, 13, '2026-01-20', '2026-01-30', 7, 35000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(14, 'BK34', 3, 1, 14, '2026-01-20', '2026-01-30', 11, 55000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(15, 'BK35', 3, 1, 15, '2026-01-20', '2026-01-30', 2, 10000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(16, 'BK41', 4, 2, 16, '2025-10-20', '2025-10-30', 5, 25000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(17, 'BK42', 4, 2, 17, '2025-10-20', '2025-10-30', 15, 75000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(18, 'BK43', 4, 2, 18, '2025-10-20', '2025-10-30', 5, 25000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(19, 'BK44', 4, 2, 19, '2025-10-20', '2025-10-30', 3, 15000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(20, 'BK45', 4, 2, 20, '2025-10-20', '2025-10-30', 4, 20000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(21, 'BK51', 5, 2, 21, '2025-11-27', '2025-12-07', 10, 50000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(22, 'BK52', 5, 2, 22, '2025-11-27', '2025-12-07', 10, 50000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(23, 'BK53', 5, 2, 23, '2025-11-27', '2025-12-07', 6, 30000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(24, 'BK54', 5, 2, 24, '2025-11-27', '2025-12-07', 1, 5000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(25, 'BK55', 5, 2, 25, '2025-11-27', '2025-12-07', 13, 65000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(26, 'BK61', 6, 2, 26, '2026-01-20', '2026-01-30', 14, 70000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(27, 'BK62', 6, 2, 27, '2026-01-20', '2026-01-30', 14, 70000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(28, 'BK63', 6, 2, 28, '2026-01-20', '2026-01-30', 1, 5000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(29, 'BK64', 6, 2, 29, '2026-01-20', '2026-01-30', 5, 25000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(30, 'BK65', 6, 2, 30, '2026-01-20', '2026-01-30', 14, 70000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(31, 'BK71', 7, 3, 31, '2025-10-20', '2025-10-30', 2, 10000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(32, 'BK72', 7, 3, 32, '2025-10-20', '2025-10-30', 6, 30000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(33, 'BK73', 7, 3, 33, '2025-10-20', '2025-10-30', 12, 60000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(34, 'BK74', 7, 3, 34, '2025-10-20', '2025-10-30', 1, 5000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(35, 'BK75', 7, 3, 35, '2025-10-20', '2025-10-30', 7, 35000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(36, 'BK81', 8, 3, 36, '2025-11-27', '2025-12-07', 14, 70000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(37, 'BK82', 8, 3, 37, '2025-11-27', '2025-12-07', 4, 20000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(38, 'BK83', 8, 3, 38, '2025-11-27', '2025-12-07', 15, 75000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(39, 'BK84', 8, 3, 39, '2025-11-27', '2025-12-07', 15, 75000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(40, 'BK85', 8, 3, 40, '2025-11-27', '2025-12-07', 3, 15000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(41, 'BK91', 9, 3, 41, '2026-01-20', '2026-01-30', 6, 30000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(42, 'BK92', 9, 3, 42, '2026-01-20', '2026-01-30', 3, 15000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(43, 'BK93', 9, 3, 43, '2026-01-20', '2026-01-30', 11, 55000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(44, 'BK94', 9, 3, 44, '2026-01-20', '2026-01-30', 12, 60000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(45, 'BK95', 9, 3, 45, '2026-01-20', '2026-01-30', 14, 70000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(46, 'BK101', 10, 4, 46, '2025-10-20', '2025-10-30', 13, 65000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(47, 'BK102', 10, 4, 47, '2025-10-20', '2025-10-30', 5, 25000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(48, 'BK103', 10, 4, 48, '2025-10-20', '2025-10-30', 13, 65000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(49, 'BK104', 10, 4, 49, '2025-10-20', '2025-10-30', 5, 25000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(50, 'BK105', 10, 4, 50, '2025-10-20', '2025-10-30', 2, 10000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(51, 'BK111', 11, 4, 51, '2025-11-27', '2025-12-07', 10, 50000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(52, 'BK112', 11, 4, 52, '2025-11-27', '2025-12-07', 9, 45000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(53, 'BK113', 11, 4, 53, '2025-11-27', '2025-12-07', 7, 35000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(54, 'BK114', 11, 4, 54, '2025-11-27', '2025-12-07', 3, 15000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(55, 'BK115', 11, 4, 55, '2025-11-27', '2025-12-07', 1, 5000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(56, 'BK121', 12, 4, 56, '2026-01-20', '2026-01-30', 13, 65000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(57, 'BK122', 12, 4, 57, '2026-01-20', '2026-01-30', 6, 30000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(58, 'BK123', 12, 4, 58, '2026-01-20', '2026-01-30', 5, 25000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(59, 'BK124', 12, 4, 59, '2026-01-20', '2026-01-30', 11, 55000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(60, 'BK125', 12, 4, 60, '2026-01-20', '2026-01-30', 12, 60000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(61, 'BK131', 13, 5, 61, '2025-10-20', '2025-10-30', 10, 50000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(62, 'BK132', 13, 5, 62, '2025-10-20', '2025-10-30', 6, 30000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(63, 'BK133', 13, 5, 63, '2025-10-20', '2025-10-30', 15, 75000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(64, 'BK134', 13, 5, 64, '2025-10-20', '2025-10-30', 13, 65000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(65, 'BK135', 13, 5, 65, '2025-10-20', '2025-10-30', 14, 70000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(66, 'BK141', 14, 5, 66, '2025-11-27', '2025-12-07', 3, 15000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(67, 'BK142', 14, 5, 67, '2025-11-27', '2025-12-07', 1, 5000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(68, 'BK143', 14, 5, 68, '2025-11-27', '2025-12-07', 11, 55000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(69, 'BK144', 14, 5, 69, '2025-11-27', '2025-12-07', 3, 15000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(70, 'BK145', 14, 5, 70, '2025-11-27', '2025-12-07', 1, 5000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(71, 'BK151', 15, 5, 71, '2026-01-20', '2026-01-30', 10, 50000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(72, 'BK152', 15, 5, 72, '2026-01-20', '2026-01-30', 7, 35000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(73, 'BK153', 15, 5, 73, '2026-01-20', '2026-01-30', 5, 25000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(74, 'BK154', 15, 5, 74, '2026-01-20', '2026-01-30', 13, 65000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(75, 'BK155', 15, 5, 75, '2026-01-20', '2026-01-30', 3, 15000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(76, 'BK161', 16, 6, 76, '2025-10-20', '2025-10-30', 15, 75000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(77, 'BK162', 16, 6, 77, '2025-10-20', '2025-10-30', 3, 15000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(78, 'BK163', 16, 6, 78, '2025-10-20', '2025-10-30', 9, 45000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(79, 'BK164', 16, 6, 79, '2025-10-20', '2025-10-30', 7, 35000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(80, 'BK165', 16, 6, 80, '2025-10-20', '2025-10-30', 11, 55000000.00, NULL, 'Hoàn tất', 'Đã thanh toán', NULL, NULL, NULL),
(81, 'BK171', 17, 6, 81, '2025-11-27', '2025-12-07', 1, 5000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(82, 'BK172', 17, 6, 82, '2025-11-27', '2025-12-07', 15, 75000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(83, 'BK173', 17, 6, 83, '2025-11-27', '2025-12-07', 9, 45000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(84, 'BK174', 17, 6, 84, '2025-11-27', '2025-12-07', 4, 20000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(85, 'BK175', 17, 6, 85, '2025-11-27', '2025-12-07', 4, 20000000.00, NULL, 'Đã hủy', 'Chưa thanh toán', NULL, NULL, NULL),
(86, 'BK181', 18, 6, 86, '2026-01-20', '2026-01-30', 6, 30000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(87, 'BK182', 18, 6, 87, '2026-01-20', '2026-01-30', 2, 10000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(88, 'BK183', 18, 6, 88, '2026-01-20', '2026-01-30', 3, 15000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(89, 'BK184', 18, 6, 89, '2026-01-20', '2026-01-30', 3, 15000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(90, 'BK185', 18, 6, 90, '2026-01-20', '2026-01-30', 4, 20000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
(91, NULL, 3, 1, 91, '2025-11-30', '2026-01-30', 3, 30000000.00, 30000000.00, 'Đã xác nhận', 'Đã thanh toán', NULL, 'Đơn tạo mới từ Admin', NULL),
(92, NULL, 4, 2, 92, '2025-11-30', '2025-10-30', 4, 20000000.00, 20000000.00, 'Đã xác nhận', 'Đã thanh toán', NULL, 'Đơn tạo mới từ Admin', NULL),
(93, 'BK2025000093', 14, 5, 93, '2025-11-30', '2025-12-07', 3, 30000000.00, 30000000.00, 'Đã xác nhận', 'Đã thanh toán', NULL, 'Đơn tạo mới từ Admin', NULL),
(94, 'BK2025000094', 6, 2, 95, '2025-12-02', '2026-01-30', 4, 344444443.00, 344444443.00, 'Đã xác nhận', 'Đã thanh toán', NULL, 'Đặt: 3 Lớn, 1 Trẻ em', NULL);

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
(1, 2, 2, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(2, 7, 7, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(3, 8, 8, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(4, 9, 9, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(5, 10, 10, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(6, 12, 12, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(7, 13, 13, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(8, 14, 14, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(9, 15, 15, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(10, 16, 16, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(11, 17, 17, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(12, 18, 18, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(13, 19, 19, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(14, 20, 20, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(15, 21, 21, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(16, 22, 22, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(17, 25, 25, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(18, 27, 27, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(19, 28, 28, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(20, 29, 29, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(21, 30, 30, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(22, 31, 31, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(23, 34, 34, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(24, 35, 35, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(25, 38, 38, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(26, 39, 39, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(27, 42, 42, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(28, 43, 43, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(29, 44, 44, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(30, 45, 45, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(31, 47, 47, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(32, 48, 48, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(33, 50, 50, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(34, 51, 51, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(35, 52, 52, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(36, 53, 53, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(37, 55, 55, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(38, 56, 56, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(39, 58, 58, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(40, 59, 59, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(41, 60, 60, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(42, 61, 61, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(43, 63, 63, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(44, 64, 64, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(45, 65, 65, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(46, 66, 66, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(47, 67, 67, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(48, 68, 68, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(49, 69, 69, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(50, 70, 70, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(51, 71, 71, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(52, 72, 72, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(53, 73, 73, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(54, 74, 74, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(55, 75, 75, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(56, 76, 76, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(57, 79, 79, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(58, 80, 80, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(59, 81, 81, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(60, 83, 83, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(61, 86, 86, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(62, 87, 87, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(63, 88, 88, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(64, 89, 89, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(65, 90, 90, 'Người lớn', '00123456789', NULL, 'Trưởng đoàn'),
(66, 11, 94, 'Người lớn', '11111111111111', NULL, '');

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
  `DuongDan` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hinhanhtour`
--

INSERT INTO `hinhanhtour` (`MaHinhAnh`, `MaTour`, `DuongDan`) VALUES
(1, 6, 'assets/uploads/1765023592_0_nhatban1.jpg'),
(2, 6, 'assets/uploads/1765023592_1_nhatban2.jpg'),
(3, 6, 'assets/uploads/1765023592_2_nhatban3.jpg'),
(4, 6, 'assets/uploads/1765023592_3_nhatban4.png'),
(5, 6, 'assets/uploads/1765023592_4_nhatban5.jpg'),
(6, 5, 'assets/uploads/1765023714_0_seoul1.jpg'),
(7, 5, 'assets/uploads/1765023714_1_seoul2.jpg'),
(8, 5, 'assets/uploads/1765023714_2_seoul3.jpg'),
(9, 5, 'assets/uploads/1765023714_3_seoul4.jpg'),
(10, 5, 'assets/uploads/1765023714_4_seoul5.jpg'),
(11, 4, 'assets/uploads/1765023927_0_thailan1.jpg'),
(12, 4, 'assets/uploads/1765023927_1_thailan2.jpg'),
(13, 4, 'assets/uploads/1765023927_2_thailan3.jpg'),
(14, 4, 'assets/uploads/1765023927_3_thailan4.jpg'),
(15, 4, 'assets/uploads/1765023927_4_thailan5.jpg'),
(16, 4, 'assets/uploads/1765023927_5_thailan6.jpg'),
(17, 3, 'assets/uploads/1765024157_0_phuquoc1.jpg'),
(18, 3, 'assets/uploads/1765024157_1_phuquoc2.jpg'),
(19, 3, 'assets/uploads/1765024157_2_phuquoc3.jpg'),
(20, 3, 'assets/uploads/1765024157_3_phuquoc4.jpg'),
(21, 3, 'assets/uploads/1765024157_4_phuquoc5.jpg'),
(22, 3, 'assets/uploads/1765024157_5_phuquoc6.jpg'),
(23, 2, 'assets/uploads/1765024325_0_danang1.jpg'),
(24, 2, 'assets/uploads/1765024325_1_danang2.jpg'),
(25, 2, 'assets/uploads/1765024325_2_danang3.jpg'),
(26, 2, 'assets/uploads/1765024325_3_danang4.jpg'),
(27, 2, 'assets/uploads/1765024325_4_danang5.jpg'),
(28, 2, 'assets/uploads/1765024325_5_danang6.jpg'),
(29, 1, 'assets/uploads/1765024453_0_halong2.jpg'),
(30, 1, 'assets/uploads/1765024460_0_halong1.jpg'),
(31, 1, 'assets/uploads/1765024460_1_halong2.jpg');

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
(1, 'Khách 1_1', NULL, '0974617904', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(2, 'Khách 1_2', NULL, '0950482430', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(3, 'Khách 1_3', NULL, '0923911199', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(4, 'Khách 1_4', NULL, '0949870462', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(5, 'Khách 1_5', NULL, '0944034758', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(6, 'Khách 2_1', NULL, '0920597573', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(7, 'Khách 2_2', NULL, '0984259621', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(8, 'Khách 2_3', NULL, '0984635391', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(9, 'Khách 2_4', NULL, '0995821692', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(10, 'Khách 2_5', NULL, '0980579979', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(11, 'Khách 3_1', NULL, '0961365774', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(12, 'Khách 3_2', NULL, '0910516179', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(13, 'Khách 3_3', NULL, '0984418599', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(14, 'Khách 3_4', NULL, '0969551257', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(15, 'Khách 3_5', NULL, '0984798614', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(16, 'Khách 4_1', NULL, '0990867049', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(17, 'Khách 4_2', NULL, '097219184', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(18, 'Khách 4_3', NULL, '0924124590', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(19, 'Khách 4_4', NULL, '0935049437', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(20, 'Khách 4_5', NULL, '0952591214', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(21, 'Khách 5_1', NULL, '0957038194', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(22, 'Khách 5_2', NULL, '0917067959', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(23, 'Khách 5_3', NULL, '0925405658', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(24, 'Khách 5_4', NULL, '0971617914', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(25, 'Khách 5_5', NULL, '0943480526', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(26, 'Khách 6_1', NULL, '096714813', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(27, 'Khách 6_2', NULL, '0928702767', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(28, 'Khách 6_3', NULL, '0914921609', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(29, 'Khách 6_4', NULL, '0918754213', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(30, 'Khách 6_5', NULL, '0997453746', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(31, 'Khách 7_1', NULL, '0963637089', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(32, 'Khách 7_2', NULL, '0962692587', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(33, 'Khách 7_3', NULL, '0997205716', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(34, 'Khách 7_4', NULL, '0983757624', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(35, 'Khách 7_5', NULL, '0976055743', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(36, 'Khách 8_1', NULL, '0942774590', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(37, 'Khách 8_2', NULL, '0968010012', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(38, 'Khách 8_3', NULL, '0985887195', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(39, 'Khách 8_4', NULL, '0934705497', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(40, 'Khách 8_5', NULL, '0957485646', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(41, 'Khách 9_1', NULL, '0992164977', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(42, 'Khách 9_2', NULL, '094504759', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(43, 'Khách 9_3', NULL, '0971926565', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(44, 'Khách 9_4', NULL, '0932496863', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(45, 'Khách 9_5', NULL, '0957731951', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(46, 'Khách 10_1', NULL, '0932514527', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(47, 'Khách 10_2', NULL, '0990356321', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(48, 'Khách 10_3', NULL, '0950613680', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(49, 'Khách 10_4', NULL, '099473903', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(50, 'Khách 10_5', NULL, '0991343052', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(51, 'Khách 11_1', NULL, '0990595556', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(52, 'Khách 11_2', NULL, '0985418912', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(53, 'Khách 11_3', NULL, '0987433117', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(54, 'Khách 11_4', NULL, '0988338907', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(55, 'Khách 11_5', NULL, '096418595', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(56, 'Khách 12_1', NULL, '0993682817', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(57, 'Khách 12_2', NULL, '0929518221', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(58, 'Khách 12_3', NULL, '0932021791', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(59, 'Khách 12_4', NULL, '0964751475', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(60, 'Khách 12_5', NULL, '0915529805', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(61, 'Khách 13_1', NULL, '0997541207', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(62, 'Khách 13_2', NULL, '0920028101', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(63, 'Khách 13_3', NULL, '0968635672', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(64, 'Khách 13_4', NULL, '0950095216', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(65, 'Khách 13_5', NULL, '0954454301', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(66, 'Khách 14_1', NULL, '0922763686', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(67, 'Khách 14_2', NULL, '0976475695', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(68, 'Khách 14_3', NULL, '093436277', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(69, 'Khách 14_4', NULL, '0910058586', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(70, 'Khách 14_5', NULL, '0911907561', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(71, 'Khách 15_1', NULL, '0915044598', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(72, 'Khách 15_2', NULL, '092128343', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(73, 'Khách 15_3', NULL, '0964063501', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(74, 'Khách 15_4', NULL, '0967614964', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(75, 'Khách 15_5', NULL, '0979482803', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(76, 'Khách 16_1', NULL, '0995594075', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(77, 'Khách 16_2', NULL, '0955976172', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(78, 'Khách 16_3', NULL, '0997053925', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(79, 'Khách 16_4', NULL, '0952957582', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(80, 'Khách 16_5', NULL, '0916150760', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(81, 'Khách 17_1', NULL, '0940431230', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(82, 'Khách 17_2', NULL, '0954929305', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(83, 'Khách 17_3', NULL, '092701368', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(84, 'Khách 17_4', NULL, '0933018369', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(85, 'Khách 17_5', NULL, '0968532759', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(86, 'Khách 18_1', NULL, '0919008277', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(87, 'Khách 18_2', NULL, '0986329240', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(88, 'Khách 18_3', NULL, '0993115927', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(89, 'Khách 18_4', NULL, '0918384882', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(90, 'Khách 18_5', NULL, '0956660684', NULL, NULL, NULL, '2025-11-30 04:29:49'),
(91, 'thai', NULL, '0968413287', NULL, NULL, NULL, '2025-11-30 04:53:45'),
(92, 'mai', NULL, '0822501239', NULL, NULL, NULL, '2025-11-30 05:03:08'),
(93, 'awdwa', NULL, '0123456789', NULL, NULL, NULL, '2025-11-30 05:08:11'),
(94, 'aaaaaaaaa', NULL, NULL, NULL, NULL, '11111111111111', '2025-12-02 04:21:52'),
(95, 'aaaaaaaaa', NULL, '33333333333333', '', '', NULL, '2025-12-02 06:01:17'),
(96, 'aaaaa', NULL, '6666666666', NULL, NULL, '666666666', '2025-12-07 06:40:28'),
(97, 'eeeeeeeee', NULL, '444444444444', NULL, NULL, '6333333333', '2025-12-07 06:40:28'),
(98, 'ttttttttttt', NULL, '3333333333', NULL, NULL, '4444444', '2025-12-07 06:40:28');

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
  `SoChoToiDa` int DEFAULT '20',
  `TrangThai` varchar(50) DEFAULT 'Đang chuẩn bị',
  `SoKhachHienTai` int DEFAULT '0',
  `GiaNguoiLon` decimal(18,2) DEFAULT '0.00',
  `GiaTreEm` decimal(18,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lichkhoihanh`
--

INSERT INTO `lichkhoihanh` (`MaLichKhoiHanh`, `MaTour`, `LichCode`, `NgayKhoiHanh`, `NgayKetThuc`, `GioTapTrung`, `DiaDiemTapTrung`, `SoChoToiDa`, `TrangThai`, `SoKhachHienTai`, `GiaNguoiLon`, `GiaTreEm`) VALUES
(1, 1, 'LKH1162', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 20, 'Hoàn tất', 8, 0.00, 0.00),
(2, 1, 'LKH1229', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 20, 'Nhận khách', 23, 0.00, 0.00),
(3, 1, 'LKH1356', '2026-01-20', '2026-01-23', '11:11:00', 'aaaaaaaa', 20, 'Nhận khách', 26, 11101111.00, 1110111.00),
(4, 2, 'LKH2141', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 20, 'Hoàn tất', 32, 0.00, 0.00),
(5, 2, 'LKH2274', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 20, 'Nhận khách', 33, 0.00, 0.00),
(6, 2, 'LKH2315', '2026-01-30', '2026-02-02', '06:00:00', 'Nhà Hát Lớn', 20, 'Nhận khách', 34, 111111111.00, 11111110.00),
(7, 3, 'LKH3169', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 20, 'Hoàn tất', 10, 0.00, 0.00),
(8, 3, 'LKH3242', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 20, 'Nhận khách', 30, 0.00, 0.00),
(9, 3, 'LKH3341', '2026-01-30', '2026-02-02', '06:00:00', 'Nhà Hát Lớn', 20, 'Nhận khách', 40, 0.00, 0.00),
(10, 4, 'LKH4160', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 20, 'Hoàn tất', 20, 0.00, 0.00),
(11, 4, 'LKH4229', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 20, 'Nhận khách', 27, 0.00, 0.00),
(12, 4, 'LKH4328', '2026-01-30', '2026-02-02', '06:00:00', 'Nhà Hát Lớn', 20, 'Nhận khách', 41, 0.00, 0.00),
(13, 5, 'LKH518', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 20, 'Hoàn tất', 52, 0.00, 0.00),
(14, 5, 'LKH5231', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 20, 'Nhận khách', 19, 0.00, 0.00),
(15, 5, 'LKH5371', '2026-01-30', '2026-02-02', '06:00:00', 'Nhà Hát Lớn', 20, 'Nhận khách', 38, 0.00, 0.00),
(16, 6, 'LKH6194', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 20, 'Hoàn tất', 33, 0.00, 0.00),
(17, 6, 'LKH6266', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 20, 'Nhận khách', 10, 0.00, 0.00),
(18, 6, 'LKH6318', '2026-01-30', '2026-02-02', '06:00:00', 'Nhà Hát Lớn', 20, 'Nhận khách', 18, 0.00, 0.00);

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
(11, 6, 1, 'HÀ NỘI - SÂN BAY NARITA (Nghỉ đêm trên máy bay)', '20h30: Quý khách tập trung tại điểm hẹn trong thành phố, xe và HDV đón đoàn di chuyển ra sân bay Nội Bài làm thủ tục đáp chuyến bay VJ 932 (00h50 – 08h00) đi Nhật Bản. Quý khách nghỉ đêm trên máy bay.'),
(12, 6, 2, 'TOKYO CITY (Ăn Sáng/Trưa/Tối)', 'Dự kiến 08h00 máy bay đáp sân bay Narita, Quý khách làm thủ tục nhập cảnh. Xe đón đoàn đi tham quan:\r\nHoàng cung: nơi sinh sống của Nhật Hoàng xưa với kiến trúc vô cùng xa hoa, tráng lệ. Nơi đây không chỉ gây ấn tượng bởi thiết kế cổ xưa mà còn sở hữu cảnh quan thiên nhiên hài hòa và bầu không khí trong lành, dễ chịu. (tham quan bên ngoài).\r\nĐoàn dùng bữa trưa tại  nhà hàng, sau bữa trưa đoàn tiếp tục tham quan:\r\nChùa Asakusa Kannon – Ngôi chùa cổ và linh thiêng nhất Tokyo. Quý khách có thể tự do ghé mua đồ lưu niệm ở khu phố đi bộ Nakamise cạnh chùa với nhiều sản phẩm thủ công mỹ nghệ truyền thống tinh xảo, và mang ý nghĩa tâm linh.\r\nNgắm tháp truyền hình Tokyo Skytree từ dòng sông Sumida (không bao gồm phí lên tháp)\r\nShopping tại thiên đường mua sắm Akihabara (nếu còn thời gian)\r\nĐoàn dùng bữa tối tại nhà hàng. Quý khách nghỉ ngơi tại khách sạn ở Tokyo. '),
(13, 6, 3, 'TOKYO - YAMANASHI (Ăn Sáng/Trưa/Tối)', 'Sau bữa sáng tại khách sạn, đoàn làm thủ tục check-out khách sạn và di chuyển về khu vực núi Phú Sỹ tham quan:\r\nNúi Phú Sĩ – biểu tượng của đất nước Nhật Bản – là đỉnh núi lửa đang hoạt động và cao nhất Nhật Bản – cao 3776m và được bao phủ bởi Ngũ hồ bao quanh núi Phú Sĩ. Quý khách tham quan đến trạm thứ 5 nếu thời tiết tốt (trong trường hợp thời tiết xấu sẽ tham quan và chụp hình dưới chân núi).\r\nĂn trưa tại nhà hàng, Quý khách tiếp tục tham quan:\r\n\r\nHồ Kawaguchiko: Hồ Kawaguchi là 1 trong 5 hồ Fuji Goko, và du khách có thể ngắm nhìn núi Phú Sĩ hùng vĩ ngay gần đó. Sự kết hợp giữa núi Phú Sĩ và phong cảnh thiên nhiên từng mùa cũng là một điểm nổi bật của nơi đây.\r\nMua sắm tại khu Premium Outlet Gotemba: rộng nhất Nhật Bản, nơi có hàng trăm chủng loại hàng hiệu giảm giá, đồ dùng, thời trang…có nhiều mặt hàng được giảm giá tới 50-70% (Nếu còn thời gian)\r\nĐoàn ăn tối và nghỉ ngơi tại khách sạn ở Yamanashi (Tặng cua Tuyết)\r\nQuý khách tự do tắm suối nước khoáng nóng tự nhiên trong khuôn viên khách sạn theo phong cách Nhật Bản (tiếng Nhật gọi là Onsen – người Nhật tin rằng khi ngâm mình trong suối nước nóng sẽ trị được một số bệnh về đau khớp, nhức mỏi… và một số bệnh khác).'),
(14, 6, 4, 'YAMANASHI - NAGOYA (Ăn Sáng/Trưa/Tối)', 'Sau bữa sáng tại khách sạn, Quý khách trả phòng khách sạn, xe đưa Quý khách tham quan:\r\nLàng Cổ Oshino Hakkai – ngôi làng nằm ngay dưới chân núi Phú Sĩ. Cho đến ngày nay, những ngôi làng ở Oshino Hakkai vẫn mang nét kiến trúc truyền thống Nhật Bản xa xưa.  Làng cổ Oshino Hakkai là điểm đến ngắm hoa anh đào khá lý tưởng và thu hút, dành cho những ai yêu thích vẻ đẹp hoài cổ cùng với sự thanh bình nơi đây.\r\nĂn trưa tại nhà hàng địa phương.\r\nSau bữa trưa, xe đưa đoàn đến ga tàu, trải nghiệm tàu siêu tốc Shinkansen (niềm tự hào của người Nhật, với vận tốc trung bình có lúc lên tới 300km/h) (1 chặng/khoảng 15 phút).\r\nĐến Nagoya, Quý khách tự do khám phá Sakae là một khu vực mua sắm chính ở khu trung tâm thành phố. Nơi đây có tất cả mặt hàng tốt từ mức giá rẻ, trung bình đến những thương hiệu nước ngoài. Gần khu vực mua sắm là công viên Hisayaodori – công viên được trang trí và thiết kế theo phong cách châu Âu khá giống với thiết kế của Pháp. Ở cuối công viên, Quý khách có thể mua vé lên tháp truyền hình Nagoya ngắm toàn cảnh thành phố Nagoya (chi phí tự túc)\r\nĐoàn di chuyển về Nagoya. Quý khách ăn tối, nhận phòng và nghỉ ngơi tại khách sạn tại Nagoya.'),
(15, 6, 5, 'NAGOYA - KYOTO - OSAKA (Ăn Sáng/Trưa/Tối)', 'Sau ăn sáng, Quý khách làm thủ tục trả phòng, đoàn lên xe khởi hành đi Kyoto tham quan:\r\n\r\nChùa Thanh Thủy (Kiyomizu Dera) – là một hạng mục của di sản văn hóa cố đô Kyoto với khuôn viên vô cùng rộng lớn nằm trên đồi giữa rừng lá phong và hoa anh đào.\r\nQuý khách dùng bữa trưa tại nhà hàng, tham quan:\r\nĐền ngàn cột Fushimi Inari Taisha: là một đền thờ Thần đạo được biết đến với khoảng 10.000 cổng torii màu đỏ son uốn lượn trên một con đường đi bộ dài hai giờ. Ngay sau khi xây dựng ngôi đền, để chứng minh sự đóng góp của những người thờ phụng, một cổng torii sẽ được xây dựng với tên của họ được ghi trên đó. Chẳng mấy chốc, hàng trăm hàng nghìn người muốn có được vận may tương tự và đã đến ngôi đền nổi tiếng này để mua cổng torii của riêng họ, tạo ra đường hầm “Senbon Torii” mà bạn thấy ngày nay.\r\nMua sắm tại trung tâm thương mại Shinsaibashi – khu thương mại sầm uất nhất của thành phố Osaka (nếu còn thời gian)\r\nQuý khách ăn tối và nghỉ đêm tại khách sạn trung tâm Osaka.'),
(16, 6, 6, 'OSAKA - HÀ NỘI (Ăn Sáng/Trưa)', 'Đoàn dùng bữa sáng tại khách sạn. Quý khách làm thủ tục trả phòng khách sạn. Xe đón quý khách ra tham quan:\r\nLâu đài Osaka: (Chụp hình bên ngoài) – địa điểm nổi tiếng nhất thành phố Osaka. Lâu đài Osaka được xây dựng vào nửa cuối thế kỷ 16 để làm nơi cư ngụ của Toyotomi Hideyoshi – vị lãnh chúa đã thống nhất Nhật Bản thời bấy giờ. Từ lúc khởi công đến khi hoàn thành mất hơn 16 năm, lâu đài có kích thước khổng lồ và được xem là một trong những biểu tượng của đất nước Nhật Bản.Đoàn dùng bữa trưa tại nhà hàng địa phương.\r\nSau bữa trưa xe đón đoàn đi sân bay quốc tế Kansai làm thủ tục đáp chuyến bay VJ 931 (15h30 – 19h15) về Hà Nội.\r\nDự kiến 19h15 Đoàn về đến sân bay Nội Bài. Xe và HDV đưa quý khách về điểm tập trung ban đầu trong thành phố.\r\nHDV chia tay đoàn và kết thúc chương trình.\r\nHẹn gặp lại quý khách trong những hành trình tiếp theo.'),
(17, 5, 1, 'VIETNAM - INCHEON (Ăn:-/-/Tối)', '6h sáng Xe đón quý khách tại điểm hẹn trong thành phố, xuất phát đi sân bay Nội Bài\r\nQuý khách tập trung tại nhà gà T2 sân bay Nội Bài, hướng dẫn viên giúp quý khách làm thủ tục đi Hàn Quốc trên chuyến bay VN414 lúc 10h20 đi Hàn Quốc.\r\nTới sân bay Quốc tế Incheon, đoàn làm thủ tục lấy hành lý và nhập cảnh.\r\nXe đưa đoàn đi ăn tối tại nhà hàng địa phương.\r\nNghỉ đêm ở khách sạn 4* tại Incheon.'),
(18, 5, 2, 'SEOUL - NAMI - NAMSAN (Ăn: Sáng/Trưa/Tối)', 'Quý khách ăn sáng tại khách sạn, xe du lịch của công ty sẽ đưa quý khách tới thăm Nami Island, nơi ra đời của nhiều bộ phim truyền hình nổi tiếng của Hàn Quốc đang làm dấy lên cơn sốt nghệ thuật thứ bảy tại các nước CHÂU Á và thế giới như: “Bản Tình Ca Mùa Đông“….\r\nBuổi chiều, xe đưa quý khách về thủ đô Seoul thăm quan Bảo tàng Rong Biển, học làm món Kim Chi Hàn Quốc – ẩm thực truyền thống và mặc Hanbok – quốc phục truyền thống của người dân Triều Tiên, chụp ảnh làm kỷ niệm một chuyến đi.\r\nThăm quan tháp Namsan, ngọn tháp truyền hình nằm trên đỉnh một ngọn núi ở giữa thủ đô Seoul. Từ đây quý khách có thể ngắm toàn bộ thủ đô Seoul với các hướng. (không bao gồm vé lên tháp)\r\nĐoàn ăn tối tại nhà hàng địa phương và nghỉ đêm ở khách sạn 4* tại Seoul'),
(19, 5, 3, 'SEOUL - SEOUL GRAND PARK (Ăn: Sáng/Trưa/Tối)', 'Quý khách ăn sáng tại khách sạn, sau đó xe du lịch của công ty sẽ sẽ đưa quý khách tới thăm công viên Seoul Grand Park, công viên sinh thái và giải trí lớn nhất thủ đô Seoul được hoàn thành xây dựng vào tháng 5/1988 trước sự kiện Thế vận hội Olympic 1988, quý khách trải nghiệm khu vui chơi và sở thú tại công viên. (Tháng 5: Vườn hoa hồng + Sở thú)\r\nSau bữa trưa, xe đưa quý khách đi thăm trung tâm mỹ phẩm miễn thuế với nhiều thương hiệu mỹ phẩm nổi tiếng của Hàn Quốc. Tại đây, quý khách sẽ được hướng dẫn cách trang điểm, sử dụng các loại mỹ phẩm phù hợp với làn da của người phụ nữ Châu Á.\r\nTham quan Suối Thanh Khê, một nhánh sông Hàn chạy giữa thành phố Seoul từng ô nhiễm rất nặng nề do hậu quả của công nghiệp hoá, nhưng sau đó đã được chính phủ Hàn Quốc đầu tư cải tạo lại thành lá phổi xanh của thủ đô Seoul.\r\n Thăm quảng trường Gwanghwamun, quảng trường trung tâm của thủ đô Seoul.\r\nSau đó hướng dẫn viên đưa đoàn đi ăn tối rồi nghỉ đêm tại khách sạn 4*.'),
(20, 5, 4, 'SEOUL (Ăn: Sáng/Trưa/Tối)', 'Sau khi ăn sáng tại khách sạn, xe đưa quý khách tham quan:\r\nĐiểm đến đầu tiên là cung điện Gyeongbok, bảo tàng Dân Gian SEOUL, Nhà Xanh (Dinh Tổng Thống)\r\nCung điện Gyeongbok: Là nơi ở chính của Hoàng Gia trong suốt vương triều Chosun (1392-1910). Điện Gyeongbok được coi là công trình nghệ thuật nổi tiếng có phong cách và kiến trúc độc đáo và đẹp nhất Seoul.\r\nBảo tàng dân gian Seoul: Là bảo tàng hàng đầu trưng bày văn hoá dân gian Hàn Quốc. Bảo tàng có hơn 98,000 hiện vật thể hiện đầy đủ và chi tiết những nét văn hóa truyền thống, cuộc sống sinh hoạt của người dân Triều Tiên.\r\nNhà Xanh (Dinh Tổng Thống): Nơi làm việc và nơi ở chính thức của các đời Tổng Thống Hàn Quốc. Nhà Xanh được xây dựng trên một khuôn viên rộng lớn với kiến trúc truyền thống Hàn Quốc kết hợp yếu tố hiện đại.\r\nTiếp tục hành trình, quý khách thăm Trung tâm nhân sâm Quốc Gia – là loại nhân sâm có lịch sử lâu đời của xứ sở kim chi từ hơn 1000 năm nay, hãng nhân sâm này được bảo hộ bởi chính phủ Hàn Quốc và là một sản vật nổi tiếng của xứ sở Cao Ly được truyền tụng từ hàng ngàn năm trước.\r\nGhé thăm shop Tinh Dầu Thông Đỏ – Sản phẩm chống mỡ máu, giảm đường huyết cho người mắc bệnh tiểu đường, chống đột quỵ và tim mạch nổi tiếng và cực kỳ hiệu quả. Người dân Hàn Quốc sử dụng tinh dầu thông đỏ trong cuộc sống hàng ngày để giúp nâng cao sức khoẻ, tinh thần thông tuệ,…\r\nKhám phá con phố Myengdong – thánh địa hội tụ hàng ngàn thương hiệu nổi tiếng trong nước và quốc tế. Con phố lúc nào cũng nhộn nhịp, khách tham quan sẽ được tận hưởng nhịp sống tại một thủ đô sôi động nhất thế giới.\r\nThưởng thức chương trình Hero Painter Show.\r\nSau đó hướng dẫn viên đưa đoàn đi ăn tối rồi nghỉ đêm ở khách sạn 4* tại Seoul'),
(21, 5, 5, 'SEOUL - INCHEON - HÀ NỘI (Ăn: Sáng/Trưa/-)', 'Sau bữa sáng đoàn làm thủ tục trả phòng. Sau đó, xe du lịch của công ty sẽ đưa quý khách thăm quan và mua sắm tại trung tâm miễn thuế Shilla duty free.\r\nThăm quan làng cổ Bukchon, ngôi làng tái hiện nếp sống truyền thống của người Hàn Quốc.\r\nThăm cửa hàng sâm tươi Hàn Quốc, sản phẩm không thể thiếu của người Việt khi đến xứ Hàn.\r\nSau đó, xe du lịch của công ty sẽ đưa quý khách ra sân bay Incheon trở về Việt Nam trên chuyến bay VN415 lúc 18h05\r\nVề đến sân bay Nội Bài, kết thúc chuyến đi tốt đẹp. Hẹn gặp lại quý khách trong những chương trình tiếp theo!'),
(22, 4, 1, 'HÀ NỘI – BANGKOK – SHOW BIỂU DIỄN NHẠC NƯỚC (Ăn Tối)', '12h30: Xe và Hướng dẫn viên của công ty đón Quý khách tại điểm hẹn và đưa ra sân bay quốc tế Nội Bài làm thủ tục cho chuyến bay VN613 khởi hành đi Bangkok (15h55 – 18h05)\r\nĐến sân bay Bangkok, HDV đón đoàn, đưa đoàn đi ăn tối. Sau bữa tối đoàn check in Icon Siam – một trong những TTTM hiện đại và lớn nhất Bangkok, bên bờ sông Chaophraya, nơi hội tụ tất cả các thương hiêu nổi tiếng trên Thế Giới như: LV,Gucci,…\r\n\r\nSau đó Đoàn di chuyển xem show: “Nhạc nước Icon Siam” được thiết kế và lắp đặt trên dòng sông nổi tiếng Chao Phraya tại đất nước Thái Lan. Công trinh quy mô, hoành tráng, đẳng cấp trang bị một loạt hệ thống tối tân, hiện đại.\r\n\r\nSau show diễn, xe đưa Quý khách về nhận phòng và nghỉ đêm tại Bangkok'),
(23, 4, 2, 'BANGKOK - PATTAYA – ALCAZAR SHOW (Ăn Sáng/ Trưa/ Tối)', 'Sáng: Sau bữa sáng, xe đón Quý khách khởi hành từ Bangkok đi Pattaya.\r\nHDV đưa đoàn khởi hành đi thăm quan:\r\n–    Wat Phrayai – Tọa lạc trên đỉnh đồi Pratumnak, giữa hai bờ biển Pattaya và Jomtien, Quý khách sẽ nhìn thấy một tượng Phật khổng lồ cao 18 m qua những hàng cây. Đây là tượng Phật lớn nhất khu vực – điểm nổi bật của chùa Wat Phra Yai, một ngôi chùa được xây dựng từ năm 1940 khi mà Pattaya mới chỉ là một làng chài.\r\n–    Great & Grand Sweet Destination – thuộc địa phận thành phố biển Pattaya xinh đẹp của Thái Lan, là quán cà phê duy nhất có không gian vui chơi ngoài trời rộng lớn, với điểm nhấn đặc trưng được truyền cảm hứng bởi các món ngọt tráng miệng. Đến đây, Quý khách sẽ được check-in cùng những ngôi nhà bánh gừng, kẹo mút và kem ốc quế có kích thước tương đương một người trưởng thành, được tô điểm bằng các tông màu pastel dịu ngọt.\r\n\r\nTrưa: Quý khách ăn trưa tại nhà hàng địa phương.\r\n\r\nChiều: Đoàn đến Pattaya, Quý khách nhận phòng, nghỉ ngơi, sau đó HDV đưa đoàn đi thưởng thức bữa tối BBQ – Buffet hải sản nướng tươi ngon tại nhà hàng.\r\n\r\nSau bữa tối, xe đưa đoàn đi xem chương trình Alcazar show hấp dẫn – chương trình biểu diễn đặc sắc, hoành tráng của các nghệ nhân chuyển giới xinh đẹp Thái lan.\r\nSau đó, Quý khách tự do khám phá phố đi bộ Walking Street thành phố “ma quỷ” theo đúng nghĩa bóng là thành phố không ngủ, với hệ thống hàng nghìn quán Bar, nhà hàng, những quán massage đặc sản của Thailand, những món ăn đường phố đa dạng phong phú và độc đáo. Thật sự là trải nghiệm vô cùng thú vị với Quý khách.\r\nNghỉ đêm tại Pattaya.'),
(24, 4, 3, 'CORAL ISLAND - VƯỜN NHIỆT ĐỚI NONG NOOCH (Ăn Sáng/ Trưa/Tối)', 'Sáng: Đoàn ăn sáng buffet tại khách sạn.\r\nSau bữa sáng, HDV đưa Quý khách tắm biển tại bãi biển đảo Coral – đi cano ra đảo. Quý khách tự do tắm biển Pattaya với làn nước trong xanh, bãi cát dài trắng mịn hoặc có thể tham dự các trò chơi: dù bay, câu cá, lướt ván, lái scooter trên mặt biển, thám hiểm dưới đáy biển… (chi phí tự túc). Đoàn trở lại đất liền.\r\n\r\nTrưa: Quý khách ăn trưa tại nhà hàng địa phương.\r\n\r\nChiều: Xe và HDV đưa đoàn đi tham quan:\r\n–    Trung tâm chế tác đá quý lớn nhất Đông Nam Á – World Gems Gallery – nơi diễn các phiên trao đổi mua bán đá quý đạt chứng chỉ ISO 9002 về quản lý chất lượng, nơi trưng bày các loại đá quý đẹp nổi tiếng, cung cấp những thông tin bổ ích về quá trình khai thác, chế tác đá quý tại Thái Lan và trên thế giới.\r\n–    Khao Chee Chan – Trân Bảo Phật Sơn: Tượng Phật Thích Ca Mâu Ni đang ngồi thiền được tạc trên một vách núi giữa trời. Sự độc đáo của bức tượng Phật lớn này là được khắc nổi bằng vàng ròng 24 kara, cao 130 m, rộng hơn 70m, được xây dựng vào năm 1996, nhân dịp Quốc vương RaMa IX trị vì vương quốc Thái Lan được 50 năm.\r\n–    Trung tâm nệm gối cao su Latex Thái Lan\r\n–    Vườn Lan Nhiệt Đới NongNouch & Công viên Khủng Long – đến Nong Nooch để khám phá những khu vườn tuyệt vời, với đầy đủ các loài thực vật và hoa từ khắp nơi trên thế giới. Thế nhưng trong vườn nhiệt đới Nong Nooch có tới 20 loại khu vườn riêng biệt, mỗi loại được thiết kế để trưng bày các loại thực vật khác nhau. Ở vườn thực vật Nong Nooch có cực nhiều góc chụp ảnh tuyệt đẹp bên cây cỏ có kiểu dáng độc đáo. Quý khách tới đây đảm bảo sẽ thích thú với những khu vườn đa dạng từ thiết kế truyền thống của Pháp, Ý cho đến những bộ sưu tập cây cảnh bonsai đặc sắc.\r\n\r\nTối: Xe đưa về khách sạn. Ăn tối. Nghỉ đêm tại Pattaya.'),
(25, 4, 4, 'PATAYA – BANGKOK - BUFFET BAIYOKE SKY 86 TẦNG – TỰ DO MUA SẮM', 'Sáng: Sau bữa sáng, Quý khách tiếp tục với chương trình khởi hành về Bangkok trên đường đi ghé thăm:\r\n–    Big Bee Farm tìm hiểu về công dụng của Mật ong và các sản phẩm Mật Ong, Sữa ong chúa, Sáp ong , Cao Hổ … Hoặc Tiger Hurb điểm đến nổi tiếng với những show biểu diễn thú vị ngoài hổ ra còn có cá sấu, lạc đà, cá heo –\r\n–    Trung tâm nghiên cứu Rắn độc: xem chương trình biểu diễn bắt rắn bằng tay không của Hoàng Gia Thái Lan.\r\n\r\nTrưa: Quý khách thưởng thức Buffet tại tòa nhà 86 tầng BaiYoke Sky  – Toà nhà cao nhất nhì Thái Lan, biểu tượng đất nước chùa Vàng. Quý khách thưởng thức vô số những món ăn đặc trưng , cao cấp Á Âu tại đây và chụp ảnh, ngắm nhìn toàn cảnh thủ đô Bangkok.\r\n\r\nChiều: Đoàn thăm quan tượng phật Bốn Mặt linh thiêng.\r\nSau đó, tự do mua sắm tại trung tâm thương mại Big C, Central World, Pratunam, …\r\n\r\nTối: Quý khách dùng bữa tối tự túc để trải nghiệm ẩm thực Thái Lan.\r\nXe đưa về khách sạn. Nghỉ đêm tại Bangkok.'),
(26, 4, 5, 'DẠO THUYỀN SÔNG CHAOPRAYA – SÂN BAY (Ăn Sáng/Trưa)', 'Sáng: Ăn sáng tại khách sạn. Xe đưa Quý khách đi tham quan:\r\n–    Dạo thuyền trên sông Chaopraya\r\n–    Chùa Thuyền Wat Yanawa – có hình dáng như một con thuyền buôn Trung Hoa nhưng kiến trúc chùa và trang trí mang đậm chất Thái Lan với các mái cao vút theo phong cách thời Ayuthaya. Tại đây còn có bức tượng nhà vua Rama I đứng oai phong, gợi cho không khí trong chùa thêm trang nghiêm, huyền bí. Chùa không quá lớn, bên trong chùa bày trí cũng khá đơn giản. Chùa thờ phật và rất nhiều bình xá lợi. Xá lợi là những vật còn lại sau khi hỏa táng xác các nhà sư\r\n–    Wat Traimit hay còn được gọi là Chùa Phật Vàng bởi trong ngôi chùa có chứa giữ một bảo vật đó là pho tượng Phật bằng vàng nguyên khối quý giá. Ngôi chùa này là một trong 5 ngôi chùa nổi tiếng nhất Bangkok, không chỉ có vẻ đẹp lung linh, sự linh thiêng thanh tịnh vốn có mà còn mang giá trị lịch sử độc đáo.\r\n\r\nTrưa: Đoàn ăn trưa tại nhà hàng địa phương.\r\nSau đó, Quý khách vào tham quan mua sắm tại Siêu thị BigC gần sân bay\r\n\r\nChiều: Quý khách ra sân bay làm thủ tục đáp chuyến bay VN 612 (19h05 – 21h10) về Hà Nội.\r\nTới sân bay Nội Bài, xe đưa Quý khách về điểm hẹn ban đầu.\r\n\r\nKết thúc chương trình.'),
(27, 3, 1, 'Hà Nội - Phú Quốc - Đảo Đông (Ăn trưa, tối)', 'Sáng : Xe và HDV của Công ty đón quý khách tại nhà hát lớn khởi hành ra sân bay Nội Bài đáp chuyến bay khởi hành đến Phú Quốc.\r\nĐến sân bay Phú Quốc, xe và HDV đón du khách tại sân bay Phú Quốc, đưa đoàn đi ăn trưa với đặc sản Phú Quốc. Về KS nhận phòng, nghỉ ngơi.\r\nChiều: Khởi hành tham quan phía Đông đảo:\r\nTham quan Cơ sở rượu vang Sim – một loại rượu đặc sản tại địa phương, thưởng thức rượu Sim rừng miễn phí\r\nVườn tiêu với những nọc tiêu thẳng tắp, xanh mơn mỡn, nổi tiếng chắc hạt, thơm ngon\r\nTham quan trại nuôi mật ong, tìm hiểu cuộc sống cần cù của những chú ong bé nhỏ và học cách lấy mật ong (quay mật) của người dân. Đến mùa trái cây, Quý khách còn được chiêm ngưỡng và tận tay hái các loại sầu riêng, chôm chôm ….trĩu quả trên cành\r\nDòng suối đẹp như tranh bắt nguồn từ dãy Hàm Ninh, gồm nhiều con suối nhỏ nhập lại thành thác nước lớn – Suối tranh, trekking, tắm suối (chỉ tham quan từ tháng 5 đến tháng 11, sau tháng 11 không đi Suối Tranh)\r\nTối: Quý khách ăn tối tại nhà hàng, về khách sạn nghỉ ngơi. Tự do dạo bãi biển, thưởng thức không khí yên tĩnh tuyệt vời của huyện đảo hoặc đăng ký tour ghép câu mực đêm (Chi phí tự túc)'),
(28, 3, 2, 'Bãi Sao - Câu Cá - Tắm biển (Ăn sáng, trưa, tối)', 'Buổi sáng: Quý khách dùng điểm tâm sáng tại khách sạn\r\n8h00: Xe và hướng dẫn viên đón quý khách tại khách sạn, đưa quý khách bắt đầu hành trình khám phá Nam đảo:\r\nKhu nuôi cấy Ngọc Trai Nhật Bản: Tìm hiểu quy trình sản xuất ngọc trai, khách có thể mua những viên ngọc trai đươc nuôi cấy tại đảo để làm quà lưu niêm (chi phí mua sắm tự túc)\r\n10h30: Đến Cảng An Thới, quý khách ngắm cảnh sinh hoạt nhộn nhịp của ngư dân, chụp hình lưu niệm quần đảo An Thới từ cầu tàu.,lên tàu câu cá và lặn ngắm san hô\r\n14h30 : Quý khách thăm quan Di tích lịch sử Nhà Lao Cây Dừa: Tìm hiểu tội ác chiến tranh của đế quốc Mỹ tại “địa ngục trần gian”\r\n16h00 : Quý khách tắm biển tại Bãi Sao, hòa mình cùng làn nước trong mát của biển xanh cát trắng của bãi biển đẹp nhất Phú Quốc và là điểm đến không thể thiếu trong mọi hành trình khám phá Đảo Ngọc. Quý khách dành nhiều thời gian tại Bãi Sao để tắm biển và tự do nghỉ ngơi.\r\nBuổi tối: Xe và HDV đưa quý khách đi ăn tối tại nhà hàng sau đó quý khách có thể về khách sạn nghỉ ngơi hay tự do khám phá Phú Quốc về đêm.'),
(29, 3, 3, 'Khám phá bắc đảo - Vinpearl land, safari (Ăn sáng, trưa)', '07h00: Quý khách ăn sáng tại khách sạn/ resort.\r\n08h00: Xuyên rừng quốc giá Phú Quốc – Khám phá rừng nguyên sinh với hệ thực vật và những loài động vật vô cùng phong phú. Dừng chân ở bìa rừng, ngắm cảnh, chụp hình.\r\n09h00: Tham quan Đền thờ anh hùng dân tộc Nguyễn Trung Trực – để tưởng nhớ vị anh hùng đã xã thân vì nước với tinh thần bất khuất, kiêng cường.\r\n09h30: Đến Mũi Gành Dầu – ngắm hải giới Campuchia và nghe đờn ca tài tử ở Biên hải quán.\r\n12h00: Quý khách ăn trưa tại nhà hàng\r\nBuổi chiều: Sau bữa trưa đoàn khởi hành về lại khách sạn\r\nTrên đường về quý khách có thể  đi Vinpearl Phú Quốc (vé vào Chi phí tự túc), quý khách tham gia các trò chơi trong khu nghỉ dưỡng: Thủy cung, công viên nước, thế giới games, rạp chiếu phim 5D, khu mua sắm, làng ẩm thực, khu phố ăn nhanh … Qúy khách thưởng thức sân khấu nhạc nước hoành tráng nhất Đông Nam Á, biểu diễn cá heo, chương trình nàng tiên cá, lễ hội đường phố, hoạt náo đường phố\r\nHoặc quý khách có thể tham quan công viên chăm sóc và bảo tồn động vật Vinpearl Safari (vé vào cổng chi phí tự thúc )  .là vườn thú mở lớn nhất việt nam . tại đây quý khách có cơ hội :\r\n\r\nTham quan khu nuôi dưỡng chăm sóc , bảo tồn động vật hoang dã duy nhất và lớn nhật hiện nay tại việt nam\r\n\r\nThưởng thúc những vũ điệu hoang dã do chính các thổ dân châu phí biểu diễn\r\nThưởng thức những màn biểu diễn hấp dẫn của nững loài động vật hoang dã\r\nChụp ảnh cùng những loại động vật mà mình yêu thích\r\nTự tay chăm sóc , cho những loài động vật ăn\r\nTối: Quý khách tự túc ăn tối và thưởng thức đặc sản địa phương, nghỉ đêm tại khách sạn.'),
(30, 3, 4, 'Tạm biệt Phú Quốc(Ăn sáng, trưa)', 'Quý khách ăn sáng tại khách sạn sau đó tự do tắm biển, nghỉ ngơi và dọn đồ, trả phòng khách sạn.\r\n08h00 : Đoàn thăm quan Dinh Cậu – nơi thờ cúng và cầu mong mưa thuận gió hòa, trời yên biển lặng cho 1 chuyến đi biển bội thu của người dân đảo Phú Quốc. Trước khi rời Phú Quốc, quý khách ghé thăm các cửa hàng hải sản khô mua quà về cho gia đình.\r\nĂn trưa tại nhà hàng\r\nĐến giờ, đoàn lên xe đi sân bay Dương Đông, đáp chuyến bay khởi hành về Hà Nội. Tới sân bay Nội Bài, xe đón Quý khách về điểm hẹn ban đầu. Kết thúc chuyến đi tốt đẹp !\r\nGhi chú: Lịch trình thăm quan có thể thay đổi linh hoạt theo thực tế nhưng vẫn đảm bảo đầy đủ các điểm theo chương trình.'),
(31, 2, 1, 'HÀ NỘI – ĐÀ NẴNG – HỘI AN (Ăn Trưa/ Tối)', 'Sáng: Hướng dẫn viên và xe đón đoàn tại Cổng Công Viên Thống Nhất – Đường Trần Nhân Tông – Quận Hai Bà Trưng – Tp Hà Nội, khởi hành đi Sân bay Nội Bài, đáp chuyến bay VN165/VN183/VN161 đi sân bay quốc tế Đà Nẵng.\r\nĐoàn có mặt tại sân bay Đà Nẵng, Ôtô và Hướng dẫn viên địa phương đón đoàn về lại trung tâm thành phố Đà Nẵng.\r\nXe đưa đoàn đi Bán Đảo Sơn Trà – để thưởng ngoạn toàn cảnh phố biển Đà Nẵng trên cao ngoạn toàn cảnh phố biển Đà Nẵng trên cao. Xe đưa quý khách dọc theo triền núi Đông Nam để chiêm ngưỡng vẻ đẹp tuyệt mỹ của biển Đà Nẵng, viếng Linh Ứng Tự – nơi có tượng Phật Bà 65m cao nhất Việt Nam.\r\n\r\n12h00: Đoàn Ăn trưa tại nhà hàng. Sau đó đoàn về khách sạn check in lấy phòng nghỉ ngơi.\r\n15h30: Xe đón quý khách đi thăm quan Phố cổ Hội An – nằm cách trung tâm thành phố Đà Nẵng 35km về phía nam, Hội An là 1 thương cảng sầm uất bậc nhất của xứ Đàng Trong từ thời Trịnh Nguyễn phân tranh.\r\n⮚    Đoàn Bách bộ thưởng ngoạn vẻ đẹp Phố Cổ Hội An, rực rỡ soi bóng bên dòng sông Hoài, từng là thương cảng sầm uất của người Chăm thế kỉ thứ II và Việt Nam từ thế kỉ XVI.\r\n⮚    Hướng dẫn viên giúp Quý khách tìm hiểu và khám phá những khu phố bàn cờ, Chùa Cầu Nhật Bản, Hội quán Phước Kiến, Quảng Đông, nhà cổ Tân Ký, nhà thờ Tộc Trần…\r\n\r\nTối: Ăn tối tại nhà hàng. Sau bữa tối, quý khách dạo chơi Hội An, chụp ảnh phố Hội về đêm. Hoặc đoàn tự do dạo chơi hoặc mua vé xem chương trình “ KÝ ỨC HỘI AN” (chi phí tự túc: từ 600.000/ khách) – Xuyên suốt 60 phút trình diễn, hơn 500 nghệ sĩ, diễn viên chuyên nghiệp sẽ mang đến cho quý khách bức tranh chân thực và đặc sắc về một Hội An – nơi đã từng là thương cảng sầm uất, nơi giao thoa văn hóa giữa Việt Nam, Nhật Bản, Trung Quốc và Phương Tây.\r\nXe đưa đoàn về khách sạn tại Đà Nẵng nghỉ đêm tại khách sạn 4 sao.'),
(32, 2, 2, 'ĐÀ NẴNG – BÀ NÀ HILLS (Ăn:Sáng/ Trưa tự túc/ Tối)', 'Sáng: Quý khách ăn sáng tại khách sạn.\r\n08h00: Sau đó xe đưa quý khách khởi hành đi:\r\n⮚    Khu du lịch Bà Nà – Núi Chúa (chi phí vé tham quan khách tự túc), nơi mà quý khách khám phá những khoảnh khắc giao mùa bất ngờ Xuân – Hạ – Thu – Đông trong 1 ngày.\r\nQúy khách Đến ga cáp treo Suối Mơ, lên tuyến cáp treo đạt 2 kỷ lục thế giới, (dài gần 6.000m), quý khách tham quan:\r\n⮚    Khu Le Jardin, tham quan Hầm Rượu Debay của Pháp.\r\n⮚    Viếng Chùa Linh Ứng Bà Nà, chiêm ngưỡng tượng Phật Thích Ca cao 27m\r\n⮚    Vườn Lộc Uyển, Quan Âm Các.\r\n⮚    Tiếp tục đến Gare Debay đi tuyến cáp thứ 2 lên đỉnh Bà Nà.\r\n\r\nTrưa: Ăn trưa tại nhà hàng trên đỉnh Bà Nà. (Chi phí ăn trưa tự túc)\r\n14h30: ⮚    Tiếp tục chinh phục đỉnh núi Chúa ở độ cao 1.487m so với mực nước biển để thưởng thức quang cảnh núi rừng Bà Nà và toàn cảnh Đà Nẵng và Quảng Nam trên cao.\r\n⮚    Tham quan khu vui chơi Fantasy Park, quý khách tự do tham gia các trò chơi phiêu lưu mới lạ, hấp dẫn, hiện đại như vòng quay tình yêu, phi công Skiver, đường đua lửa, xe điện đụng, ngôi nhà ma…\r\nQúy khách xuống cáp treo về lại Đà Nẵng. Xe đưa quý khách về khách sạn, quý khách tự do nghỉ ngơi, tắm biển.\r\n\r\n19h00: Đoàn ăn tối tại nhà hàng, sau đó là thời gian tự do khám phá thành phố Đà Nẵng về đêm, thưởng thức café, nét văn hóa người dân Đà Nẵng. Ngủ đêm tại Khách sạn 4 sao ở Đà Nẵng.'),
(33, 2, 3, 'TP ĐÀ NẴNG – HUẾ (Ăn: Sáng/ Trưa/ Tối)', 'Sáng: Đoàn dùng bữa sáng tại nhà hàng và trả phòng khách sạn.\r\nSau đó, Đoàn di chuyển tới thành phố Huế, trên đường quý khách dừng chân thăm quan, chụp hình tại bãi biển Lăng Cô – Thừa Thiên Huế.\r\nTrên đường quý khách dừng chân thăm quan: Lăng Khải Định – một trong những lăng tẩm đồ sộ được xây dựng kỳ công qua 11 năm với kiến trúc độc đáo kết cấu bằng các nguyên liệu ngoại quốc.\r\n\r\nTrưa: Đoàn dùng bữa trưa tại nhà hàng. Sau đó về khách sạn nhận phòng nghỉ ngơi.\r\n\r\nChiều: Xe và HDV đón đoàn đi tham quan:\r\n⮚    Đại Nội (Hoàng Cung của 13 vị vua triều Nguyễn, triều đại phong kiến cuối cùng của Việt Nam: Ngọ Môn, Điện Thái Hòa, Tử Cấm Thành, Thế Miếu, Hiển Lâm Các, Cửu Đỉnh).\r\n⮚    Chùa Thiên Mụ hay còn gọi là chùa Linh Mụ là một ngôi chùa cổ nằm trên đồi Hà Khê, tả ngạn sông Hương, cách trung tâm thành phố Huế khoảng 5 km về phíatây.\r\n\r\nTối: Đoàn dùng bữa tối tại nhà hàng. Xe và Hướng dẫn viên đưa quý khách ra sông Hương, đoàn lên thuyền rồng nghe Ca Huế. Sau đó đoàn tự do tham quan thành phố Huê thơ mộng về đêm. Nghỉ đêm khách sạn 4 sao tại Huế.'),
(34, 2, 4, 'HUẾ – HÀ NỘI (Ăn: Sáng)', 'Sáng: Sau khi ăn sáng, trả phòng khách sạn.\r\n\r\n08h00: Xe và HDV đón đoàn đi tham quan:\r\n⮚    Làng hương trầm Thủy Xuân là một trong những địa điểm du lịch Huế nổi tiếng mà hầu như mọi du khách đều dừng chân ghé thăm\r\n⮚    Đoàn dừng chân mua đặc sản xứ Huế: Mè Xững, Dầu tràm, Tôm Chua….\r\n\r\nXe đưa đoàn ra sân bay đáp chuyến bay VN1542 lúc 12h35  về Hà Nội. (Quý khách tự túc ăn trưa tại sân bay).\r\nĐến sân bay Nội Bài, Xe và HDV đón đoàn và đưa đoàn về lại điểm đón ban đầu. Kết thúc chương trình tham quan!'),
(38, 1, 1, 'Hà Nội - Hạ Long (Ăn trưa, tối)', '07h30: Xe và HDV đón Quý khách tại điểm hẹn khởi hành đi Hạ Long. Quý khách nghe thuyết minh và giao lưu văn nghệ trên xe với các tiểu phẩm hấp dẫn do HDV tổ chức\r\n11h00: Tới Hạ Long, nghỉ ngơi và ăn trưa tại nhà hàng. Sau đó Quý khách nhận phòng nghỉ ngơi.\r\nChiều: Quý khách tự do nghỉ ngơi, hoặc tham quan Quần thể du lịch giải trí Sun World Halong Complex là sản phẩm của Tập đoàn Sun Group mang đến muôn vàn trải nghiệm (chi phí tự túc):\r\n\r\nPhấn khích với các trò chơi cảm giác mạnh tại Công Viên Rồng – Dragon Park\r\nThách thức nắng hè tại Công Viên Nước Typhoon Water Park\r\nThích thú với cáp treo Nữ Hoàng chiêm ngưỡng Vịnh Hạ Long\r\nHòa mình trong không gian mang đậm phong cách Nhật Bản tại đồi Ba Đèo\r\nThư thái trong làn nước xanh mát tại bãi biển Sun World\r\nThưởng thức ẩm thực và thỏa thích mua sắm\r\nTối: Dùng bữa tối tại nhà hàng. Nghỉ đêm tại khách sạn.'),
(39, 1, 2, 'Tham quan vịnh Hạ Long (Ăn sáng, trưa, tối)', 'Sáng: Quý khách ăn sáng tại khách sạn.\r\n08h00: Quý khách lên tàu đi thăm Vịnh Hạ Long – một thắng cảnh được UNESCO công nhận là di sản thiên nhiên của Thế giới năm 1994.  Đi thuyền khám phá Vịnh Hạ Long, Quý khách dừng thuyền lên tham quan động Thiên Cung tức là “Cung điện của trời”, Hang Đầu Gỗ.\r\n11h30: Quý khách trở lại tàu ăn trưa.\r\nSau đó, tiếp tục hành trình du thuyền đi giữa Hạ Long với hàng ngàn đảo đá sừng sững, Quý khách sẽ có cảm giác như mình đang đi vào thế giới động vật trải qua triệu năm hoá đá đó là: Hòn Chó đá, hòn Lư Hương, hòn Gà Chọi…, Quý khách tham quan làng chài.\r\n12h00: Quý khách trở lại bến tàu Bãi Cháy, trở về khách sạn nghỉ ngơi.\r\nChiều: Quý khách tự do nghỉ ngơi và tắm biển Bãi Cháy. Hoặc tham quan Khu vui chơi giải trí Tuần Châu với nhiều chương trình giải trí hấp dẫn như: xiếc Cá Sấu, xiếc Khỉ, biểu diễn Sư tử, Hải Cẩu, Trình diễn nhạc nước… (Chi phí tự túc).\r\n\r\nTối: Ăn tối tại nhà hàng. Tự do dạo chơi Hạ Long về đêm. Quý khách nghỉ đêm tại khách sạn.'),
(40, 1, 3, 'Hạ Long - Hà Nội (Ăn sáng, trưa)', 'Sáng: Quý khách ăn sáng tại khách sạn. Xe đưa Quý khách đi mua sắm tại chợ Hạ Long 1: mua sắm những sản vật địa phương về làm quà cho người thân và bạn bè….\r\n11h00: Quý khách trở về khách sạn làm thủ tục trả phòng và ăn trưa tại nhà hàng.\r\nChiều Quý khách lên xe về Hà Nội, trên đường về dừng chân thưởng thức và mua đặc sản Hải Dương (bánh đậu xanh, bánh gai, vải khô…)\r\nVề tới điểm hẹn, HDV chia tay và hẹn gặp lại Quý khách, kết thúc chương trình.');

-- --------------------------------------------------------

--
-- Table structure for table `loaitour`
--

CREATE TABLE `loaitour` (
  `MaLoaiTour` int NOT NULL,
  `TenLoai` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `MoTa` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loaitour`
--

INSERT INTO `loaitour` (`MaLoaiTour`, `TenLoai`, `MoTa`) VALUES
(1, 'Tour Trong Nước', NULL),
(2, 'Tour Quốc Tế', NULL),
(3, 'Tour Theo Yêu Cầu', NULL);

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
(2, 'hdv1', '123456', 'Nguyễn Văn An', 'HDV', 1, NULL, 'Hoạt động'),
(3, 'hdv2', '123456', 'Trần Thị Bích', 'HDV', 2, NULL, 'Hoạt động'),
(4, 'hdv3', '123456', 'Lê Văn Cường', 'HDV', 3, NULL, 'Hoạt động'),
(5, 'hdv4', '123456', 'Phạm Thị Dung', 'HDV', 4, NULL, 'Hoạt động'),
(6, 'hdv5', '123456', 'Hoàng Văn Em', 'HDV', 5, NULL, 'Hoạt động'),
(7, 'hdv6', '123456', 'Đỗ Thị Fượng', 'HDV', 6, NULL, 'Hoạt động'),
(8, 'hdv7', '123456', 'Ngô Văn Giang', 'HDV', 7, NULL, 'Hoạt động'),
(9, 'hdv8', '123456', 'Bùi Thị Huệ', 'HDV', 8, NULL, 'Hoạt động'),
(10, 'hdv9', '123456', 'Đặng Văn Ích', 'HDV', 9, NULL, 'Hoạt động'),
(11, 'hdv10', '123456', 'Vũ Thị Kim', 'HDV', 10, NULL, 'Hoạt động'),
(12, 'hdv11', '123456', 'Lý Văn Long', 'HDV', 11, NULL, 'Hoạt động'),
(13, 'hdv12', '123456', 'Mai Thị Mận', 'HDV', 12, NULL, 'Hoạt động'),
(14, 'hdv13', '123456', 'Trương Văn Nam', 'HDV', 13, NULL, 'Hoạt động'),
(15, 'hdv14', '123456', 'Đinh Thị Oanh', 'HDV', 14, NULL, 'Hoạt động'),
(16, 'hdv15', '123456', 'Lâm Văn Phú', 'HDV', 15, NULL, 'Hoạt động'),
(17, 'hdv16', '123456', 'Hà Thị Quyên', 'HDV', 16, NULL, 'Hoạt động'),
(18, 'hdv17', '123456', 'Kiều Văn Ri', 'HDV', 17, NULL, 'Hoạt động'),
(19, 'hdv18', '123456', 'Dương Thị Sen', 'HDV', 18, NULL, 'Hoạt động'),
(20, 'hdv19', '123456', 'Tống Văn Tài', 'HDV', 19, NULL, 'Hoạt động'),
(21, 'hdv20', '123456', 'Cao Thị Uyên', 'HDV', 20, NULL, 'Hoạt động');

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
(1, 'Nguyễn Văn An', NULL, '0901000001', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(2, 'Trần Thị Bích', NULL, '0901000002', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(3, 'Lê Văn Cường', NULL, '0901000003', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(4, 'Phạm Thị Dung', NULL, '0901000004', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(5, 'Hoàng Văn Em', NULL, '0901000005', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(6, 'Đỗ Thị Fượng', NULL, '0901000006', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(7, 'Ngô Văn Giang', NULL, '0901000007', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(8, 'Bùi Thị Huệ', NULL, '0901000008', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(9, 'Đặng Văn Ích', NULL, '0901000009', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(10, 'Vũ Thị Kim', NULL, '0901000010', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(11, 'Lý Văn Long', NULL, '0901000011', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(12, 'Mai Thị Mận', NULL, '0901000012', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(13, 'Trương Văn Nam', NULL, '0901000013', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(14, 'Đinh Thị Oanh', NULL, '0901000014', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(15, 'Lâm Văn Phú', NULL, '0901000015', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(16, 'Hà Thị Quyên', NULL, '0901000016', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(17, 'Kiều Văn Ri', NULL, '0901000017', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(18, 'Dương Thị Sen', NULL, '0901000018', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(19, 'Tống Văn Tài', NULL, '0901000019', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động'),
(20, 'Cao Thị Uyên', NULL, '0901000020', NULL, NULL, NULL, 'HDV', 'Tour trong nước', 'Hoạt động');

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
(1, 'Vietnam Airlines', 'Vận chuyển', NULL, NULL, 'Hoạt động'),
(2, 'Xe Du lịch Minh Tâm', 'Vận chuyển', NULL, NULL, 'Hoạt động'),
(3, 'Khách sạn Mường Thanh', 'Lưu trú', NULL, NULL, 'Hoạt động');

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
(40, 1, 16, 'Hướng dẫn viên'),
(41, 2, 12, 'Hướng dẫn viên'),
(42, 2, 2, 'Hướng dẫn viên'),
(45, 4, 13, 'Hướng dẫn viên'),
(46, 4, 15, 'Hướng dẫn viên'),
(47, 5, 9, 'Hướng dẫn viên'),
(48, 5, 18, 'Hướng dẫn viên'),
(51, 7, 5, 'Hướng dẫn viên'),
(52, 8, 13, 'Hướng dẫn viên'),
(53, 8, 2, 'Hướng dẫn viên'),
(54, 9, 9, 'Hướng dẫn viên'),
(55, 9, 12, 'Hướng dẫn viên'),
(56, 10, 11, 'Hướng dẫn viên'),
(57, 11, 6, 'Hướng dẫn viên'),
(58, 11, 17, 'Hướng dẫn viên'),
(59, 12, 19, 'Hướng dẫn viên'),
(60, 12, 7, 'Hướng dẫn viên'),
(61, 12, 17, 'Hướng dẫn viên'),
(62, 13, 1, 'Hướng dẫn viên'),
(63, 13, 7, 'Hướng dẫn viên'),
(64, 13, 10, 'Hướng dẫn viên'),
(65, 14, 17, 'Hướng dẫn viên'),
(66, 15, 7, 'Hướng dẫn viên'),
(67, 15, 12, 'Hướng dẫn viên'),
(68, 16, 13, 'Hướng dẫn viên'),
(69, 16, 7, 'Hướng dẫn viên'),
(70, 17, 19, 'Hướng dẫn viên'),
(71, 18, 8, 'Hướng dẫn viên'),
(74, 3, 2, 'Hướng dẫn viên'),
(75, 3, 15, 'Hướng dẫn viên'),
(76, 6, 6, 'Hướng dẫn viên'),
(77, 6, 18, 'Hướng dẫn viên');

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
(2, 3, 1, NULL, NULL),
(3, 3, 3, NULL, NULL);

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
(1, 2, 'Xe 45 chỗ - 29B.99999', 45, NULL),
(2, 2, 'Xe 29 chỗ - 29B.88888', 29, NULL),
(3, 3, 'Phòng Deluxe Sea View', 2, NULL);

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
  `TrangThai` varchar(50) DEFAULT 'Hoạt động',
  `NgayTao` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tour`
--

INSERT INTO `tour` (`MaTour`, `MaLoaiTour`, `TenTour`, `HinhAnh`, `MoTa`, `ChinhSach`, `SoNgay`, `TrangThai`, `NgayTao`) VALUES
(1, 1, 'Tour Du Lịch Hà Nội – Hạ Long – Sunworld', '1765024453_halong2.jpg', 'Vịnh Hạ Long như một bức tranh khổng lồ vô cùng sống động. Ði giữa Hạ Long, ta ngỡ như lạc vào một thế giới cổ tích bị hoá đá, đảo thì giống hình một người đang đứng hướng về đất liền (hòn Ðầu Người), đảo thì giống như một con rồng đang bay lượn trên mặt nước (hòn Rồng), đảo thì giống như một ông lão đang ngồi câu cá (hòn Ông Lã Vọng), hòn Cánh Buồm, hòn Cặp Gà, hòn Lư Hương…\r\nNgày 1: Hà Nội – Hạ Long (Ăn trưa, tối)\r\nNgày 2: Tham quan vịnh Hạ Long (Ăn sáng, trưa, tối)\r\nNgày 3: Hạ Long – Hà Nội (Ăn sáng, trưa)', 'Sau khi đăng ký, thanh toán ít nhất 50% tiền cọc và đóng hết 100% trước khởi hành 7 ngày. \r\nDời ngày khởi hành trước 10 ngày không mất phí, sau 8 ngày mất 20%. (Không tính ngày lễ và chủ nhật), chỉ được dời 1 lần.\r\nSau khi đăng ký huỷ tour mất 10% giá tour \r\nTừ 10 đến trước 8 ngày trước ngày khởi hành chịu phí 30% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 8 đến 6 ngày trước ngày khởi hành chịu phí 50% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 3-5 ngày trước ngày khởi hành chịu phí 70% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 2 ngày trước ngày khởi hành chịu phí 100% giá tour. (Không tính ngày lễ và chủ nhật)\r\nCác quy định trên không áp dụng cho các dịp lễ và tết.\r\nCác ngày lễ tết việc dời ngày và hủy tour trước 15 ngày khởi hành mất 50% giá tour. Sau 15 ngày so với ngày khởi hành mất 100% giá tour. (Không tính ngày chủ nhật.)\r\nSau khi hủy tour, du khách vui lòng đến công ty nhận tiền trong vòng 2 tuần kể từ ngày đăng ký hủy tour. Chúng tôi chỉ thanh toán trong thời gian 14 ngày nói trên. \r\nTrường hợp hủy tour do sự cố khách quan như thiên tai, dịch bệnh hoặc do tàu thủy, xe lửa, máy bay hoãn/hủy chuyến, CTY sẽ không chịu trách nhiệm bồi thường thêm bất kỳ chi phí nào khác ngoài việc hoàn trả chi phí những dịch vụ chưa được sử dụng của tour đó.', 3, 'Hoạt động', '2025-11-30 04:29:49'),
(2, 1, 'TOUR HÀ NỘI – ĐÀ NẴNG – BÀ NÀ HILL – PHỐ CỔ HỘI AN – CỐ ĐÔ HUẾ', '1765024325_danang2.jpg', 'Cầu Trường Tiền soi bóng xuống dòng Hương Giang vẫn ngày ngày nối đôi bờ Thành phố Huế: một bên là thành nội trầm mặc, cổ kính, uy nghi… một bên sôi động, mang dáng dấp của đô thị hiện đại. Kinh Thành vẫn còn đó, với Ngọ Môn, Cửu Đỉnh, Kỳ Đài, Tử Cấm Thành, Hiển Lâm Các, Thế Miếu… Rồi các công trình đền đài, lăng tẩm của các vị Vua triều Nguyễn vẫn là những công trình đặc sắc, không thể bỏ qua khi đến mảnh đất Cố Đô.\r\nĐà Nẵng, thành phố của những cây cầu nối đôi bờ Sông Hàn, thành phố đáng sống bậc nhất Việt Nam. Cầu Rồng, Cầu Quay Sông Hàn, Cầu Trần Thị Lý, Cầu Thuận Phước vẫn ngày ngày mang sứ mệnh nối 1 bên là trung tâm hành chính quan trọng và 1 bên là khu du lịch biển nổi tiếng khắp trong và ngoài nước. Bà Nà như một nàng công chúa ngủ trong rừng từ ngàn xưa và bỗng bừng tỉnh mang lại 1 diện mạo đặc sắc và hấp dẫn cho Đà Nẵng. Phố Cổ Hội An với Chùa Cầu, Hội Quán…vẫn ngày đêm soi mình xuống dòng sông Hoài lịch sử.', 'Sau khi đăng ký, thanh toán ít nhất 50% tiền cọc và đóng hết 100% trước khởi hành 7 ngày. \r\nDời ngày khởi hành trước 10 ngày không mất phí, sau 8 ngày mất 20%. (Không tính ngày lễ và chủ nhật), chỉ được dời 1 lần.\r\nSau khi đăng ký huỷ tour mất 10% giá tour \r\nTừ 10 đến trước 8 ngày trước ngày khởi hành chịu phí 30% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 8 đến 6 ngày trước ngày khởi hành chịu phí 50% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 3-5 ngày trước ngày khởi hành chịu phí 70% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 2 ngày trước ngày khởi hành chịu phí 100% giá tour. (Không tính ngày lễ và chủ nhật)\r\nCác quy định trên không áp dụng cho các dịp lễ và tết.\r\nCác ngày lễ tết việc dời ngày và hủy tour trước 15 ngày khởi hành mất 50% giá tour. Sau 15 ngày so với ngày khởi hành mất 100% giá tour. (Không tính ngày chủ nhật.)\r\nSau khi hủy tour, du khách vui lòng đến công ty nhận tiền trong vòng 2 tuần kể từ ngày đăng ký hủy tour. Chúng tôi chỉ thanh toán trong thời gian 14 ngày nói trên. \r\nTrường hợp hủy tour do sự cố khách quan như thiên tai, dịch bệnh hoặc do tàu thủy, xe lửa, máy bay hoãn/hủy chuyến, CTY sẽ không chịu trách nhiệm bồi thường thêm bất kỳ chi phí nào khác ngoài việc hoàn trả chi phí những dịch vụ chưa được sử dụng của tour đó.', 4, 'Hoạt động', '2025-11-30 04:29:49'),
(3, 1, 'Tour Du Lịch Hà Nội – Phú Quốc – Bãi Sao – Hộ Quốc – Vinpearl Land', '1765024157_phuquoc3.jpg', 'Phú Quốc được xem là một quần đảo đẹp và lớn nhất Việt Nam. Từ lâu Phú Quốc đã được mệnh danh là hòn đảo ngọc trên vùng biển Tây Nam của Tổ quốc mang vẻ đẹp hoang sơ, huyền bí với những bãi biển đẹp làm say lòng biết bao du khách. Thiên nhiên đã ban tặng cho Đảo Phú Quốc tất cả những giá trị tuyệt mỹ của một thiên đường du lịch biển. Những bãi biển cát trắng dài bất tận, nước biển xanh ngọc bích, thế giới san hô sinh vật biển đầy sắc màu, những khu rừng nguyên sinh với hệ sinh thái vô cùng phong phú….. Phú Quốc là điểm đến dành cho du khách yêu thích hình thức du lịch nghỉ dưỡng và khám phá sinh thái tuyệt vời. Hành trình đến với thiên nhiên và chiêm ngưỡng thế giới san hô và cá biển sặc sỡ, Bãi Sao cát trắng mịn, những địa điểm ngắm hoàng hôn đẹp nhất là một món quà dành cho những du khách muốn có một chuyến đi Phú Quốc, tiết kiệm, an toàn, và tuyệt vời nhất.', 'Sau khi đăng ký, thanh toán ít nhất 50% tiền cọc và đóng hết 100% trước khởi hành 7 ngày. \r\nDời ngày khởi hành trước 10 ngày không mất phí, sau 8 ngày mất 20%. (Không tính ngày lễ và chủ nhật), chỉ được dời 1 lần.\r\nSau khi đăng ký huỷ tour mất 10% giá tour \r\nTừ 10 đến trước 8 ngày trước ngày khởi hành chịu phí 30% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 8 đến 6 ngày trước ngày khởi hành chịu phí 50% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 3-5 ngày trước ngày khởi hành chịu phí 70% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 2 ngày trước ngày khởi hành chịu phí 100% giá tour. (Không tính ngày lễ và chủ nhật)\r\nCác quy định trên không áp dụng cho các dịp lễ và tết.\r\nCác ngày lễ tết việc dời ngày và hủy tour trước 15 ngày khởi hành mất 50% giá tour. Sau 15 ngày so với ngày khởi hành mất 100% giá tour. (Không tính ngày chủ nhật.)\r\nSau khi hủy tour, du khách vui lòng đến công ty nhận tiền trong vòng 2 tuần kể từ ngày đăng ký hủy tour. Chúng tôi chỉ thanh toán trong thời gian 14 ngày nói trên. \r\nTrường hợp hủy tour do sự cố khách quan như thiên tai, dịch bệnh hoặc do tàu thủy, xe lửa, máy bay hoãn/hủy chuyến, CTY sẽ không chịu trách nhiệm bồi thường thêm bất kỳ chi phí nào khác ngoài việc hoàn trả chi phí những dịch vụ chưa được sử dụng của tour đó.', 3, 'Hoạt động', '2025-11-30 04:29:49'),
(4, 2, 'SA-WA-DEE, THAI! KHÁM PHÁ THÁI LAN, ĐẤT NƯỚC CHÙA VÀNG', '1765023927_thailan1.jpg', '✔    Tham quan Chùa Phật Lớn tại Pattaya\r\n✔    Tham quan Vườn hoa nhiệt đới Nong Nooch lớn nhất Châu Á\r\n✔    Đi tàu cao tốc và tắm biển tại đảo san hô – Coral island\r\n✔    Vé tham dự show Bede Alcazar\r\n✔    Vé show nhạc nước\r\n✔    Tặng 01 bữa Buffet tại tòa nhà Baiyoke Sky 84 tầng tại Bangkok\r\n✔    Tặng 01 bữa BBQ tiệc nướng hải sản\r\n✔    Lễ Phật bốn mặt vô cùng linh thiêng tại đền thờ Erawan\r\n✔    Thỏa thích mua sắm tại trung tâm thương mại lớn: BigC, Central World,…', 'Sau khi đăng ký, thanh toán ít nhất 50% tiền cọc và đóng hết 100% trước khởi hành 7 ngày. \r\nDời ngày khởi hành trước 10 ngày không mất phí, sau 8 ngày mất 20%. (Không tính ngày lễ và chủ nhật), chỉ được dời 1 lần.\r\nSau khi đăng ký huỷ tour mất 10% giá tour \r\nTừ 10 đến trước 8 ngày trước ngày khởi hành chịu phí 30% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 8 đến 6 ngày trước ngày khởi hành chịu phí 50% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 3-5 ngày trước ngày khởi hành chịu phí 70% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 2 ngày trước ngày khởi hành chịu phí 100% giá tour. (Không tính ngày lễ và chủ nhật)\r\nCác quy định trên không áp dụng cho các dịp lễ và tết.\r\nCác ngày lễ tết việc dời ngày và hủy tour trước 15 ngày khởi hành mất 50% giá tour. Sau 15 ngày so với ngày khởi hành mất 100% giá tour. (Không tính ngày chủ nhật.)\r\nSau khi hủy tour, du khách vui lòng đến công ty nhận tiền trong vòng 2 tuần kể từ ngày đăng ký hủy tour. Chúng tôi chỉ thanh toán trong thời gian 14 ngày nói trên. \r\nTrường hợp hủy tour do sự cố khách quan như thiên tai, dịch bệnh hoặc do tàu thủy, xe lửa, máy bay hoãn/hủy chuyến, CTY sẽ không chịu trách nhiệm bồi thường thêm bất kỳ chi phí nào khác ngoài việc hoàn trả chi phí những dịch vụ chưa được sử dụng của tour đó.', 5, 'Hoạt động', '2025-11-30 04:29:49'),
(5, 2, 'DU LỊCH HÀN QUỐC: SEOUL – NAMI – SEOUL GRAND PARK', '1765023714_seoul2.jpg', '“Cung điện Hoàng Gia – Cảnh Phúc Cung” từ triều đại JoSeon (Triều Tiên) năm 1392-1910.\r\nTham quan Nhà Xanh – Dinh Tổng Thống.\r\nĐảo Nami – Hòn đảo lãng mạn bậc nhất xứ Hàn\r\n“Seoul Grand Park” công viên phức hợp lớn nhất của thủ đô Seoul.\r\n“Tháp Namsan” tọa lạc trên núi Namsan và đã trở thành một biểu tượng của Seoul.\r\nThỏa sức shopping tại Myengdong – khu phố sầm uất nhất Seoul.\r\nTặng vé xem chương trình nghệ thuật Hero Painter Show.\r\nHọc cách trang điểm, sử dụng sản phẩm dưỡng da hàng đầu Hàn Quốc.', 'Sau khi đăng ký, thanh toán ít nhất 50% tiền cọc và đóng hết 100% trước khởi hành 7 ngày. \r\nDời ngày khởi hành trước 10 ngày không mất phí, sau 8 ngày mất 20%. (Không tính ngày lễ và chủ nhật), chỉ được dời 1 lần.\r\nSau khi đăng ký huỷ tour mất 10% giá tour \r\nTừ 10 đến trước 8 ngày trước ngày khởi hành chịu phí 30% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 8 đến 6 ngày trước ngày khởi hành chịu phí 50% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 3-5 ngày trước ngày khởi hành chịu phí 70% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 2 ngày trước ngày khởi hành chịu phí 100% giá tour. (Không tính ngày lễ và chủ nhật)\r\nCác quy định trên không áp dụng cho các dịp lễ và tết.\r\nCác ngày lễ tết việc dời ngày và hủy tour trước 15 ngày khởi hành mất 50% giá tour. Sau 15 ngày so với ngày khởi hành mất 100% giá tour. (Không tính ngày chủ nhật.)\r\nSau khi hủy tour, du khách vui lòng đến công ty nhận tiền trong vòng 2 tuần kể từ ngày đăng ký hủy tour. Chúng tôi chỉ thanh toán trong thời gian 14 ngày nói trên. \r\nTrường hợp hủy tour do sự cố khách quan như thiên tai, dịch bệnh hoặc do tàu thủy, xe lửa, máy bay hoãn/hủy chuyến, CTY sẽ không chịu trách nhiệm bồi thường thêm bất kỳ chi phí nào khác ngoài việc hoàn trả chi phí những dịch vụ chưa được sử dụng của tour đó.', 5, 'Hoạt động', '2025-11-30 04:29:49'),
(6, 2, 'DU LỊCH NHẬT BẢN HÈ RỰC RỠ: TOKYO – PHÚ SĨ – NAGOYA – KYOTO – OSAKA', '1765023592_nhatban2.jpg', 'Khám phá cung đường vàng với những danh lam thắng cảnh nổi tiếng ở Nhật Bản\r\nGhé thăm Làng cổ Oshino Hakkai ngôi làng cổ dưới chân núi Phú Sĩ\r\nTặng 01 bữa cua Tuyết\r\nTrải nghiệm tắm suối nóng Onsen truyền thống\r\nTrải nghiệm tàu Shinkansen với vận tốc 300km/h\r\nThỏa sức mua sắm đồ lưu niệm, mỹ phẩm, các mặt hàng thời trang tại các khu phố thương mại nổi tiếng tại Nhật Bản', 'Sau khi đăng ký, thanh toán ít nhất 50% tiền cọc và đóng hết 100% trước khởi hành 7 ngày. \r\nDời ngày khởi hành trước 10 ngày không mất phí, sau 8 ngày mất 20%. (Không tính ngày lễ và chủ nhật), chỉ được dời 1 lần.\r\nSau khi đăng ký huỷ tour mất 10% giá tour \r\nTừ 10 đến trước 8 ngày trước ngày khởi hành chịu phí 30% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 8 đến 6 ngày trước ngày khởi hành chịu phí 50% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 3-5 ngày trước ngày khởi hành chịu phí 70% giá tour. (Không tính ngày lễ và chủ nhật)\r\nTừ 2 ngày trước ngày khởi hành chịu phí 100% giá tour. (Không tính ngày lễ và chủ nhật)\r\nCác quy định trên không áp dụng cho các dịp lễ và tết.\r\nCác ngày lễ tết việc dời ngày và hủy tour trước 15 ngày khởi hành mất 50% giá tour. Sau 15 ngày so với ngày khởi hành mất 100% giá tour. (Không tính ngày chủ nhật.)\r\nSau khi hủy tour, du khách vui lòng đến công ty nhận tiền trong vòng 2 tuần kể từ ngày đăng ký hủy tour. Chúng tôi chỉ thanh toán trong thời gian 14 ngày nói trên. \r\nTrường hợp hủy tour do sự cố khách quan như thiên tai, dịch bệnh hoặc do tàu thủy, xe lửa, máy bay hoãn/hủy chuyến, CTY sẽ không chịu trách nhiệm bồi thường thêm bất kỳ chi phí nào khác ngoài việc hoàn trả chi phí những dịch vụ chưa được sử dụng của tour đó.', 6, 'Hoạt động', '2025-11-30 04:29:49');

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
  MODIFY `MaBooking` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

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
  MODIFY `MaChiTiet` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

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
  MODIFY `MaHinhAnh` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `MaKhachHang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `lichkhoihanh`
--
ALTER TABLE `lichkhoihanh`
  MODIFY `MaLichKhoiHanh` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `lichtrinhtour`
--
ALTER TABLE `lichtrinhtour`
  MODIFY `MaLichTrinh` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `loaitour`
--
ALTER TABLE `loaitour`
  MODIFY `MaLoaiTour` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `MaNguoiDung` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `nhansu`
--
ALTER TABLE `nhansu`
  MODIFY `MaNhanSu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `nhatkytour`
--
ALTER TABLE `nhatkytour`
  MODIFY `MaNhatKy` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  MODIFY `MaNhaCungCap` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `phanbonhansu`
--
ALTER TABLE `phanbonhansu`
  MODIFY `MaPhanBo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `phan_bo_tai_nguyen`
--
ALTER TABLE `phan_bo_tai_nguyen`
  MODIFY `MaPhanBo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tai_nguyen_ncc`
--
ALTER TABLE `tai_nguyen_ncc`
  MODIFY `MaTaiNguyen` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
