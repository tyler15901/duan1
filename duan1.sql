-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 01, 2025 at 11:03 PM
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
(12, 'BK32', 3, 1, 12, '2026-01-20', '2026-01-30', 6, 30000000.00, NULL, 'Đã xác nhận', 'Đã thanh toán', NULL, NULL, NULL),
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
(95, 'aaaaaaaaa', NULL, '33333333333333', '', '', NULL, '2025-12-02 06:01:17');

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
  `SoKhachHienTai` int DEFAULT '0',
  `GiaNguoiLon` decimal(18,2) DEFAULT '0.00',
  `GiaTreEm` decimal(18,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lichkhoihanh`
--

INSERT INTO `lichkhoihanh` (`MaLichKhoiHanh`, `MaTour`, `LichCode`, `NgayKhoiHanh`, `NgayKetThuc`, `GioTapTrung`, `DiaDiemTapTrung`, `TrangThai`, `SoKhachHienTai`, `GiaNguoiLon`, `GiaTreEm`) VALUES
(1, 1, 'LKH1162', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 'Hoàn tất', 8, 0.00, 0.00),
(2, 1, 'LKH1229', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 'Nhận khách', 23, 0.00, 0.00),
(3, 1, 'LKH1356', '2026-01-20', '2026-01-23', '11:11:00', 'aaaaaaaa', 'Nhận khách', 26, 11101111.00, 1110111.00),
(4, 2, 'LKH2141', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 'Hoàn tất', 32, 0.00, 0.00),
(5, 2, 'LKH2274', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 'Nhận khách', 33, 0.00, 0.00),
(6, 2, 'LKH2315', '2026-01-30', '2026-02-02', '06:00:00', 'Nhà Hát Lớn', 'Nhận khách', 34, 111111111.00, 11111110.00),
(7, 3, 'LKH3169', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 'Hoàn tất', 10, 0.00, 0.00),
(8, 3, 'LKH3242', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 'Nhận khách', 30, 0.00, 0.00),
(9, 3, 'LKH3341', '2026-01-30', '2026-02-02', '06:00:00', 'Nhà Hát Lớn', 'Nhận khách', 40, 0.00, 0.00),
(10, 4, 'LKH4160', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 'Hoàn tất', 20, 0.00, 0.00),
(11, 4, 'LKH4229', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 'Nhận khách', 27, 0.00, 0.00),
(12, 4, 'LKH4328', '2026-01-30', '2026-02-02', '06:00:00', 'Nhà Hát Lớn', 'Nhận khách', 41, 0.00, 0.00),
(13, 5, 'LKH518', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 'Hoàn tất', 52, 0.00, 0.00),
(14, 5, 'LKH5231', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 'Nhận khách', 19, 0.00, 0.00),
(15, 5, 'LKH5371', '2026-01-30', '2026-02-02', '06:00:00', 'Nhà Hát Lớn', 'Nhận khách', 38, 0.00, 0.00),
(16, 6, 'LKH6194', '2025-10-30', '2025-11-02', '06:00:00', 'Nhà Hát Lớn', 'Hoàn tất', 33, 0.00, 0.00),
(17, 6, 'LKH6266', '2025-12-07', '2025-12-10', '06:00:00', 'Nhà Hát Lớn', 'Nhận khách', 10, 0.00, 0.00),
(18, 6, 'LKH6318', '2026-01-30', '2026-02-02', '06:00:00', 'Nhà Hát Lớn', 'Nhận khách', 18, 0.00, 0.00);

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
  `SoChoToiDa` int DEFAULT '40',
  `TrangThai` varchar(50) DEFAULT 'Hoạt động',
  `NgayTao` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tour`
--

INSERT INTO `tour` (`MaTour`, `MaLoaiTour`, `TenTour`, `HinhAnh`, `MoTa`, `ChinhSach`, `SoNgay`, `SoChoToiDa`, `TrangThai`, `NgayTao`) VALUES
(1, 1, 'Hà Nội - Sapa - Fansipan (3N2Đ)', 'sapa.jpg', NULL, NULL, 3, 40, 'Hoạt động', '2025-11-30 04:29:49'),
(2, 1, 'Đà Nẵng - Hội An - Bà Nà (4N3Đ)', 'danang.jpg', NULL, NULL, 4, 40, 'Hoạt động', '2025-11-30 04:29:49'),
(3, 1, 'Phú Quốc - Đảo Ngọc (3N2Đ)', 'phuquoc.jpg', NULL, NULL, 3, 40, 'Hoạt động', '2025-11-30 04:29:49'),
(4, 2, 'Bangkok - Pattaya (Thái Lan 5N4Đ)', 'thailan.jpg', NULL, NULL, 5, 40, 'Hoạt động', '2025-11-30 04:29:49'),
(5, 2, 'Seoul - Nami (Hàn Quốc 5N4Đ)', 'hanquoc.jpg', NULL, NULL, 5, 30, 'Hoạt động', '2025-11-30 04:29:49'),
(6, 2, 'Tokyo - Osaka (Nhật Bản 6N5Đ)', 'nhatban.jpg', '', '', 6, 30, 'Hoạt động', '2025-11-30 04:29:49');

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
  MODIFY `MaBooking` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

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
  MODIFY `MaChiTiet` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

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
  MODIFY `MaKhachHang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `lichkhoihanh`
--
ALTER TABLE `lichkhoihanh`
  MODIFY `MaLichKhoiHanh` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lichtrinhtour`
--
ALTER TABLE `lichtrinhtour`
  MODIFY `MaLichTrinh` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `MaPhanBo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `phan_bo_tai_nguyen`
--
ALTER TABLE `phan_bo_tai_nguyen`
  MODIFY `MaPhanBo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tai_nguyen_ncc`
--
ALTER TABLE `tai_nguyen_ncc`
  MODIFY `MaTaiNguyen` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tour`
--
ALTER TABLE `tour`
  MODIFY `MaTour` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
