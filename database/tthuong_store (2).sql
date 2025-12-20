-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 16, 2025 lúc 02:06 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `tthuong_store`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `admin_id`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2024-12-07 14:47:31'),
(2, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2024-12-07 15:17:15'),
(3, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2024-12-07 15:18:12'),
(4, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2024-12-12 17:46:12'),
(5, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2024-12-13 04:59:03'),
(6, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2024-12-21 13:18:25'),
(7, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2024-12-27 05:00:05'),
(8, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2024-12-29 02:33:49'),
(9, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-01-14 13:31:16'),
(10, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-01-15 06:27:02'),
(11, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-01-15 07:08:01'),
(12, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-21 04:03:31'),
(13, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-21 05:30:19'),
(14, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-21 05:56:55'),
(15, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-21 07:01:33'),
(16, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-22 05:46:55'),
(17, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-11-22 05:50:48'),
(18, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-22 05:51:00'),
(19, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-11-22 05:51:06'),
(20, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-22 05:54:42'),
(21, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-11-22 06:23:48'),
(22, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-11-22 06:27:46'),
(23, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-22 06:27:52'),
(24, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-23 13:41:08'),
(25, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-23 14:22:40'),
(26, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-25 06:14:33'),
(27, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-25 14:37:22'),
(28, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-11-25 14:49:48'),
(29, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-25 15:21:33'),
(30, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-11-25 15:54:31'),
(31, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 12:44:07'),
(32, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-11-28 13:15:09'),
(33, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:15:14'),
(34, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:18:48'),
(35, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:24:36'),
(36, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:25:54'),
(37, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:29:27'),
(38, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:30:36'),
(39, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:30:45'),
(40, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:31:09'),
(41, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:31:18'),
(42, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:32:35'),
(43, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:39:08'),
(44, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-11-28 13:45:07'),
(45, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-02 10:52:03'),
(46, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-02 10:57:21'),
(47, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-02 11:47:29'),
(48, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-02 11:48:34'),
(49, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-02 12:00:23'),
(50, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-02 12:10:24'),
(51, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-06 11:57:43'),
(52, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-06 12:04:57'),
(53, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-06 12:14:17'),
(54, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-08 12:35:23'),
(55, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-08 12:46:51'),
(56, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-08 12:46:57'),
(57, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-08 12:47:11'),
(58, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-08 12:48:28'),
(59, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-08 12:48:50'),
(60, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-08 13:00:37'),
(61, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-08 13:04:24'),
(62, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-08 13:39:44'),
(63, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-08 13:45:44'),
(64, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-14 14:58:06'),
(65, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '192.168.1.7', '2025-12-14 15:23:47'),
(66, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-14 15:32:48'),
(67, 1, 'logout', 'Đăng xuất khỏi hệ thống', '192.168.1.7', '2025-12-14 15:42:19'),
(68, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-15 06:24:12'),
(69, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-16 04:04:53'),
(70, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-16 04:05:00'),
(71, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-16 04:05:03'),
(72, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-16 04:23:42'),
(73, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-16 04:24:37'),
(74, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-16 04:56:04'),
(75, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-16 04:56:18'),
(76, 1, 'login', 'Đăng nhập thành công vào hệ thống quản trị', '::1', '2025-12-16 04:56:24'),
(77, 1, 'logout', 'Đăng xuất khỏi hệ thống', '::1', '2025-12-16 04:56:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `administrators`
--

CREATE TABLE `administrators` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `administrators`
--

INSERT INTO `administrators` (`admin_id`, `username`, `password`, `full_name`, `email`, `phone`, `created_at`, `last_login`, `status`) VALUES
(1, 'admin', '$2y$10$RcxuN.GUn3k1DTMXPfMFKuhk63gu26yG1p51IRzptTTmsZma2aLZK', 'Administrator', 'admin@tthuong.com', '0392656499', '2024-12-05 15:38:44', '2025-12-16 04:56:24', 'active');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `admin_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `authors`
--

CREATE TABLE `authors` (
  `author_id` int(11) NOT NULL,
  `author_name` varchar(255) NOT NULL,
  `pen_name` varchar(255) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `awards` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `authors`
--

INSERT INTO `authors` (`author_id`, `author_name`, `pen_name`, `biography`, `birth_date`, `nationality`, `email`, `photo`, `website`, `awards`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Dale Carnegie', NULL, 'Tac gia nguoi My noi tieng ve sach phat trien ban than va ky nang mem', '1888-11-24', 'My', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(2, 'Paulo Coelho', NULL, 'Tieu thuyet gia nguoi Brazil, tac gia cuon Nha gia kim noi tieng', '1947-08-24', 'Brazil', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(3, 'Jose Mauro de Vasconcelos', NULL, 'Nha van Brazil noi tieng voi tac pham Cay cam ngot cua toi', '1920-02-26', 'Brazil', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(4, 'Tony Buoi Sang', NULL, 'Tac gia va nha khoi nghiep nguoi Viet, noi tieng voi cac bai viet truyen cam hung', '1974-01-01', 'Viet Nam', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(5, 'Rosie Nguyen', NULL, 'Tac gia tre Viet Nam viet ve phat trien ban than va ky nang song', '1990-01-01', 'Viet Nam', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(6, 'Jim Collins', NULL, 'Nha nghien cuu va tu van quan tri noi tieng nguoi My', '1958-01-25', 'My', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(7, 'Napoleon Hill', NULL, 'Tac gia My ve phat trien ban than, noi tieng voi cuon Nghi giau va lam giau', '1883-10-26', 'My', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(8, 'Robin Sharma', NULL, 'Tac gia Canada ve lanh dao va phat trien ban than', '1964-06-16', 'Canada', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(9, 'T. Harv Eker', NULL, 'Tac gia va dien gia motivational nguoi Canada', '1954-06-10', 'Canada', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(10, 'W. Chan Kim', NULL, 'Giao su quan tri va dong tac gia cuon Chien luoc dai duong xanh', '1952-01-01', 'Han Quoc', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(11, 'Renee Mauborgne', NULL, 'Giao su quan tri va dong tac gia cuon Chien luoc dai duong xanh', '1963-01-01', 'My', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(12, 'To Hoai', NULL, 'Nha van Viet Nam voi tac pham noi tieng De Men phieu luu ky', '1920-09-27', 'Viet Nam', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(13, 'Antoine de Saint-Exupery', NULL, 'Nha van va phi cong nguoi Phap, tac gia Hoang tu be', '1900-06-29', 'Phap', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(14, 'Robert Louis Stevenson', NULL, 'Nha van Scotland noi tieng voi Dao giau vang', '1850-11-13', 'Scotland', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(15, 'J.K. Rowling', 'Robert Galbraith', 'Tac gia cua bo truyen Harry Potter noi tieng', '1965-07-31', 'Anh', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(16, 'Mark Twain', 'Samuel Clemens', 'Nha van nguoi My, noi tieng voi Tom Sawyer va Huckleberry Finn', '1835-11-30', 'My', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(17, 'Eiichiro Oda', NULL, 'Tac gia manga One Piece noi tieng nhat the gioi', '1975-01-01', 'Nhat Ban', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(18, 'Masashi Kishimoto', NULL, 'Tac gia manga Naruto', '1974-11-08', 'Nhat Ban', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(19, 'Gosho Aoyama', NULL, 'Tac gia manga Tham tu lung danh Conan', '1963-06-21', 'Nhat Ban', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(20, 'Akira Toriyama', NULL, 'Tac gia manga Dragon Ball huyen thoai', '1955-04-05', 'Nhat Ban', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(21, 'Fujiko F. Fujio', NULL, 'But danh cua Fujimoto Hiroshi, tac gia Doraemon', '1933-12-01', 'Nhat Ban', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(22, 'George Orwell', 'Eric Arthur Blair', 'Nha van Anh noi tieng voi 1984 va Trai chan nuoi', '1903-06-25', 'Anh', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(23, 'Yuval Noah Harari', NULL, 'Nha su hoc Israel, tac gia Sapiens va Homo Deus', '1976-02-24', 'Israel', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(24, 'Gabriel Garcia Marquez', NULL, 'Nha van Colombia, giai Nobel van chuong 1982', '1927-03-06', 'Colombia', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(25, 'Haruki Murakami', 'Murakami Haruki', 'Tieu thuyet gia va nha van nguoi Nhat Ban', '1949-01-12', 'Nhat Ban', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(26, 'Victor Hugo', NULL, 'Nha tho va tieu thuyet gia lon cua Phap', '1802-02-26', 'Phap', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(27, 'Vu Trong Phung', NULL, 'Nha van hien thuc phe phan Viet Nam', '1912-10-20', 'Viet Nam', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(28, 'Nam Cao', NULL, 'Nha van hien thuc Viet Nam, tac gia Lao Hac, Chi Pheo', '1915-10-29', 'Viet Nam', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(29, 'Kim Lan', NULL, 'Nha van Viet Nam voi tac pham Vo nhat noi tieng', '1920-07-01', 'Viet Nam', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(30, 'Ngo Tat To', NULL, 'Nha van Viet Nam voi tac pham Tat den', '1894-11-30', 'Viet Nam', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(31, 'Lev Tolstoy', NULL, 'Nha van vi dai nguoi Nga, tac gia Chien tranh va hoa binh', '1828-09-09', 'Nga', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(32, 'Fyodor Dostoevsky', NULL, 'Nha van Nga vi dai, tac gia Toi ac va trung phat', '1821-11-11', 'Nga', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(33, 'Honore de Balzac', NULL, 'Nha van Phap vi dai, tac gia Hai kich nhan sinh', '1799-05-20', 'Phap', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(34, 'Marguerite Duras', NULL, 'Nha van Phap, tac gia Nguoi tinh', '1914-04-04', 'Phap', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(35, 'Og Mandino', NULL, 'Tac gia My ve phat trien ban than va ban hang', '1923-12-12', 'My', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(36, 'Eric Ries', NULL, 'Doanh nhan va tac gia nguoi My, tac gia Khoi nghiep tinh gon', '1978-09-22', 'My', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(37, 'Dale Carnegie & Associates', NULL, 'To chuc dao tao ky nang cua Dale Carnegie', NULL, 'My', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(38, 'Daniel Kahneman', NULL, 'Nha tam ly hoc Israel, giai Nobel Kinh te 2002', '1934-03-05', 'Israel', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(39, 'Pamela Anderson', NULL, 'Tac gia sach kinh doanh', NULL, 'My', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(40, 'Nguyen Nhat Anh', NULL, 'Nha van noi tieng voi nhieu tac pham van hoc thieu nhi', '1955-05-07', 'Viet Nam', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(41, 'Emily Bronte', NULL, 'Nha van Anh, tac gia Doi gio hu', '1818-07-30', 'Anh', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(42, 'Arthur Conan Doyle', NULL, 'Nha van Anh, sang tac ra tham tu Sherlock Holmes', '1859-05-22', 'Anh', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(43, 'Hajime Isayama', NULL, 'Tac gia manga Attack on Titan', '1986-08-29', 'Nhat Ban', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(44, 'Kohei Horikoshi', NULL, 'Tac gia manga My Hero Academia', '1986-11-20', 'Nhat Ban', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(45, 'Koyoharu Gotouge', NULL, 'Tac gia manga Demon Slayer', '1989-05-05', 'Nhat Ban', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(46, 'Sui Ishida', NULL, 'Tac gia manga Tokyo Ghoul', '1986-12-28', 'Nhat Ban', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(47, 'Hiromu Arakawa', NULL, 'Tac gia manga Fullmetal Alchemist', '1973-05-08', 'Nhat Ban', NULL, NULL, NULL, NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `blog_categories`
--

CREATE TABLE `blog_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `blog_categories`
--

INSERT INTO `blog_categories` (`category_id`, `category_name`, `slug`, `description`, `status`, `created_at`) VALUES
(1, 'Tin tuc sach moi', 'tin-tuc-sach-moi', 'Cap nhat cac dau sach moi ra mat', 'active', '2025-11-28 14:42:06'),
(2, 'Review sach hay', 'review-sach-hay', 'Danh gia va gioi thieu sach', 'active', '2025-11-28 14:42:06'),
(3, 'Tac gia noi bat', 'tac-gia-noi-bat', 'Gioi thieu ve cac tac gia va nha van', 'active', '2025-11-28 14:42:06'),
(4, 'Meo doc sach', 'meo-doc-sach', 'Chia se kinh nghiem va phuong phap doc sach hieu qua', 'active', '2025-11-28 14:42:06'),
(5, 'Su kien van hoc', 'su-kien-van-hoc', 'Thong tin ve cac su kien, hoi sach', 'active', '2025-11-28 14:42:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `blog_comments`
--

CREATE TABLE `blog_comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `author_name` varchar(100) DEFAULT NULL,
  `author_email` varchar(100) DEFAULT NULL,
  `content` text NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `blog_comments`
--

INSERT INTO `blog_comments` (`comment_id`, `post_id`, `user_id`, `author_name`, `author_email`, `content`, `parent_id`, `status`, `created_at`) VALUES
(1, 1, 5, NULL, NULL, 'Hay', NULL, 'approved', '2025-12-02 11:26:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `blog_posts`
--

CREATE TABLE `blog_posts` (
  `post_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `views` int(11) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `blog_posts`
--

INSERT INTO `blog_posts` (`post_id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `category_id`, `author_id`, `status`, `views`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'Nghệ thuật đọc sách - Kỹ năng cần thiết cho cuộc sống', 'nghe-thuat-doc-sach-ky-nang-can-thiet', '<h2>Tại sao ch&uacute;ng ta cần đọc s&aacute;ch?</h2>\r\n<p>Trong thời đại c&ocirc;ng nghệ số ph&aacute;t triển như hiện nay, việc đọc s&aacute;ch c&oacute; vẻ như đang dần trở th&agrave;nh một th&oacute;i quen xa xỉ. Tuy nhi&ecirc;n, đọc s&aacute;ch kh&ocirc;ng chỉ l&agrave; một sở th&iacute;ch giải tr&iacute; m&agrave; c&ograve;n l&agrave; một kỹ năng sống cần thiết, gi&uacute;p ch&uacute;ng ta ph&aacute;t triển tư duy, mở rộng kiến thức v&agrave; ho&agrave;n thiện bản th&acirc;n.</p>\r\n<h3>1. Đọc s&aacute;ch gi&uacute;p mở rộng kiến thức</h3>\r\n<p>Mỗi cuốn s&aacute;ch l&agrave; một kho t&agrave;i nguy&ecirc;n tri thức v&ocirc; gi&aacute;. Khi đọc s&aacute;ch, bạn kh&ocirc;ng chỉ tiếp thu th&ocirc;ng tin mới m&agrave; c&ograve;n học được những kinh nghiệm, g&oacute;c nh&igrave;n v&agrave; c&aacute;ch suy nghĩ của t&aacute;c giả. Điều n&agrave;y gi&uacute;p bạn c&oacute; c&aacute;i nh&igrave;n đa chiều hơn về cuộc sống v&agrave; thế giới xung quanh.</p>\r\n<h3>2. Ph&aacute;t triển tư duy phản biện</h3>\r\n<p>Đọc s&aacute;ch, đặc biệt l&agrave; những cuốn s&aacute;ch triết học, khoa học hoặc văn học kinh điển, gi&uacute;p r&egrave;n luyện khả năng tư duy phản biện. Bạn học c&aacute;ch ph&acirc;n t&iacute;ch, đ&aacute;nh gi&aacute; v&agrave; đưa ra quan điểm ri&ecirc;ng về c&aacute;c vấn đề kh&aacute;c nhau.</p>\r\n<h3>3. Cải thiện kỹ năng giao tiếp</h3>\r\n<p>Người đọc nhiều s&aacute;ch thường c&oacute; vốn từ vựng phong ph&uacute; hơn, diễn đạt &yacute; tưởng r&otilde; r&agrave;ng v&agrave; mạch lạc hơn. Điều n&agrave;y gi&uacute;p n&acirc;ng cao khả năng giao tiếp, thuyết tr&igrave;nh v&agrave; viết l&aacute;ch trong c&ocirc;ng việc cũng như cuộc sống.</p>\r\n<h3>4. Giảm stress v&agrave; thư gi&atilde;n</h3>\r\n<p>Nghi&ecirc;n cứu cho thấy đọc s&aacute;ch chỉ trong 6 ph&uacute;t c&oacute; thể giảm stress l&ecirc;n đến 68%. Khi đắm ch&igrave;m v&agrave;o thế giới của một cuốn s&aacute;ch hay, bạn tạm qu&ecirc;n đi những lo toan trong cuộc sống v&agrave; t&igrave;m được sự b&igrave;nh y&ecirc;n trong t&acirc;m hồn.</p>\r\n<h3>5. Phương ph&aacute;p đọc s&aacute;ch hiệu quả</h3>\r\n<p><strong>Đọc chủ động:</strong> Kh&ocirc;ng đọc thụ động m&agrave; h&atilde;y đặt c&acirc;u hỏi, suy ngẫm về nội dung đang đọc.</p>\r\n<p><strong>Ghi ch&uacute;:</strong> Đ&aacute;nh dấu những đoạn văn quan trọng, ghi lại suy nghĩ của bạn b&ecirc;n lề s&aacute;ch.</p>\r\n<p><strong>Thảo luận:</strong> Chia sẻ những g&igrave; bạn đọc với người kh&aacute;c để c&oacute; th&ecirc;m nhiều g&oacute;c nh&igrave;n mới.</p>\r\n<p><strong>&Aacute;p dụng:</strong> Cố gắng &aacute;p dụng những kiến thức học được v&agrave;o cuộc sống thực tế.</p>\r\n<h3>Kết luận</h3>\r\n<p>Đọc s&aacute;ch l&agrave; một h&agrave;nh tr&igrave;nh kh&aacute;m ph&aacute; bản th&acirc;n v&agrave; thế giới. H&atilde;y bắt đầu với những cuốn s&aacute;ch bạn y&ecirc;u th&iacute;ch, dần dần x&acirc;y dựng th&oacute;i quen đọc đều đặn. Mỗi trang s&aacute;ch bạn lật l&agrave; một bước tiến trong h&agrave;nh tr&igrave;nh ph&aacute;t triển bản th&acirc;n. Như c&acirc;u n&oacute;i của Jorge Luis Borges: \"T&ocirc;i lu&ocirc;n tưởng tượng thi&ecirc;n đường sẽ l&agrave; một thư viện.\"</p>', 'Khám phá nghệ thuật đọc sách và những lợi ích tuyệt vời mà việc đọc sách mang lại cho cuộc sống. Hãy cùng tìm hiểu cách đọc sách hiệu quả và phát triển bản thân qua từng trang sách.', 'blog_1765028087.jpg', 1, NULL, 'published', 12, '2025-12-02 11:15:37', '2025-12-02 11:15:37', '2025-12-16 05:14:56'),
(2, 'Văn học Việt Nam đương đại - Những tác phẩm đáng đọc', 'van-hoc-viet-nam-duong-dai', '<h2>Bức tranh văn học Việt Nam hiện đại</h2>\r\n<p>Văn học Việt Nam đương đại đang chứng kiến một sự ph&aacute;t triển mạnh mẽ với nhiều t&aacute;c phẩm chất lượng, đa dạng thể loại v&agrave; phong ph&uacute; về nội dung. Từ tiểu thuyết, truyện ngắn đến thơ ca, c&aacute;c nh&agrave; văn Việt Nam đang kh&ocirc;ng ngừng s&aacute;ng tạo v&agrave; mang đến cho độc giả những trải nghiệm văn học độc đ&aacute;o.</p>\r\n<h3>1. Những xu hướng nổi bật</h3>\r\n<p><strong>Văn học th&agrave;nh thị:</strong> Phản &aacute;nh cuộc sống hiện đại với nhịp sống hối hả, những mối quan hệ phức tạp v&agrave; &aacute;p lực x&atilde; hội. C&aacute;c t&aacute;c giả như Nguyễn Nhật &Aacute;nh, Nguyễn Ngọc Tư đ&atilde; c&oacute; những đ&oacute;ng g&oacute;p quan trọng cho d&ograve;ng văn học n&agrave;y.</p>\r\n<p><strong>Văn học n&ocirc;ng th&ocirc;n mới:</strong> Kh&ocirc;ng c&ograve;n l&agrave; h&igrave;nh ảnh n&ocirc;ng th&ocirc;n ngh&egrave;o kh&oacute; truyền thống, văn học hiện đại vẽ n&ecirc;n bức tranh n&ocirc;ng th&ocirc;n với những biến đổi kinh tế - x&atilde; hội, những m&acirc;u thuẫn giữa truyền thống v&agrave; hiện đại.</p>\r\n<p><strong>Văn học tự truyện:</strong> Ng&agrave;y c&agrave;ng nhiều t&aacute;c giả chọn c&aacute;ch kể lại c&acirc;u chuyện cuộc đời m&igrave;nh, chia sẻ những trải nghiệm c&aacute; nh&acirc;n một c&aacute;ch ch&acirc;n thực v&agrave; s&acirc;u sắc.</p>\r\n<h3>2. Một số t&aacute;c phẩm ti&ecirc;u biểu</h3>\r\n<p><strong>\"C&aacute;nh đồng bất tận\" - Nguyễn Ngọc Tư:</strong> Một kiệt t&aacute;c văn xu&ocirc;i về miền T&acirc;y s&ocirc;ng nước, về con người v&agrave; cuộc sống đầy chất thơ nhưng cũng kh&ocirc;ng k&eacute;m phần khắc nghiệt.</p>\r\n<p><strong>\"T&ocirc;i thấy hoa v&agrave;ng tr&ecirc;n cỏ xanh\" - Nguyễn Nhật &Aacute;nh:</strong> T&aacute;c phẩm đưa người đọc trở về tuổi thơ miền qu&ecirc; Việt Nam, với những kỷ niệm đẹp đẽ v&agrave; cả những nỗi buồn đau của cuộc đời.</p>\r\n<p><strong>\"Nơi ấy c&oacute; con chim xanh\" - Nguyễn Phi V&acirc;n:</strong> Một c&acirc;u chuyện cảm động về t&igrave;nh y&ecirc;u, gia đ&igrave;nh v&agrave; những gi&aacute; trị nh&acirc;n văn s&acirc;u sắc.</p>\r\n<h3>3. &Yacute; nghĩa của văn học đương đại</h3>\r\n<p>Văn học kh&ocirc;ng chỉ l&agrave; nghệ thuật ng&ocirc;n từ m&agrave; c&ograve;n l&agrave; gương phản chiếu x&atilde; hội, l&agrave; tiếng n&oacute;i của thời đại. Qua văn học, ch&uacute;ng ta hiểu hơn về con người, về cuộc sống v&agrave; t&igrave;m thấy những gi&aacute; trị nh&acirc;n văn cao đẹp.</p>\r\n<h3>4. Lời khuy&ecirc;n cho người đọc</h3>\r\n<p>H&atilde;y đọc văn học với t&acirc;m thế cởi mở, sẵn s&agrave;ng đ&oacute;n nhận những cảm x&uacute;c v&agrave; suy tư m&agrave; t&aacute;c giả muốn truyền tải. Mỗi t&aacute;c phẩm l&agrave; một thế giới ri&ecirc;ng, h&atilde;y để m&igrave;nh h&ograve;a m&igrave;nh v&agrave;o đ&oacute; v&agrave; t&igrave;m thấy những điều &yacute; nghĩa cho ri&ecirc;ng bạn.</p>\r\n<h3>Kết luận</h3>\r\n<p>Văn học Việt Nam đương đại đang ng&agrave;y c&agrave;ng khẳng định vị thế của m&igrave;nh kh&ocirc;ng chỉ trong nước m&agrave; c&ograve;n tr&ecirc;n trường quốc tế. H&atilde;y ủng hộ v&agrave; lan tỏa những t&aacute;c phẩm hay để văn học Việt Nam ng&agrave;y c&agrave;ng ph&aacute;t triển.</p>', 'Tìm hiểu về văn học Việt Nam đương đại với những tác phẩm tiêu biểu, xu hướng mới và ý nghĩa sâu sắc mà văn học mang lại cho người đọc và xã hội.', 'blog_1765028231.jpg', 1, NULL, 'published', 2, '2025-12-02 11:15:37', '2025-12-02 11:15:37', '2025-12-16 05:15:25'),
(3, 'Tầm quan trọng của sách thiếu nhi trong sự phát triển của trẻ', 'tam-quan-trong-cua-sach-thieu-nhi', '<h2>Vai tr&ograve; của s&aacute;ch thiếu nhi</h2>\r\n<p>S&aacute;ch thiếu nhi kh&ocirc;ng chỉ l&agrave; nguồn giải tr&iacute; m&agrave; c&ograve;n l&agrave; c&ocirc;ng cụ gi&aacute;o dục quan trọng, gi&uacute;p trẻ ph&aacute;t triển to&agrave;n diện về mặt tr&iacute; tuệ, t&igrave;nh cảm v&agrave; nh&acirc;n c&aacute;ch. Những trang s&aacute;ch đầu ti&ecirc;n trong đời c&oacute; thể tạo nền m&oacute;ng cho th&oacute;i quen đọc s&aacute;ch suốt đời v&agrave; định h&igrave;nh tương lai của trẻ.</p>\r\n<h3>1. Ph&aacute;t triển ng&ocirc;n ngữ v&agrave; tư duy</h3>\r\n<p>Đọc s&aacute;ch gi&uacute;p trẻ l&agrave;m quen với ng&ocirc;n ngữ từ rất sớm. Qua c&aacute;c c&acirc;u chuyện, trẻ học được vốn từ vựng mới, cấu tr&uacute;c c&acirc;u đa dạng v&agrave; c&aacute;ch diễn đạt &yacute; tưởng. Điều n&agrave;y kh&ocirc;ng chỉ gi&uacute;p trẻ giao tiếp tốt hơn m&agrave; c&ograve;n ph&aacute;t triển khả năng tư duy logic v&agrave; s&aacute;ng tạo.</p>\r\n<h3>2. Nu&ocirc;i dưỡng tr&iacute; tưởng tượng</h3>\r\n<p>C&aacute;c c&acirc;u chuyện cổ t&iacute;ch, truyện thần ti&ecirc;n hay khoa học viễn tưởng mở ra cho trẻ một thế giới kỳ diệu kh&ocirc;ng giới hạn. Tr&iacute; tưởng tượng phong ph&uacute; gi&uacute;p trẻ s&aacute;ng tạo, giải quyết vấn đề linh hoạt v&agrave; nh&igrave;n nhận cuộc sống với nhiều g&oacute;c độ kh&aacute;c nhau.</p>\r\n<h3>3. H&igrave;nh th&agrave;nh gi&aacute; trị đạo đức</h3>\r\n<p>Qua c&aacute;c nh&acirc;n vật v&agrave; t&igrave;nh huống trong s&aacute;ch, trẻ học được những b&agrave;i học về l&ograve;ng tốt, sự trung thực, l&ograve;ng dũng cảm, t&igrave;nh bạn v&agrave; y&ecirc;u thương. Những gi&aacute; trị n&agrave;y được truyền tải một c&aacute;ch tự nhi&ecirc;n, dễ tiếp nhận qua c&aacute;c c&acirc;u chuyện hấp dẫn.</p>\r\n<h3>4. Ph&aacute;t triển cảm x&uacute;c v&agrave; đồng cảm</h3>\r\n<p>Khi đọc s&aacute;ch, trẻ trải nghiệm những cảm x&uacute;c của nh&acirc;n vật - vui, buồn, sợ h&atilde;i, hồi hộp. Điều n&agrave;y gi&uacute;p trẻ hiểu v&agrave; quản l&yacute; cảm x&uacute;c của bản th&acirc;n tốt hơn, đồng thời ph&aacute;t triển khả năng đồng cảm với người kh&aacute;c.</p>\r\n<h3>5. Tăng cường mối quan hệ cha mẹ - con c&aacute;i</h3>\r\n<p>Đọc s&aacute;ch cho con trước giờ ngủ l&agrave; một hoạt động tuyệt vời để tăng cường sự gắn kết trong gia đ&igrave;nh. Đ&acirc;y l&agrave; khoảng thời gian qu&yacute; gi&aacute; để cha mẹ v&agrave; con c&ugrave;ng chia sẻ, tr&ograve; chuyện v&agrave; hiểu nhau hơn.</p>\r\n<h3>6. Gợi &yacute; chọn s&aacute;ch theo độ tuổi</h3>\r\n<p><strong>0-3 tuổi:</strong> S&aacute;ch tranh đơn giản, m&agrave;u sắc rực rỡ, nội dung về cuộc sống h&agrave;ng ng&agrave;y.</p>\r\n<p><strong>3-6 tuổi:</strong> Truyện cổ t&iacute;ch, truyện kể về động vật, s&aacute;ch c&oacute; vần điệu.</p>\r\n<p><strong>6-9 tuổi:</strong> Truyện phi&ecirc;u lưu, truyện trinh th&aacute;m đơn giản, s&aacute;ch khoa học phổ th&ocirc;ng.</p>\r\n<p><strong>9-12 tuổi:</strong> Tiểu thuyết thiếu nhi, s&aacute;ch lịch sử, s&aacute;ch về c&aacute;c nh&acirc;n vật truyền cảm hứng.</p>\r\n<h3>7. L&agrave;m thế n&agrave;o để khuyến kh&iacute;ch trẻ đọc s&aacute;ch?</h3>\r\n<ul>\r\n<li>Tạo kh&ocirc;ng gian đọc s&aacute;ch thoải m&aacute;i v&agrave; hấp dẫn</li>\r\n<li>Đọc s&aacute;ch c&ugrave;ng con v&agrave; thảo luận về nội dung</li>\r\n<li>Cho trẻ tự do lựa chọn s&aacute;ch y&ecirc;u th&iacute;ch</li>\r\n<li>L&agrave;m gương bằng c&aacute;ch đọc s&aacute;ch thường xuy&ecirc;n</li>\r\n<li>Tham gia c&aacute;c c&acirc;u lạc bộ đọc s&aacute;ch cho trẻ</li>\r\n<li>Kết nối nội dung s&aacute;ch với cuộc sống thực tế</li>\r\n</ul>\r\n<h3>Kết luận</h3>\r\n<p>Đầu tư v&agrave;o s&aacute;ch thiếu nhi l&agrave; đầu tư cho tương lai của con trẻ. H&atilde;y gi&uacute;p trẻ y&ecirc;u th&iacute;ch đọc s&aacute;ch từ khi c&ograve;n nhỏ, để ch&uacute;ng c&oacute; thể tự tin bước v&agrave;o cuộc sống với h&agrave;nh trang kiến thức v&agrave; gi&aacute; trị vững chắc. Như Helen Keller đ&atilde; n&oacute;i: \"S&aacute;ch l&agrave; kho b&aacute;u m&agrave; t&ocirc;i c&oacute; thể mở ra bằng ch&igrave;a kh&oacute;a của tr&iacute; tuệ.\"</p>', 'Khám phá vai trò quan trọng của sách thiếu nhi trong việc phát triển trí tuệ, cảm xúc và nhân cách của trẻ. Cùng tìm hiểu cách chọn sách và khuyến khích trẻ yêu thích đọc.', 'blog_1765028256.jpg', 1, NULL, 'published', 0, '2025-12-02 11:15:37', '2025-12-02 11:15:37', '2025-12-06 13:37:39'),
(4, 'Sách Self-Help - Người bạn đồng hành trên hành trình phát triển bản thân', 'sach-self-help-phat-trien-ban-than', '<h2>Hiện tượng s&aacute;ch Self-Help</h2>\r\n<p>Trong những năm gần đ&acirc;y, s&aacute;ch self-help (s&aacute;ch ph&aacute;t triển bản th&acirc;n) đ&atilde; trở th&agrave;nh một trong những thể loại được ưa chuộng nhất tr&ecirc;n thị trường xuất bản. Từ \"Đắc nh&acirc;n t&acirc;m\" của Dale Carnegie đến \"Atomic Habits\" của James Clear, c&aacute;c đầu s&aacute;ch n&agrave;y kh&ocirc;ng chỉ b&aacute;n chạy m&agrave; c&ograve;n tạo ra những thay đổi t&iacute;ch cực trong cuộc sống của h&agrave;ng triệu độc giả tr&ecirc;n to&agrave;n thế giới.</p>\r\n<h3>1. Self-Help l&agrave; g&igrave;?</h3>\r\n<p>S&aacute;ch self-help l&agrave; những cuốn s&aacute;ch hướng dẫn, tư vấn gi&uacute;p người đọc cải thiện chất lượng cuộc sống, ph&aacute;t triển kỹ năng c&aacute; nh&acirc;n, v&agrave; đạt được mục ti&ecirc;u. Nội dung c&oacute; thể xoay quanh nhiều chủ đề như quản l&yacute; thời gian, giao tiếp, t&agrave;i ch&iacute;nh, sức khỏe tinh thần, hoặc x&acirc;y dựng th&oacute;i quen t&iacute;ch cực.</p>\r\n<h3>2. Tại sao s&aacute;ch Self-Help lại phổ biến?</h3>\r\n<p><strong>Đ&aacute;p ứng nhu cầu thực tế:</strong> Trong cuộc sống hiện đại đầy &aacute;p lực, mọi người t&igrave;m kiếm giải ph&aacute;p cụ thể cho c&aacute;c vấn đề họ đang gặp phải.</p>\r\n<p><strong>Dễ tiếp cận:</strong> S&aacute;ch self-help thường được viết với ng&ocirc;n ngữ đơn giản, dễ hiểu, k&egrave;m theo c&aacute;c v&iacute; dụ thực tế v&agrave; b&agrave;i tập &aacute;p dụng.</p>\r\n<p><strong>Tạo động lực:</strong> Những c&acirc;u chuyện truyền cảm hứng v&agrave; lời khuy&ecirc;n thiết thực gi&uacute;p người đọc c&oacute; th&ecirc;m động lực để thay đổi v&agrave; ph&aacute;t triển.</p>\r\n<h3>3. Những cuốn s&aacute;ch Self-Help kinh điển</h3>\r\n<p><strong>\"Đắc nh&acirc;n t&acirc;m\" - Dale Carnegie:</strong> Cuốn s&aacute;ch bất hủ về nghệ thuật giao tiếp v&agrave; ứng xử, gi&uacute;p x&acirc;y dựng mối quan hệ tốt đẹp với mọi người.</p>\r\n<p><strong>\"Nghĩ gi&agrave;u l&agrave;m gi&agrave;u\" - Napoleon Hill:</strong> Chia sẻ 13 nguy&ecirc;n tắc th&agrave;nh c&ocirc;ng từ những người gi&agrave;u c&oacute; nhất thế giới.</p>\r\n<p><strong>\"Atomic Habits\" - James Clear:</strong> Hướng dẫn c&aacute;ch x&acirc;y dựng th&oacute;i quen tốt v&agrave; loại bỏ th&oacute;i quen xấu một c&aacute;ch khoa học.</p>\r\n<p><strong>\"7 th&oacute;i quen của người th&agrave;nh đạt\" - Stephen Covey:</strong> Một hệ thống nguy&ecirc;n tắc sống gi&uacute;p con người đạt được sự c&acirc;n bằng v&agrave; th&agrave;nh c&ocirc;ng bền vững.</p>\r\n<h3>4. C&aacute;ch đọc s&aacute;ch Self-Help hiệu quả</h3>\r\n<p><strong>Đọc với mục đ&iacute;ch r&otilde; r&agrave;ng:</strong> X&aacute;c định bạn muốn cải thiện điều g&igrave; trước khi bắt đầu đọc.</p>\r\n<p><strong>Ghi ch&uacute; v&agrave; t&oacute;m tắt:</strong> Viết ra những điểm quan trọng, những &yacute; tưởng bạn muốn &aacute;p dụng.</p>\r\n<p><strong>&Aacute;p dụng ngay:</strong> Đừng chỉ đọc m&agrave; phải thực h&agrave;nh. H&atilde;y bắt đầu với một thay đổi nhỏ v&agrave; ki&ecirc;n tr&igrave;.</p>\r\n<p><strong>Chia sẻ v&agrave; thảo luận:</strong> N&oacute;i về những g&igrave; bạn học được với người kh&aacute;c gi&uacute;p củng cố kiến thức v&agrave; c&oacute; th&ecirc;m động lực.</p>\r\n<p><strong>Đọc lại:</strong> Nhiều cuốn s&aacute;ch self-help đ&aacute;ng để đọc nhiều lần, mỗi lần bạn sẽ c&oacute; những hiểu biết mới.</p>\r\n<h3>5. Những lưu &yacute; khi đọc s&aacute;ch Self-Help</h3>\r\n<p><strong>Kh&ocirc;ng phải mọi lời khuy&ecirc;n đều ph&ugrave; hợp:</strong> Mỗi người c&oacute; ho&agrave;n cảnh kh&aacute;c nhau, h&atilde;y chọn lọc những g&igrave; ph&ugrave; hợp với bạn.</p>\r\n<p><strong>Tr&aacute;nh \"nghiện\" đọc:</strong> Đừng chỉ đọc m&agrave; kh&ocirc;ng h&agrave;nh động. Một cuốn s&aacute;ch được &aacute;p dụng tốt c&oacute; gi&aacute; trị hơn mười cuốn chỉ đọc qua loa.</p>\r\n<p><strong>Kỳ vọng thực tế:</strong> Thay đổi cần thời gian v&agrave; nỗ lực. Đừng mong đợi kết quả ngay lập tức.</p>\r\n<h3>6. Self-Help trong thời đại số</h3>\r\n<p>Ngo&agrave;i s&aacute;ch giấy truyền thống, nội dung self-help giờ đ&acirc;y c&ograve;n c&oacute; ở nhiều định dạng kh&aacute;c như s&aacute;ch n&oacute;i, podcast, kh&oacute;a học online, v&agrave; video. Điều n&agrave;y gi&uacute;p người học c&oacute; thể tiếp cận kiến thức mọi l&uacute;c, mọi nơi.</p>\r\n<h3>Kết luận</h3>\r\n<p>S&aacute;ch self-help l&agrave; c&ocirc;ng cụ hữu &iacute;ch tr&ecirc;n h&agrave;nh tr&igrave;nh ph&aacute;t triển bản th&acirc;n, nhưng điều quan trọng nhất vẫn l&agrave; sự cam kết thay đổi của ch&iacute;nh bạn. H&atilde;y đọc, học hỏi v&agrave; h&agrave;nh động để biến những l&yacute; thuyết trong s&aacute;ch th&agrave;nh thực tế trong cuộc sống của bạn. Như Tony Robbins đ&atilde; n&oacute;i: \"Kiến thức kh&ocirc;ng phải l&agrave; sức mạnh. H&agrave;nh động mới l&agrave; sức mạnh.\"</p>', 'Tìm hiểu về sách self-help - thể loại sách phát triển bản thân đang rất được ưa chuộng. Khám phá các tác phẩm kinh điển và cách đọc sách self-help một cách hiệu quả.', 'blog_1765028281.jpg', 1, NULL, 'published', 1, '2025-12-02 11:15:37', '2025-12-02 11:15:37', '2025-12-06 13:38:05'),
(5, 'Xây dựng văn hóa đọc sách trong cộng đồng', 'xay-dung-van-hoa-doc-sach-cong-dong', '<h2>Tầm quan trọng của văn h&oacute;a đọc</h2>\r\n<p>Văn h&oacute;a đọc l&agrave; một trong những yếu tố quan trọng phản &aacute;nh tr&igrave;nh độ văn minh của một quốc gia. Một x&atilde; hội c&oacute; văn h&oacute;a đọc ph&aacute;t triển sẽ c&oacute; nguồn nh&acirc;n lực chất lượng cao, c&oacute; khả năng s&aacute;ng tạo v&agrave; th&iacute;ch ứng tốt với những thay đổi của thời đại. Tuy nhi&ecirc;n, trong bối cảnh c&ocirc;ng nghệ ph&aacute;t triển v&agrave; cuộc sống bận rộn, việc duy tr&igrave; v&agrave; ph&aacute;t triển văn h&oacute;a đọc đang trở th&agrave;nh một th&aacute;ch thức lớn.</p>\r\n<h3>1. Thực trạng văn h&oacute;a đọc ở Việt Nam</h3>\r\n<p>Theo c&aacute;c khảo s&aacute;t gần đ&acirc;y, người Việt Nam đọc trung b&igrave;nh khoảng 1-2 cuốn s&aacute;ch mỗi năm, con số n&agrave;y thấp hơn nhiều so với c&aacute;c nước ph&aacute;t triển như Nhật Bản (40 cuốn/năm) hay H&agrave;n Quốc (30 cuốn/năm). Điều n&agrave;y cho thấy ch&uacute;ng ta cần c&oacute; những nỗ lực lớn hơn để x&acirc;y dựng văn h&oacute;a đọc.</p>\r\n<h3>2. Nguy&ecirc;n nh&acirc;n của t&igrave;nh trạng đọc s&aacute;ch &iacute;t</h3>\r\n<p><strong>Thiếu thời gian:</strong> Cuộc sống hiện đại bận rộn khiến nhiều người cho rằng họ kh&ocirc;ng c&oacute; thời gian để đọc s&aacute;ch.</p>\r\n<p><strong>Cạnh tranh với giải tr&iacute; số:</strong> C&aacute;c phương tiện giải tr&iacute; như mạng x&atilde; hội, game, phim ảnh đang thu h&uacute;t phần lớn thời gian rảnh của mọi người.</p>\r\n<p><strong>Gi&aacute; s&aacute;ch cao:</strong> Chi ph&iacute; mua s&aacute;ch đ&ocirc;i khi trở th&agrave;nh r&agrave;o cản, đặc biệt với c&aacute;c gia đ&igrave;nh c&oacute; thu nhập thấp.</p>\r\n<p><strong>Thiếu m&ocirc;i trường khuyến kh&iacute;ch:</strong> &Iacute;t c&oacute; c&aacute;c kh&ocirc;ng gian c&ocirc;ng cộng d&agrave;nh cho việc đọc s&aacute;ch v&agrave; thảo luận về s&aacute;ch.</p>\r\n<h3>3. Lợi &iacute;ch của việc x&acirc;y dựng văn h&oacute;a đọc</h3>\r\n<p><strong>Ph&aacute;t triển tr&iacute; tuệ cộng đồng:</strong> Một cộng đồng y&ecirc;u s&aacute;ch l&agrave; một cộng đồng c&oacute; tri thức, c&oacute; khả năng tư duy phản biện v&agrave; giải quyết vấn đề tốt.</p>\r\n<p><strong>Tạo m&ocirc;i trường học tập:</strong> Khi đọc s&aacute;ch trở th&agrave;nh một hoạt động phổ biến, n&oacute; tạo ra văn h&oacute;a học tập suốt đời trong cộng đồng.</p>\r\n<p><strong>Gắn kết cộng đồng:</strong> C&aacute;c c&acirc;u lạc bộ đọc s&aacute;ch, hội thảo, triển l&atilde;m s&aacute;ch l&agrave; những dịp để mọi người gặp gỡ, chia sẻ v&agrave; học hỏi lẫn nhau.</p>\r\n<p><strong>Bảo tồn văn h&oacute;a:</strong> S&aacute;ch l&agrave; nơi lưu giữ v&agrave; truyền b&aacute; c&aacute;c gi&aacute; trị văn h&oacute;a, lịch sử của d&acirc;n tộc.</p>\r\n<h3>4. C&aacute;c s&aacute;ng kiến x&acirc;y dựng văn h&oacute;a đọc</h3>\r\n<p><strong>Thư viện cộng đồng:</strong> X&acirc;y dựng c&aacute;c thư viện miễn ph&iacute; tại c&aacute;c khu d&acirc;n cư, trường học, nơi l&agrave;m việc.</p>\r\n<p><strong>C&acirc;u lạc bộ đọc s&aacute;ch:</strong> Tổ chức c&aacute;c nh&oacute;m đọc s&aacute;ch thường xuy&ecirc;n để mọi người c&ugrave;ng đọc v&agrave; thảo luận.</p>\r\n<p><strong>Ng&agrave;y hội s&aacute;ch:</strong> Tổ chức c&aacute;c sự kiện lớn về s&aacute;ch với nhiều hoạt động hấp dẫn như gặp gỡ t&aacute;c giả, tọa đ&agrave;m, hội chợ s&aacute;ch.</p>\r\n<p><strong>Chương tr&igrave;nh đọc s&aacute;ch trong trường học:</strong> T&iacute;ch hợp hoạt động đọc s&aacute;ch v&agrave;o chương tr&igrave;nh gi&aacute;o dục, tổ chức c&aacute;c cuộc thi viết cảm nhận về s&aacute;ch.</p>\r\n<p><strong>Thư viện số:</strong> Ph&aacute;t triển c&aacute;c nền tảng đọc s&aacute;ch online, s&aacute;ch n&oacute;i để tiếp cận rộng r&atilde;i hơn.</p>\r\n<h3>5. Vai tr&ograve; của từng th&agrave;nh phần</h3>\r\n<p><strong>Gia đ&igrave;nh:</strong> Bố mẹ cần l&agrave;m gương, tạo kh&ocirc;ng gian đọc s&aacute;ch tại nh&agrave; v&agrave; khuyến kh&iacute;ch con em đọc s&aacute;ch từ nhỏ.</p>\r\n<p><strong>Nh&agrave; trường:</strong> Gi&aacute;o vi&ecirc;n cần truyền cảm hứng, giới thiệu s&aacute;ch hay v&agrave; tổ chức c&aacute;c hoạt động li&ecirc;n quan đến s&aacute;ch.</p>\r\n<p><strong>X&atilde; hội:</strong> Ch&iacute;nh quyền, doanh nghiệp cần đầu tư v&agrave;o hệ thống thư viện, giảm gi&aacute; s&aacute;ch, tổ chức c&aacute;c sự kiện văn h&oacute;a đọc.</p>\r\n<p><strong>C&aacute; nh&acirc;n:</strong> Mỗi người cần &yacute; thức về tầm quan trọng của đọc s&aacute;ch v&agrave; d&agrave;nh thời gian cho hoạt động n&agrave;y.</p>\r\n<h3>6. Những m&ocirc; h&igrave;nh th&agrave;nh c&ocirc;ng tr&ecirc;n thế giới</h3>\r\n<p><strong>Iceland:</strong> Đất nước c&oacute; truyền thống tặng s&aacute;ch v&agrave;o dịp Gi&aacute;ng sinh (J&oacute;lab&oacute;kafl&oacute;&eth;), khiến việc đọc s&aacute;ch trở th&agrave;nh một phần văn h&oacute;a.</p>\r\n<p><strong>H&agrave;n Quốc:</strong> C&oacute; hệ thống thư viện c&ocirc;ng cộng rộng khắp, mở cửa 24/7, thu h&uacute;t h&agrave;ng triệu lượt người đến mỗi năm.</p>\r\n<p><strong>Nhật Bản:</strong> T&agrave;u điện ngầm đầy người đọc s&aacute;ch, c&aacute;c hiệu s&aacute;ch lớn với kh&ocirc;ng gian thoải m&aacute;i thu h&uacute;t độc giả mọi lứa tuổi.</p>\r\n<h3>7. H&agrave;nh động cụ thể bạn c&oacute; thể l&agrave;m</h3>\r\n<ul>\r\n<li>Đọc &iacute;t nhất 15 ph&uacute;t mỗi ng&agrave;y</li>\r\n<li>Chia sẻ s&aacute;ch hay với bạn b&egrave;, đồng nghiệp</li>\r\n<li>Tham gia hoặc th&agrave;nh lập c&acirc;u lạc bộ đọc s&aacute;ch</li>\r\n<li>Quy&ecirc;n g&oacute;p s&aacute;ch cho thư viện, trường học</li>\r\n<li>Viết review s&aacute;ch để lan tỏa</li>\r\n<li>Mua s&aacute;ch l&agrave;m qu&agrave; tặng</li>\r\n<li>Tham gia c&aacute;c sự kiện về s&aacute;ch</li>\r\n</ul>\r\n<h3>Kết luận</h3>\r\n<p>X&acirc;y dựng văn h&oacute;a đọc l&agrave; một qu&aacute; tr&igrave;nh l&acirc;u d&agrave;i đ&ograve;i hỏi sự chung tay của cả cộng đồng. Tuy nhi&ecirc;n, mỗi c&aacute; nh&acirc;n đều c&oacute; thể đ&oacute;ng g&oacute;p một phần bằng c&aacute;ch bắt đầu từ ch&iacute;nh m&igrave;nh - đọc nhiều hơn v&agrave; truyền cảm hứng cho người xung quanh. Như Margaret Fuller đ&atilde; n&oacute;i: \"Ng&agrave;y h&ocirc;m nay, một người đọc s&aacute;ch, ng&agrave;y mai l&agrave; một nh&agrave; l&atilde;nh đạo.\" H&atilde;y c&ugrave;ng nhau biến đọc s&aacute;ch th&agrave;nh một n&eacute;t đẹp văn h&oacute;a của cộng đồng v&agrave; đất nước!</p>', 'Khám phá tầm quan trọng của việc xây dựng văn hóa đọc trong cộng đồng. Tìm hiểu các sáng kiến và hành động cụ thể để thúc đẩy thói quen đọc sách trong xã hội.', 'blog_1765028323.jpg', 1, NULL, 'published', 1, '2025-12-02 11:15:37', '2025-12-02 11:15:37', '2025-12-08 12:27:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(10, 4, 'SP050', 2, '2025-12-06 12:05:22'),
(11, 4, 'SP049', 2, '2025-12-06 12:05:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Văn học', 'Tượng trang trí'),
(2, 'Kinh tế', 'Tranh treo tường'),
(3, 'Tâm lý - Kỹ năng', 'Đèn trang trí'),
(4, 'khac', 'Sản phẩm khác'),
(5, 'Tiểu thuyết', 'Sách tiểu thuyết Việt Nam và nước ngoài'),
(6, 'Thiếu nhi', 'Sách dành cho trẻ em'),
(7, 'Manga - Comic', 'Truyện tranh Nhật Bản và Hàn Quốc'),
(8, 'Sách giáo khoa', 'Sách giáo khoa các cấp'),
(9, 'Sách ngoại văn', 'Sách tiếng Anh và các ngôn ngữ khác'),
(10, 'Self-help', 'Sách phát triển bản thân');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `replied_at` timestamp NULL DEFAULT NULL,
  `reply_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `sender_type` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `sender_type`, `message`, `created_at`, `is_read`) VALUES
(1, 3, NULL, 'user', 'hi', '2025-11-22 06:53:21', 1),
(2, 1, 3, 'admin', 'hi', '2025-11-22 06:53:38', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cod','bank_transfer') NOT NULL DEFAULT 'cod',
  `payment_proof` varchar(255) DEFAULT NULL,
  `order_status` varchar(20) DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_date` datetime DEFAULT current_timestamp(),
  `status` text DEFAULT 'Chờ xác nhận',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customer_confirmed` tinyint(1) DEFAULT 0,
  `return_request` tinyint(1) DEFAULT 0,
  `return_reason` text DEFAULT NULL,
  `return_request_date` datetime DEFAULT NULL,
  `return_status` varchar(50) DEFAULT NULL,
  `completed_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `full_name`, `email`, `phone`, `address`, `total_amount`, `payment_method`, `payment_proof`, `order_status`, `notes`, `created_at`, `order_date`, `status`, `updated_at`, `customer_confirmed`, `return_request`, `return_reason`, `return_request_date`, `return_status`, `completed_date`) VALUES
(8, 5, 'Nguyễn Văn A', 'nguyena@gmail.com', '0987876552', 'Cầu Ngang trà vinh', 446000.00, 'cod', NULL, 'Đã hủy', '', '2025-12-08 12:30:01', '2025-12-08 19:30:01', 'Chờ xác nhận', '2025-12-08 12:39:03', 0, 0, NULL, NULL, NULL, NULL),
(9, 5, 'Nguyễn Văn A', 'nguyena@gmail.com', '0987876552', 'Cầu Ngang trà vinh', 99000.00, 'bank_transfer', NULL, 'Hoàn thành', '', '2025-12-08 12:30:24', '2025-12-08 19:30:24', 'Chờ xác nhận', '2025-12-15 06:22:36', 1, 0, NULL, NULL, NULL, '2025-12-08 19:38:52'),
(10, 5, 'Nguyễn Văn A', 'nguyena@gmail.com', '0987876552', 'Cầu Ngang trà vinh', 618000.00, 'bank_transfer', NULL, 'Hoàn thành', '', '2025-12-08 12:49:16', '2025-12-08 19:49:16', 'Chờ xác nhận', '2025-12-15 06:22:36', 1, 0, NULL, NULL, NULL, '2025-12-08 20:01:26'),
(13, 5, 'Nguyễn Văn A', 'nguyena@gmail.com', '0987876552', 'Cầu Ngang trà vinh', 772000.00, 'bank_transfer', NULL, 'Đã hủy', '', '2025-12-08 12:54:23', '2025-12-08 19:54:23', 'Chờ xác nhận', '2025-12-08 13:01:31', 0, 0, NULL, NULL, NULL, NULL),
(14, 5, 'Nguyễn Văn A', 'nguyena@gmail.com', '0987876552', 'Cầu Ngang trà vinh', 264000.00, 'bank_transfer', NULL, 'Hoàn thành', '', '2025-12-08 12:54:54', '2025-12-08 19:54:54', 'Chờ xác nhận', '2025-12-15 06:22:36', 1, 0, NULL, NULL, NULL, '2025-12-08 20:01:16'),
(15, 5, '', '', '', '', 189000.00, 'cod', NULL, 'Đã hủy', NULL, '2025-12-08 12:56:42', '2025-12-08 19:56:42', 'Chờ xác nhận', '2025-12-08 13:01:21', 0, 0, NULL, NULL, NULL, NULL),
(16, 5, 'Nguyễn Văn A', 'nguyena@gmail.com', '0987876552', 'Cầu Ngang trà vinh', 240000.00, 'bank_transfer', NULL, 'Đã trả hàng', '', '2025-12-08 12:57:17', '2025-12-08 19:57:17', 'Chờ xác nhận', '2025-12-14 15:08:52', 0, 1, 'ko hay', '2025-12-08 20:46:21', 'Đã duyệt', '2025-12-08 20:01:12'),
(17, 5, 'Nguyễn Văn A', 'nguyena@gmail.com', '0987876552', 'Cầu Ngang trà vinh', 270000.00, 'bank_transfer', NULL, 'Hoàn thành', '', '2025-12-08 12:58:33', '2025-12-08 19:58:33', 'Chờ xác nhận', '2025-12-08 13:15:38', 1, 0, NULL, NULL, NULL, '2025-12-08 20:01:07'),
(18, 5, '', '', '', '', 119000.00, 'cod', NULL, 'Đã giao', NULL, '2025-12-08 13:13:33', '2025-12-08 20:13:33', 'Chờ xác nhận', '2025-12-16 04:23:50', 0, 0, NULL, NULL, NULL, NULL),
(19, 3, 'Trần Thanh Thưởng', 'dubu2k4@gmail.com', '0392656499', 'Cầu Ngang trà vinh', 189000.00, 'cod', NULL, 'Hoàn thành', '', '2025-12-15 06:24:01', '2025-12-15 13:24:01', 'Chờ xác nhận', '2025-12-16 04:54:51', 1, 0, NULL, NULL, NULL, '2025-12-16 11:54:51'),
(420, 5, 'Nguyễn Văn A', 'nguyena@gmail.com', '0987876552', 'Cầu Ngang trà vinh', 119000.00, 'cod', NULL, 'Đã giao', '', '2025-12-16 04:55:48', '2025-12-16 11:55:48', 'Chờ xác nhận', '2025-12-16 04:56:12', 0, 0, NULL, NULL, NULL, NULL);

--
-- Bẫy `orders`
--
DELIMITER $$
CREATE TRIGGER `update_order_status_time` BEFORE UPDATE ON `orders` FOR EACH ROW BEGIN
    IF NEW.status != OLD.status THEN
        SET NEW.updated_at = NOW();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` varchar(10) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(10, 8, 'SP041', 1, 119000.00),
(11, 8, 'SP038', 1, 129000.00),
(12, 8, 'SP032', 1, 198000.00),
(13, 9, 'SP047', 1, 32000.00),
(14, 9, 'SP048', 1, 33000.00),
(15, 9, 'SP049', 1, 34000.00),
(16, 10, 'SP041', 1, 119000.00),
(17, 10, 'SP036', 1, 119000.00),
(18, 10, 'SP034', 1, 380000.00),
(19, 13, 'SP049', 1, 34000.00),
(20, 13, 'SP039', 1, 189000.00),
(21, 13, 'SP040', 1, 99000.00),
(22, 13, 'SP031', 1, 450000.00),
(23, 14, 'SP037', 1, 145000.00),
(24, 14, 'SP036', 1, 119000.00),
(25, 15, 'SP039', 1, 189000.00),
(26, 16, 'SP043', 1, 115000.00),
(27, 16, 'SP044', 1, 125000.00),
(28, 17, 'SP044', 1, 125000.00),
(29, 17, 'SP045', 1, 145000.00),
(30, 18, 'SP036', 1, 119000.00),
(31, 19, 'SP039', 1, 189000.00),
(32, 420, 'SP036', 1, 119000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `product_id` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Tác giả',
  `publisher` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nhà xuất bản',
  `publish_year` int(11) DEFAULT NULL COMMENT 'Năm xuất bản',
  `isbn` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Mã ISBN',
  `pages` int(11) DEFAULT NULL COMMENT 'Số trang',
  `language` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Tiếng Việt' COMMENT 'Ngôn ngữ',
  `book_format` enum('Bìa mềm','Bìa cứng','Ebook') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Bìa mềm' COMMENT 'Hình thức sách',
  `dimensions` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Kích thước (cm)',
  `weight` int(11) DEFAULT NULL COMMENT 'Trọng lượng (gram)',
  `series` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Bộ sách/Series',
  `price` decimal(10,2) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `sold_quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `author`, `publisher`, `publish_year`, `isbn`, `pages`, `language`, `book_format`, `dimensions`, `weight`, `series`, `price`, `description`, `image_url`, `stock_quantity`, `sold_quantity`) VALUES
('SP001', 1, 'Đắc Nhân Tâm', 'Dale Carnegie', 'NXB Tổng Hợp TPHCM', 2020, '9786041096080', 320, 'Tiếng Việt', 'Bìa mềm', '14.5 x 20.5 cm', 350, NULL, 86000.00, 'Cuốn sách kinh điển về nghệ thuật đối nhân xử thế và giao tiếp. Dale Carnegie đã tổng hợp những nguyên tắc cơ bản giúp bạn trở nên thân thiện hơn, thuyết phục người khác theo cách của bạn và chiến thắng trong giao tiếp. Đây là cuốn sách bán chạy nhất mọi thời đại, đã giúp hàng triệu người thay đổi cuộc sống.', 'uploads/dac_nhan_tam.jpg', 149, 231),
('SP002', 1, 'Nhà Giả Kim', 'Paulo Coelho', 'NXB Hội Nhà Văn', 2021, '9786041046733', 227, 'Tiếng Việt', 'Bìa mềm', '13 x 20 cm', 280, NULL, 79000.00, 'Câu chuyện về chuyến hành trình đi tìm kho báu của Santiago - cậu bé chăn cừu Tây Ban Nha. Qua hành trình ấy, chúng ta được sống cùng Santiago những trải nghiệm quý giá, học cách lắng nghe trái tim mình, hiểu được ý nghĩa đích thực của hạnh phúc và theo đuổi ước mơ của chính mình.', 'uploads/nha_gia_kim.jpg', 199, 451),
('SP003', 1, 'Cây Cam Ngọt Của Tôi', 'José Mauro de Vasconcelos', 'NXB Hội Nhà Văn', 2020, '9786041046719', 244, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 320, NULL, 108000.00, 'Một tác phẩm đầy cảm động về tuổi thơ nghèo khó nhưng giàu tình yêu thương. Zezé - cậu bé năm tuổi tinh nghịch, thông minh và nhạy cảm đã trải qua những ngày tháng khó khăn nhất trong cuộc đời mình. Cuốn sách là lời nhắn nhủ sâu sắc về ý nghĩa của tình yêu thương và sự hy sinh.', 'uploads/cay_cam_ngot.jpg', 179, 381),
('SP004', 1, 'Café Sáng Với Tony', 'Tony Buổi Sáng', 'NXB Trẻ', 2019, '9786041109629', 280, 'Tiếng Việt', 'Bìa mềm', '13 x 19 cm', 300, 'Café', 95000.00, 'Tuyển tập những bài viết truyền cảm hứng từ Tony Buổi Sáng. Từng trang sách như những tách café đậm đà, đánh thức những suy nghĩ tích cực về cuộc sống, công việc và hạnh phúc. Một cuốn sách nhẹ nhàng nhưng đầy ý nghĩa cho những ai đang tìm kiếm động lực.', 'uploads/cafe_sang.jpg', 119, 191),
('SP005', 1, 'Tuổi Trẻ Đáng Giá Bao Nhiêu', 'Rosie Nguyễn', 'NXB Hội Nhà Văn', 2019, '9786041109735', 296, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 340, NULL, 89000.00, 'Cuốn sách dành cho tuổi trẻ đang loay hoay tìm kiếm ý nghĩa cuộc sống. Rosie Nguyễn chia sẻ những trải nghiệm thực tế, những bài học quý giá về việc sống có ý nghĩa, làm việc hiệu quả và yêu thương bản thân. Đây là kim chỉ nam cho thế hệ trẻ trong hành trình trưởng thành.', 'uploads/tuoi_tre_dang_gia.jpg', 159, 321),
('SP006', 2, 'Từ Tốt Đến Vĩ Đại', 'Jim Collins', 'NXB Trẻ', 2020, '9786041109957', 432, 'Tiếng Việt', 'Bìa mềm', '15.5 x 23 cm', 580, NULL, 169000.00, 'Jim Collins nghiên cứu những công ty có bước nhảy vọt và bền vững để trả lời câu hỏi: Điều gì làm nên sự khác biệt giữa công ty tốt và công ty vĩ đại? Cuốn sách đưa ra những phát hiện đi ngược lại với nhiều quan niệm trước đây về quản trị, lãnh đạo và chiến lược kinh doanh.', 'uploads/tu_tot_den_vi_dai.jpg', 99, 181),
('SP007', 2, 'Nghĩ Giàu Và Làm Giàu', 'Napoleon Hill', 'NXB Tổng Hợp TPHCM', 2019, '9786041096097', 368, 'Tiếng Việt', 'Bìa mềm', '14.5 x 20.5 cm', 420, NULL, 120000.00, 'Tác phẩm bất hủ về triết lý làm giàu của Napoleon Hill. Cuốn sách tổng hợp 13 nguyên tắc thành công từ việc nghiên cứu 500 triệu phú nổi tiếng. Đây không chỉ là cuốn sách về tiền bạc mà còn là kim chỉ nam về tư duy, thái độ và hành động để đạt được thành công.', 'uploads/nghi_giau_lam_giau.jpg', 89, 151),
('SP008', 2, 'Đời Ngắn Đừng Ngủ Dài', 'Robin Sharma', 'NXB Trẻ', 2021, '9786041110281', 256, 'Tiếng Việt', 'Bìa mềm', '13 x 20 cm', 290, NULL, 98000.00, 'Robin Sharma chia sẻ những chiến lược đơn giản nhưng mạnh mẽ để thay đổi cuộc sống. Cuốn sách hướng dẫn cách tối ưu hóa thời gian, năng lượng và tiềm năng của bạn để đạt được thành công vượt bậc. Mỗi chương là một bài học quý giá về năng suất và hiệu quả.', 'uploads/doi_ngan_dung_ngu_dai.jpg', 129, 211),
('SP009', 2, 'Bí Mật Tư Duy Triệu Phú', 'T. Harv Eker', 'NXB Tổng Hợp TPHCM', 2020, '9786041096103', 312, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 380, NULL, 135000.00, 'T. Harv Eker tiết lộ những nguyên tắc về \"bản đồ tài chính\" - hệ thống niềm tin chi phối mối quan hệ của chúng ta với tiền bạc. Cuốn sách giúp bạn nhận diện và thay đổi những rào cản tâm lý để đạt được tự do tài chính. Đây là cuốn sách thay đổi mindset về tiền bạc.', 'uploads/bi_mat_tu_duy_trieu_phu.jpg', 109, 166),
('SP010', 2, 'Chiến Lược Đại Dương Xanh', 'W. Chan Kim, Renée Mauborgne', 'NXB Trẻ', 2021, '9786041110298', 348, 'Tiếng Việt', 'Bìa cứng', '16 x 24 cm', 620, NULL, 189000.00, 'W. Chan Kim và Renée Mauborgne giới thiệu khái niệm \"đại dương xanh\" - không gian thị trường mới chưa được khai phá. Thay vì cạnh tranh khốc liệt trong \"đại dương đỏ\", các doanh nghiệp nên tạo ra giá trị độc đáo. Cuốn sách đi kèm hàng trăm ví dụ thực tế từ các công ty thành công.', 'uploads/chien_luoc_dai_duong_xanh.jpg', 84, 121),
('SP011', 3, 'Dế Mèn Phiêu Lưu Ký', 'Tô Hoài', 'NXB Kim Đồng', 2020, '9786042097451', 196, 'Tiếng Việt', 'Bìa mềm', '14 x 20 cm', 250, NULL, 65000.00, 'Tác phẩm kinh điển của văn học thiếu nhi Việt Nam. Câu chuyện về chú dế mèn dũng cảm, ham học hỏi và luôn sẵn sàng giúp đỡ bạn bè. Qua những cuộc phiêu lưu, Dế Mèn học được nhiều bài học quý giá về tình bạn, lòng dũng cảm và sự khôn ngoan.', 'uploads/de_men_phieu_luu_ky.jpg', 199, 421),
('SP012', 3, 'Hoàng Tử Bé', 'Antoine de Saint-Exupéry', 'NXB Hội Nhà Văn', 2019, '9786041046740', 128, 'Tiếng Việt', 'Bìa mềm', '13 x 19 cm', 180, NULL, 72000.00, 'Câu chuyện cảm động về hoàng tử bé từ hành tinh nhỏ B612. Cuốn sách là bài học sâu sắc về tình yêu, trách nhiệm và ý nghĩa cuộc sống qua con mắt trong sáng của một đứa trẻ. Một tác phẩm dành cho mọi lứa tuổi, mỗi lần đọc lại là một lần khám phá mới.', 'uploads/hoang_tu_be.jpg', 179, 381),
('SP013', 3, 'Đảo Giấu Vàng', 'Robert Louis Stevenson', 'NXB Kim Đồng', 2020, '9786042097468', 288, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 340, NULL, 89000.00, 'Cuộc phiêu lưu ly kỳ đi tìm kho báu trên đảo hoang. Jim Hawkins - cậu bé dũng cảm cùng thuyền trưởng và thủy thủ đoàn vượt qua ngàn trùng hiểm nguy. Tác phẩm kinh điển về lòng can đảm, sự trung thành và tinh thần phiêu lưu mạo hiểm.', 'uploads/dao_giau_vang.jpg', 139, 281),
('SP014', 3, 'Harry Potter Và Hòn Đá Phù Thủy', 'J.K. Rowling', 'NXB Trẻ', 2021, '9786041110304', 368, 'Tiếng Việt', 'Bìa cứng', '15 x 23 cm', 540, 'Harry Potter', 195000.00, 'Khởi đầu hành trình kỳ diệu của cậu bé Harry Potter tại trường phù thủy Hogwarts. Một thế giới ma thuật đầy màu sắc với những cuộc phiêu lưu gay cấn, tình bạn chân thành và sự đối đầu giữa thiện và ác. Đây là hiện tượng văn học thế giới, cuốn sách mở đầu cho series huyền thoại.', 'uploads/harry_potter_1.jpg', 149, 521),
('SP015', 3, 'Những Cuộc Phiêu Lưu Của Tom Sawyer', 'Mark Twain', 'NXB Kim Đồng', 2019, '9786042097475', 312, 'Tiếng Việt', 'Bìa mềm', '14.5 x 20.5 cm', 390, NULL, 98000.00, 'Câu chuyện về cậu bé nghịch ngợm nhưng thông minh Tom Sawyer sống ở miền nam nước Mỹ thế kỷ 19. Những trò nghịch ngợm, cuộc phiêu lưu đầy màu sắc và tình bạn trong sáng. Mark Twain đã tạo nên tác phẩm bất hủ về tuổi thơ đầy ắp niềm vui và sự tò mò.', 'uploads/tom_sawyer.jpg', 119, 241),
('SP016', 4, 'One Piece - Tập 1', 'Eiichiro Oda', 'NXB Kim Đồng', 2020, '9786042097482', 208, 'Tiếng Việt', 'Bìa mềm', '11.5 x 17.5 cm', 180, 'One Piece', 25000.00, 'Khởi đầu hành trình của Luffy và băng hải tặc Mũ Rơm. Cuộc phiêu lưu vĩ đại để tìm kho báu One Piece và trở thành Vua Hải Tặc. Với câu chuyện hấp dẫn, nhân vật đa dạng và thông điệp về tình bạn, ước mơ, One Piece đã trở thành manga huyền thoại với hơn 500 triệu bản in trên toàn thế giới.', 'uploads/one_piece_1.jpg', 299, 891),
('SP017', 4, 'Naruto - Tập 1', 'Masashi Kishimoto', 'NXB Kim Đồng', 2019, '9786042097499', 192, 'Tiếng Việt', 'Bìa mềm', '11.5 x 17.5 cm', 170, 'Naruto', 25000.00, 'Câu chuyện về Uzumaki Naruto - cậu bé ninja mồ côi mang trong mình con cáo chín đuôi, ước mơ trở thành Hokage. Qua những thử thách, Naruto dần trưởng thành và chinh phục trái tim mọi người. Một tác phẩm về lòng kiên trì, tình bạn và sự hy sinh.', 'uploads/naruto_1.jpg', 279, 851),
('SP018', 4, 'Conan - Thám Tử Lừng Danh - Tập 1', 'Gosho Aoyama', 'NXB Kim Đồng', 2020, '9786042097505', 180, 'Tiếng Việt', 'Bìa mềm', '11.5 x 17.5 cm', 165, 'Detective Conan', 25000.00, 'Kudo Shinichi bị teo nhỏ thành cậu bé Conan sau khi bị ép uống thuốc độc. Với trí tuệ phi thường, Conan giải quyết những vụ án bí ẩn trong khi tìm cách quay về hình dạng ban đầu. Series trinh thám kinh điển với hơn 100 tập, mỗi vụ án là một câu đố hấp dẫn.', 'uploads/conan_1.jpg', 319, 921),
('SP019', 4, 'Dragon Ball - Tập 1', 'Akira Toriyama', 'NXB Kim Đồng', 2021, '9786042097512', 196, 'Tiếng Việt', 'Bìa mềm', '11.5 x 17.5 cm', 175, 'Dragon Ball', 25000.00, 'Cuộc phiêu lưu tìm kiếm bảy viên ngọc rồng của Son Goku. Từ cậu bé hoang dã đến chiến binh mạnh nhất vũ trụ, Dragon Ball là huyền thoại manga với tầm ảnh hưởng toàn cầu. Akira Toriyama đã tạo nên thế giới đầy màu sắc với những trận chiến hoành tráng và thông điệp về lòng dũng cảm.', 'uploads/dragon_ball_1.jpg', 259, 781),
('SP020', 4, 'Doraemon - Tập 1', 'Fujiko F. Fujio', 'NXB Kim Đồng', 2020, '9786042097529', 196, 'Tiếng Việt', 'Bìa mềm', '11.5 x 17.5 cm', 160, 'Doraemon', 20000.00, 'Chú mèo máy đến từ tương lai cùng những bảo bối thần kỳ giúp đỡ Nobita. Doraemon mang đến những câu chuyện ấm áp, hài hước về tình bạn, gia đình và những bài học cuộc sống. Đây là manga kinh điển cho mọi lứa tuổi, đã đồng hành với nhiều thế hệ độc giả Việt Nam.', 'uploads/doraemon_1.jpg', 349, 1201),
('SP021', 1, '1984', 'George Orwell', 'NXB Hội Nhà Văn', 2020, '9786041046757', 396, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 450, NULL, 125000.00, 'Tác phẩm phản địa đàng kinh điển về một xã hội toàn trị, nơi Big Brother giám sát mọi hành động và suy nghĩ. Winston Smith nổi loạn chống lại hệ thống nhưng phải đối mặt với hậu quả khủng khiếp. George Orwell đã tạo nên một tác phẩm cảnh báo sâu sắc về quyền lực và tự do.', 'uploads/1984.jpg', 94, 146),
('SP022', 2, 'Sapiens: Lược Sử Loài Người', 'Yuval Noah Harari', 'NXB Trẻ', 2021, '9786041110311', 544, 'Tiếng Việt', 'Bìa cứng', '15.5 x 23 cm', 720, NULL, 189000.00, 'Yuval Noah Harari dẫn dắt chúng ta qua 70.000 năm lịch sử loài người từ khi là loài vượn không đáng kể cho đến khi thống trị hành tinh. Cuốn sách đặt ra những câu hỏi lớn về bản chất con người, xã hội và tương lai. Một tác phẩm làm thay đổi cách nhìn về chính mình và thế giới.', 'uploads/sapiens.jpg', 119, 281),
('SP023', 1, 'Người Đưa Thư Của Nỗi Buồn', 'Gabriel García Márquez', 'NXB Hội Nhà Văn', 2019, '9786041046764', 284, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 360, NULL, 145000.00, 'Gabriel García Márquez kể về thị trấn nhỏ bị cô lập bởi bão tuyết kéo dài. Câu chuyện huyền bí và buồn bã về tình yêu, cô độc và sự tuyệt vọng. Văn phong ma thuật hiện thực đặc trưng đã tạo nên một tác phẩm nghệ thuật đích thực từ bậc thầy Nobel văn chương.', 'uploads/nguoi_dua_thu.jpg', 79, 111),
('SP024', 1, 'Rừng Na Uy', 'Haruki Murakami', 'NXB Hội Nhà Văn', 2020, '9786041046771', 468, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 520, NULL, 165000.00, 'Haruki Murakami kể về Watanabe - chàng sinh viên Tokyo và câu chuyện tình yêu đầy ám ảnh với Naoko - cô gái mang theo nỗi buồn sâu thẳm. Một tác phẩm về tuổi trẻ, sự mất mát và tìm kiếm bản thân trong thế giới hiện đại. Rừng Na Uy đã trở thành biểu tượng văn học Nhật Bản đương đại.', 'uploads/rung_na_uy.jpg', 109, 241),
('SP025', 1, 'Những Tấm Lòng Cao Cả', 'Victor Hugo', 'NXB Văn Học', 2021, '9786041046788', 672, 'Tiếng Việt', 'Bìa cứng', '15.5 x 23 cm', 850, NULL, 198000.00, 'Victor Hugo tái hiện Paris thế kỷ 15 qua câu chuyện của Quasimodo - người gù nhà thờ Đức Bà, cô gái xinh đẹp Esmeralda và linh mục Frollo. Một thiên sử thi về tình yêu, lòng nhân ái và sự phán xét của xã hội. Tác phẩm bất hủ về cái đẹp tâm hồn vượt lên ngoại hình.', 'uploads/nha_tho_duc_ba.jpg', 74, 126),
('SP026', 1, 'Số Đỏ', 'Vũ Trọng Phụng', 'NXB Văn học', 2023, '978-604-56-7890-1', 280, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 320, NULL, 89000.00, 'Tác phẩm kinh điển của Vũ Trọng Phụng, một bức tranh sống động về xã hội Hà Nội những năm 1930 với nhân vật Xuân Tóc Đỏ nổi tiếng. Tác phẩm châm biếm sắc sảo về thói đời, về những kẻ cơ hội, về xã hội suy đồi.', 'uploads/so_do.jpg', 149, 46),
('SP027', 1, 'Lão Hạc', 'Nam Cao', 'NXB Kim Đồng', 2023, '978-604-56-7891-2', 180, 'Tiếng Việt', 'Bìa mềm', '19 x 13 cm', 200, NULL, 65000.00, 'Truyện ngắn nổi tiếng của Nam Cao kể về cuộc đời bi thảm của ông lão nghèo khó Lão Hạc và con chó của ông. Một tác phẩm đầy nhân văn, phản ánh hiện thực đau thương của nông dân Việt Nam trước cách mạng.', 'uploads/lao_hac.jpg', 199, 68),
('SP028', 1, 'Chí Phèo', 'Nam Cao', 'NXB Văn học', 2023, '978-604-56-7892-3', 150, 'Tiếng Việt', 'Bìa mềm', '19 x 13 cm', 180, NULL, 59000.00, 'Truyện ngắn xuất sắc của Nam Cao về nhân vật Chí Phèo - một người nông dân bị xã hội đẩy đưa vào con đường sa đọa. Tác phẩm phê phán sâu sắc chế độ xã hội cũ và thể hiện khả năng miêu tả tâm lý nhân vật tuyệt vời.', 'uploads/chi_pheo.jpg', 179, 53),
('SP029', 1, 'Vợ Nhặt', 'Kim Lân', 'NXB Kim Đồng', 2023, '978-604-56-7893-4', 120, 'Tiếng Việt', 'Bìa mềm', '19 x 13 cm', 150, NULL, 55000.00, 'Truyện ngắn của Kim Lân viết về tình yêu thương chân thành giữa người với người qua hình ảnh người đàn ông nhặt được người vợ trong nạn đói 1945. Một tác phẩm đầy tính nhân văn và cảm động.', 'uploads/vo_nhat.jpg', 159, 49),
('SP030', 1, 'Tắt Đèn', 'Ngô Tất Tố', 'NXB Văn học', 2023, '978-604-56-7894-5', 320, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 350, NULL, 95000.00, 'Tiểu thuyết của Ngô Tất Tố phản ánh cuộc sống khốn khổ của nông dân Việt Nam đầu thế kỷ XX. Qua gia đình cô Dậu - chị Dậu, tác phẩm khắc họa sinh động bi kịch của người nông dân bị bóc lột.', 'uploads/tat_den.jpg', 139, 39),
('SP031', 1, 'Chiến Tranh Và Hòa Bình', 'Lev Tolstoy', 'NXB Văn học', 2023, '978-604-56-7895-6', 1200, 'Tiếng Việt', 'Bìa cứng', '23 x 16 cm', 1400, NULL, 450000.00, 'Kiệt tác của Lev Tolstoy, một trong những tiểu thuyết vĩ đại nhất mọi thời đại. Tác phẩm mô tả cuộc xâm lược nước Nga của Napoleon qua số phận các gia đình quý tộc, đan xen triết lý lịch sử sâu sắc.', 'uploads/chien_tranh_hoa_binh.jpg', 79, 16),
('SP032', 1, 'Tội Ác Và Trừng Phạt', 'Fyodor Dostoevsky', 'NXB Văn học', 2023, '978-604-56-7896-7', 680, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 720, NULL, 198000.00, 'Kiệt tác tâm lý của Dostoevsky kể về sinh viên nghèo Raskolnikov giết người cầm đồ và cuộc đấu tranh nội tâm đau khổ sau đó. Một tác phẩm triết học văn học sâu sắc về tội lỗi và sự cứu rỗi.', 'uploads/toi_ac_trung_phat.jpg', 99, 29),
('SP033', 1, 'Cha Già Goriot', 'Honoré de Balzac', 'NXB Văn học', 2023, '978-604-56-7897-8', 420, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 450, NULL, 135000.00, 'Tác phẩm nổi tiếng của Balzac thuộc bộ truyện Hài kịch nhân sinh. Câu chuyện về người cha già hy sinh tất cả cho hai cô con gái vô ơn, phản ánh xã hội Paris thế kỷ 19 với chủ nghĩa vật chất thống trị.', 'uploads/cha_gia_goriot.jpg', 109, 33),
('SP034', 1, 'Những Người Khốn Khổ', 'Victor Hugo', 'NXB Văn học', 2023, '978-604-56-7898-9', 1100, 'Tiếng Việt', 'Bìa cứng', '23 x 16 cm', 1300, NULL, 380000.00, 'Kiệt tác của Victor Hugo về Jean Valjean - từ tù nhân trở thành người cao thượng. Tác phẩm nhân văn vĩ đại phê phán xã hội bất công, ca ngợi lòng nhân ái và sự cứu rỗi.', 'uploads/nguoi_khon_kho.jpg', 83, 24),
('SP035', 1, 'Người Tình', 'Marguerite Duras', 'NXB Hội Nhà Văn', 2023, '978-604-56-7899-0', 180, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 220, NULL, 98000.00, 'Tiểu thuyết tự truyện của Marguerite Duras kể về mối tình đầu của cô gái Pháp 15 tuổi với người tình Trung Quốc giàu có ở Sài Gòn thập niên 1930. Văn xuôi tinh tế, cảm xúc sâu lắng.', 'uploads/nguoi_tinh.jpg', 129, 46),
('SP036', 2, 'Nghệ Thuật Bán Hàng Vĩ Đại Nhất Thế Giới', 'Og Mandino', 'NXB Lao động', 2023, '978-604-56-7900-3', 280, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 300, NULL, 119000.00, 'Cuốn sách kinh điển của Og Mandino về nghệ thuật bán hàng thông qua câu chuyện Hafid - từ cậu bé chăn lạc đà trở thành thương gia giàu có nhất. 10 cuộn giấy da với những bí quyết thành công vượt thời gian.', 'uploads/nghe_thuat_ban_hang.jpg', 176, 72),
('SP037', 2, 'Khởi Nghiệp Tinh Gọn', 'Eric Ries', 'NXB Trẻ', 2023, '978-604-56-7901-4', 320, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 340, NULL, 145000.00, 'Sách hướng dẫn phương pháp khởi nghiệp hiện đại của Eric Ries - xây dựng sản phẩm nhanh, thử nghiệm và điều chỉnh liên tục. Một cuốn sách bắt buộc cho mọi startup và doanh nghiệp muốn đổi mới.', 'uploads/khoi_nghiep_tinh_gon.jpg', 148, 54),
('SP038', 2, 'Đắc Nhân Tâm Trong Kinh Doanh', 'Dale Carnegie & Associates', 'NXB Tổng hợp TP.HCM', 2023, '978-604-56-7902-5', 350, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 370, NULL, 129000.00, 'Ứng dụng các nguyên tắc trong Đắc Nhân Tâm vào thế giới kinh doanh. Hướng dẫn cách xây dựng mối quan hệ, thuyết phục khách hàng và phát triển doanh nghiệp bền vững.', 'uploads/dac_nhan_tam_kinh_doanh.jpg', 159, 59),
('SP039', 2, 'Tư Duy Nhanh Và Chậm', 'Daniel Kahneman', 'NXB Thế Giới', 2023, '978-604-56-7903-6', 580, 'Tiếng Việt', 'Bìa mềm', '22 x 15 cm', 650, NULL, 189000.00, 'Tác phẩm của nhà tâm lý học đoạt giải Nobel Daniel Kahneman về hai hệ thống tư duy của con người. Giải thích cách chúng ta đưa ra quyết định và những thiên kiến nhận thức.', 'uploads/tu_duy_nhanh_cham.jpg', 138, 44),
('SP040', 2, 'Chơi Lớn Hay Về Nhà', 'Pamela Anderson', 'NXB Lao động', 2023, '978-604-56-7904-7', 240, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 280, NULL, 99000.00, 'Sách của Pamela Anderson về cách xây dựng tư duy sư tử, dám nghĩ lớn và hành động táo bạo trong kinh doanh. Khích lệ người đọc thoát khỏi vùng an toàn để đạt thành công lớn.', 'uploads/choi_lon_ve_nha.jpg', 169, 63),
('SP041', 3, 'Làm Bạn Với Bầu Trời', 'Nguyễn Nhật Ánh', 'NXB Trẻ', 2023, '978-604-56-7905-8', 380, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 400, NULL, 119000.00, 'Truyện thiếu nhi của Nguyễn Nhật Ánh về tuổi thơ miền quê với những câu chuyện đầy cảm xúc. Tình bạn, tình thân, nỗi buồn và niềm vui của trẻ thơ được tái hiện sinh động.', 'uploads/lam_ban_voi_bau_troi.jpg', 198, 91),
('SP042', 3, 'Mắt Biếc', 'Nguyễn Nhật Ánh', 'NXB Trẻ', 2023, '978-604-56-7906-9', 420, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 440, NULL, 135000.00, 'Truyện dài của Nguyễn Nhật Ánh về tình yêu thầm lặng của Ngạn dành cho Hà Lan từ thuở nhỏ. Một câu chuyện tình đẹp, buồn và đầy cảm xúc về tuổi trẻ.', 'uploads/mat_biec.jpg', 179, 96),
('SP043', 3, 'Đồi Gió Hú', 'Emily Brontë', 'NXB Kim Đồng', 2023, '978-604-56-7907-0', 380, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 400, NULL, 115000.00, 'Tiểu thuyết kinh điển của Emily Brontë về tình yêu mãnh liệt và bi thương giữa Heathcliff và Catherine. Phiên bản thiếu nhi được chuyển thể phù hợp.', 'uploads/doi_gio_hu.jpg', 119, 36),
('SP044', 3, 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 'Nguyễn Nhật Ánh', 'NXB Trẻ', 2023, '978-604-56-7908-1', 400, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 420, NULL, 125000.00, 'Tác phẩm nổi tiếng của Nguyễn Nhật Ánh về tuổi thơ ở miền quê với những ký ức đẹp đẽ, chân thật. Câu chuyện về tình anh em, tình làng nghĩa xóm đầy xúc động.', 'uploads/hoa_vang_co_xanh.jpg', 188, 107),
('SP045', 3, 'Sherlock Holmes - Thám Tử Lừng Danh', 'Arthur Conan Doyle', 'NXB Kim Đồng', 2023, '978-604-56-7909-2', 480, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 500, NULL, 145000.00, 'Tuyển tập truyện trinh thám nổi tiếng của Arthur Conan Doyle về thám tử thiên tài Sherlock Holmes. Phiên bản dành cho thiếu nhi với ngôn ngữ dễ hiểu, hấp dẫn.', 'uploads/sherlock_holmes.jpg', 138, 50),
('SP046', 4, 'Attack On Titan - Tập 1', 'Hajime Isayama', 'NXB Kim Đồng', 2023, '978-604-56-7910-5', 180, 'Tiếng Việt', 'Bìa mềm', '18 x 13 cm', 200, 'Attack On Titan', 35000.00, 'Manga đình đám của Hajime Isayama về thế giới con người bị titan khổng lồ tấn công. Eren Yeager quyết tâm tiêu diệt tất cả titan sau khi chứng kiến mẹ mình bị giết. Cốt truyện hấp dẫn, đầy bất ngờ.', 'uploads/attack_on_titan_1.jpg', 199, 146),
('SP047', 4, 'My Hero Academia - Tập 1', 'Kohei Horikoshi', 'NXB Kim Đồng', 2023, '978-604-56-7911-6', 180, 'Tiếng Việt', 'Bìa mềm', '18 x 13 cm', 200, 'My Hero Academia', 32000.00, 'Manga của Kohei Horikoshi về thế giới 80% dân số có siêu năng lực. Izuku Midoriya sinh ra không có năng lực nhưng vẫn mơ trở thành anh hùng vĩ đại nhất. Câu chuyện truyền cảm hứng mạnh mẽ.', 'uploads/my_hero_academia_1.jpg', 178, 127),
('SP048', 4, 'Demon Slayer - Tập 1', 'Koyoharu Gotouge', 'NXB Kim Đồng', 2023, '978-604-56-7912-7', 180, 'Tiếng Việt', 'Bìa mềm', '18 x 13 cm', 200, 'Demon Slayer', 33000.00, 'Manga của Koyoharu Gotouge về Tanjirou - cậu bé trở thành sát quỷ sau khi gia đình bị giết bởi quỷ. Em gái Nezuko biến thành quỷ nhưng giữ được ý thức. Hành trình tìm cách cứu em và báo thù bắt đầu.', 'uploads/demon_slayer_1.jpg', 188, 140),
('SP049', 4, 'Tokyo Ghoul - Tập 1', 'Sui Ishida', 'NXB Kim Đồng', 2023, '978-604-56-7913-8', 180, 'Tiếng Việt', 'Bìa mềm', '18 x 13 cm', 200, 'Tokyo Ghoul', 34000.00, 'Manga kinh dị của Sui Ishida về Ken Kaneki - sinh viên biến thành nửa người nửa ghoul sau tai nạn. Phải sống trong thế giới ngầm đầy nguy hiểm, đấu tranh giữa hai bản tính.', 'uploads/tokyo_ghoul_1.jpg', 158, 100),
('SP050', 4, 'Fullmetal Alchemist - Tập 1', 'Hiromu Arakawa', 'NXB Kim Đồng', 2023, '978-604-56-7914-9', 180, 'Tiếng Việt', 'Bìa mềm', '18 x 13 cm', 200, 'Fullmetal Alchemist', 35000.00, 'Manga của Hiromu Arakawa về hai anh em nhà Elric - Edward và Alphonse. Sau khi thất bại trong thuật luyện kim để hồi sinh mẹ, họ bắt đầu hành trình tìm Đá Hiền Giả để lấy lại cơ thể. Cốt truyện sâu sắc, cảm động.', 'uploads/fullmetal_alchemist_1.jpg', 169, 113);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

CREATE TABLE `promotions` (
  `promotion_id` int(11) NOT NULL,
  `promotion_code` varchar(50) NOT NULL,
  `promotion_name` varchar(255) NOT NULL,
  `promotion_type` enum('product','category','flash_sale','coupon','minimum_order') NOT NULL,
  `discount_type` enum('percentage','fixed_amount') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `status` enum('active','inactive','expired') DEFAULT 'active',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`promotion_id`, `promotion_code`, `promotion_name`, `promotion_type`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount`, `start_date`, `end_date`, `usage_limit`, `used_count`, `status`, `description`, `created_at`, `updated_at`) VALUES
(1, 'FSCHOPNHOANG', 'Flash Sale Khai Trương Cửa Hàng', 'flash_sale', 'percentage', 10.00, 0.00, 20000.00, '2025-11-22 00:00:00', '2025-11-23 02:31:00', NULL, 0, 'active', 'Flash Sale khai trương Cửa hàng ngày 22/11/2025', '2025-11-22 07:19:24', '2025-11-22 07:19:24'),
(2, 'BOOKHAY', 'Giảm giá ngày hội sách', 'coupon', 'percentage', 20.00, 0.00, 20000.00, '2025-12-06 19:03:00', '2025-12-07 19:04:00', NULL, 0, 'active', 'Mua sách hay giảm thật nhiều!', '2025-12-06 12:04:31', '2025-12-06 12:04:31'),
(4, 'OKANHA', 'Giảm giá ngày hội sách', 'flash_sale', 'percentage', 20.00, 0.00, NULL, '2025-12-08 19:46:00', '2025-12-09 19:46:00', NULL, 5, 'active', 'Khuyến mãi hay á nha', '2025-12-08 12:46:46', '2025-12-08 12:58:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotion_categories`
--

CREATE TABLE `promotion_categories` (
  `id` int(11) NOT NULL,
  `promotion_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotion_products`
--

CREATE TABLE `promotion_products` (
  `id` int(11) NOT NULL,
  `promotion_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotion_usage`
--

CREATE TABLE `promotion_usage` (
  `id` int(11) NOT NULL,
  `promotion_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `promotion_usage`
--

INSERT INTO `promotion_usage` (`id`, `promotion_id`, `user_id`, `order_id`, `discount_amount`, `used_at`) VALUES
(1, 4, 5, 10, 123600.00, '2025-12-08 12:49:16'),
(2, 4, 5, 13, 154400.00, '2025-12-08 12:54:23'),
(3, 4, 5, 14, 52800.00, '2025-12-08 12:54:54'),
(4, 4, 5, 16, 48000.00, '2025-12-08 12:57:17'),
(5, 4, 5, 17, 54000.00, '2025-12-08 12:58:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `publishers`
--

CREATE TABLE `publishers` (
  `publisher_id` int(11) NOT NULL,
  `publisher_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `publishers`
--

INSERT INTO `publishers` (`publisher_id`, `publisher_name`, `description`, `address`, `phone`, `email`, `website`, `logo`, `status`, `created_at`, `updated_at`) VALUES
(1, 'NXB Tong hop TPHCM', 'Nha xuat ban Tong hop Thanh pho Ho Chi Minh', '62 Nguyen Thi Minh Khai, Q1, TP.HCM', '0283822 8344', 'info@nxbhcm.com.vn', 'https://www.nxbhcm.com.vn', NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(2, 'NXB Hoi Nha Van', 'Nha xuat ban Hoi Nha Van Viet Nam', '65 Nguyen Du, Hai Ba Trung, Ha Noi', '0243943 6460', 'nxbhoinhavan@gmail.com', 'https://nxbhoinhavan.vn', NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(3, 'NXB Tre', 'Nha xuat ban chuyen ve sach thieu nhi va van hoc', '161B Ly Chinh Thang, Quan 3, TP.HCM', '0283932704', 'info@nxbtre.com.vn', 'https://www.nxbtre.com.vn', NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(4, 'NXB Kim Dong', 'Nha xuat ban sach thieu nhi hang dau Viet Nam', '55 Quang Trung, Hai Ba Trung, Ha Noi', '0243943463', 'info@nxbkimdong.com.vn', 'https://nxbkimdong.com.vn', NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(5, 'NXB Van hoc', 'Chuyen xuat ban cac tac pham van hoc Viet Nam va the gioi', '18 Nguyen Truong To, Ba Dinh, Ha Noi', '0243733723', 'info@nxbvanhoc.com.vn', 'https://nxbvanhoc.com.vn', NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(6, 'NXB Lao dong', 'Xuat ban sach ve kinh te, xa hoi, ky nang', '175 Giang Vo, Dong Da, Ha Noi', '0243514932', 'nxblaodong@gmail.com', 'https://nxblaodong.com.vn', NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06'),
(7, 'NXB The Gioi', 'Chuyen dich va xuat ban sach nuoc ngoai', '46 Tran Hung Dao, Hoan Kiem, Ha Noi', '0243253841', 'thegioi@thegioipublishers.vn', 'https://www.thegioipublishers.vn', NULL, 'active', '2025-11-28 14:42:06', '2025-11-28 14:42:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `content` text NOT NULL,
  `images` text DEFAULT NULL,
  `review_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `product_id`, `order_id`, `rating`, `content`, `images`, `review_date`) VALUES
(6, 5, 'SP034', 10, 4, 'ok', '[\"uploads\\/reviews\\/6940e88074df2_c6213b872bc2dfe1e101de6445c865ac.webp\"]', '2025-12-16 12:05:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `google_picture` varchar(500) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `google_id`, `google_picture`, `email`, `full_name`, `phone`, `address`, `created_at`, `is_admin`) VALUES
(3, 'xali', '$2y$10$/NLh6o1wo5up/tlYlvBIN.Z3uG4M3svGtHTSgj6Rt3K601quPyKUS', '112326748375285415981', 'https://lh3.googleusercontent.com/a/ACg8ocIlCuXYXca8gBf8Ys-a_nlqCUtIF1O5yGbPEsUm2qsFkGYafZ41=s96-c', 'dubu2k4@gmail.com', 'Trần Thanh Thưởng', '0392656499', 'Cầu Ngang trà vinh', '2024-12-04 17:49:41', 0),
(4, 'ss', '$2y$10$8egLwGoW1DE9HKJBVeCKT.QitqaKinmIhSqNr5mZ7.4XDixfMokq6', NULL, NULL, 'dubu2k@gmail.com', 'Trần Thanh Thưởng', '0392656499', 'dsdsd', '2024-12-04 17:59:57', 0),
(5, 'dihi', '$2y$10$lWgkTUKS1fedQ2vR8tjQiOws6g9nTgxfgnYe93ORh6NKXq7SuN9JG', NULL, NULL, 'nguyena@gmail.com', 'Nguyễn Văn A', '0987876552', 'Cầu Ngang trà vinh', '2025-11-21 04:31:29', 0),
(6, 'thuongne', '$2y$10$BUl/ZTDyHPmd86emA6876Owr/tKHE4fe8G4uRKC6vsf3.NXDw/xdu', NULL, NULL, 'thuongne@gmail.com', 'Nguyễn Văn Em', '0987876552', 'Cầu Ngang trà vinh', '2025-12-08 12:45:05', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `added_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Chỉ mục cho bảng `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`admin_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Chỉ mục cho bảng `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Chỉ mục cho bảng `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Chỉ mục cho bảng `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`post_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Chỉ mục cho bảng `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_sender` (`sender_id`,`sender_type`),
  ADD KEY `idx_receiver` (`receiver_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_author` (`author`),
  ADD KEY `idx_publisher` (`publisher`),
  ADD KEY `idx_isbn` (`isbn`),
  ADD KEY `idx_series` (`series`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promotion_id`),
  ADD UNIQUE KEY `promotion_code` (`promotion_code`),
  ADD KEY `idx_code` (`promotion_code`),
  ADD KEY `idx_type` (`promotion_type`),
  ADD KEY `idx_dates` (`start_date`,`end_date`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `promotion_categories`
--
ALTER TABLE `promotion_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_promo_category` (`promotion_id`,`category_id`);

--
-- Chỉ mục cho bảng `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_promo_product` (`promotion_id`,`product_id`);

--
-- Chỉ mục cho bảng `promotion_usage`
--
ALTER TABLE `promotion_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_id` (`promotion_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Chỉ mục cho bảng `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`publisher_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id_UNIQUE` (`google_id`);

--
-- Chỉ mục cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT cho bảng `administrators`
--
ALTER TABLE `administrators`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `authors`
--
ALTER TABLE `authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT cho bảng `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=421;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `promotions`
--
ALTER TABLE `promotions`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `promotion_categories`
--
ALTER TABLE `promotion_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `promotion_products`
--
ALTER TABLE `promotion_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `promotion_usage`
--
ALTER TABLE `promotion_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `publishers`
--
ALTER TABLE `publishers`
  MODIFY `publisher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `administrators` (`admin_id`);

--
-- Các ràng buộc cho bảng `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD CONSTRAINT `admin_permissions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `administrators` (`admin_id`),
  ADD CONSTRAINT `admin_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`);

--
-- Các ràng buộc cho bảng `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `blog_comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `blog_comments` (`comment_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`category_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `blog_posts_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `authors` (`author_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Các ràng buộc cho bảng `promotion_categories`
--
ALTER TABLE `promotion_categories`
  ADD CONSTRAINT `promotion_categories_ibfk_1` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`promotion_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD CONSTRAINT `promotion_products_ibfk_1` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`promotion_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `promotion_usage`
--
ALTER TABLE `promotion_usage`
  ADD CONSTRAINT `promotion_usage_ibfk_1` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`promotion_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Các ràng buộc cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
