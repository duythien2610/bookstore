-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 08, 2026 at 04:41 AM
-- Server version: 8.0.42
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookstoredb`
--

-- --------------------------------------------------------

--
-- Table structure for table `danh_gia`
--

DROP TABLE IF EXISTS `danh_gia`;
CREATE TABLE IF NOT EXISTS `danh_gia` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `sach_id` bigint UNSIGNED NOT NULL,
  `so_sao` int NOT NULL,
  `tieu_de` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `binh_luan` text COLLATE utf8mb4_unicode_ci,
  `trang_thai` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `danh_gia_user_id_foreign` (`user_id`),
  KEY `danh_gia_sach_id_foreign` (`sach_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `don_hang`
--

DROP TABLE IF EXISTS `don_hang`;
CREATE TABLE IF NOT EXISTS `don_hang` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ma_giam_gia_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `ngay_dat` datetime NOT NULL,
  `trang_thai` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tong_tien` decimal(12,2) NOT NULL,
  `dia_chi_giao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phuong_thuc_tt` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trang_thai_tt` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'chua_thanh_toan',
  `ghi_chu` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `don_hang_user_id_foreign` (`user_id`),
  KEY `don_hang_ma_giam_gia_id_foreign` (`ma_giam_gia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `don_hang_chi_tiet`
--

DROP TABLE IF EXISTS `don_hang_chi_tiet`;
CREATE TABLE IF NOT EXISTS `don_hang_chi_tiet` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `don_hang_id` bigint UNSIGNED NOT NULL,
  `sach_id` bigint UNSIGNED NOT NULL,
  `so_luong` int NOT NULL,
  `don_gia` decimal(12,2) NOT NULL,
  `thanh_tien` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `don_hang_chi_tiet_don_hang_id_foreign` (`don_hang_id`),
  KEY `don_hang_chi_tiet_sach_id_foreign` (`sach_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_verifications`
--

DROP TABLE IF EXISTS `email_verifications`;
CREATE TABLE IF NOT EXISTS `email_verifications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `code_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_verifications_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gio_hang`
--

DROP TABLE IF EXISTS `gio_hang`;
CREATE TABLE IF NOT EXISTS `gio_hang` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `trang_thai` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tong_tien` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gio_hang_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gio_hang_chi_tiet`
--

DROP TABLE IF EXISTS `gio_hang_chi_tiet`;
CREATE TABLE IF NOT EXISTS `gio_hang_chi_tiet` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `gio_hang_id` bigint UNSIGNED NOT NULL,
  `sach_id` bigint UNSIGNED NOT NULL,
  `so_luong` int NOT NULL,
  `don_gia` decimal(12,2) NOT NULL,
  `thanh_tien` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gio_hang_chi_tiet_gio_hang_id_foreign` (`gio_hang_id`),
  KEY `gio_hang_chi_tiet_sach_id_foreign` (`sach_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ma_giam_gia`
--

DROP TABLE IF EXISTS `ma_giam_gia`;
CREATE TABLE IF NOT EXISTS `ma_giam_gia` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ma_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loai` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `gia_tri` decimal(12,2) NOT NULL,
  `ngay_het_han` date DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  `da_dung` int NOT NULL DEFAULT '0',
  `trang_thai` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_giam_gia_ma_code_unique` (`ma_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_08_19_000000_create_failed_jobs_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2026_03_03_035208_create_roles_table', 1),
(4, '2026_03_03_035250_create_tac_gia_table', 1),
(5, '2026_03_03_035342_create_nha_xuat_ban_table', 1),
(6, '2026_03_03_035519_create_nha_cung_cap_table', 1),
(7, '2026_03_03_035557_create_the_loai_table', 1),
(8, '2026_03_03_040058_create_users_table', 1),
(9, '2026_03_03_040121_create_sach_table', 1),
(10, '2026_03_03_040145_create_gio_hang_table', 1),
(11, '2026_03_03_040209_create_gio_hang_chi_tiet_table', 1),
(12, '2026_03_03_040238_create_don_hang_table', 1),
(13, '2026_03_03_040256_create_don_hang_chi_tiet_table', 1),
(14, '2026_03_03_040332_create_danh_gia_table', 1),
(15, '2026_03_04_033629_add_fields_to_sach_table', 1),
(16, '2026_03_04_033800_add_fields_to_don_hang_table', 1),
(17, '2026_03_04_033909_add_fields_to_users_table', 1),
(18, '2026_03_04_033948_create_ma_giam_gia_table', 1),
(19, '2026_03_04_034041_add_ma_giam_gia_to_don_hang_table', 1),
(20, '2026_03_04_034111_add_fields_to_danh_gia_table', 1),
(21, '2026_03_04_040056_add_missing_fields_to_sach_table', 1),
(22, '2026_03_04_042432_rename_mat_khau_to_password_in_users_table', 1),
(23, '2026_03_04_055629_add_parent_to_the_loai_table', 1),
(24, '2026_03_04_055839_add_loai_sach_to_sach_table', 1),
(25, '2026_03_07_060000_create_email_verifications_table', 1),
(26, '2026_03_07_065000_create_password_resets_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `nha_cung_cap`
--

DROP TABLE IF EXISTS `nha_cung_cap`;
CREATE TABLE IF NOT EXISTS `nha_cung_cap` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ten_ncc` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nha_cung_cap`
--

INSERT INTO `nha_cung_cap` (`id`, `ten_ncc`, `created_at`, `updated_at`) VALUES
(1, 'Đinh Tị', '2026-03-08 04:20:21', '2026-03-08 04:20:21'),
(2, 'CÔNG TY CỔ PHẦN VĂN HÓA VÀ TRUYỀN THÔNG LINH LAN', '2026-03-08 04:36:00', '2026-03-08 04:36:00');

-- --------------------------------------------------------

--
-- Table structure for table `nha_xuat_ban`
--

DROP TABLE IF EXISTS `nha_xuat_ban`;
CREATE TABLE IF NOT EXISTS `nha_xuat_ban` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ten_nxb` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nha_xuat_ban`
--

INSERT INTO `nha_xuat_ban` (`id`, `ten_nxb`, `created_at`, `updated_at`) VALUES
(1, 'Văn Học', '2026-03-08 04:19:55', '2026-03-08 04:19:55'),
(2, 'Dân Trí', '2026-03-08 04:34:03', '2026-03-08 04:34:03');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ten_vai_tro` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `ten_vai_tro`, `created_at`, `updated_at`) VALUES
(1, 'admin', '2026-03-07 15:43:17', '2026-03-07 15:43:17'),
(2, 'user', '2026-03-07 15:43:17', '2026-03-07 15:43:17');

-- --------------------------------------------------------

--
-- Table structure for table `sach`
--

DROP TABLE IF EXISTS `sach`;
CREATE TABLE IF NOT EXISTS `sach` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tieu_de` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isbn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nam_xuat_ban` year DEFAULT NULL,
  `so_trang` int DEFAULT NULL,
  `tac_gia_id` bigint UNSIGNED DEFAULT NULL,
  `nha_xuat_ban_id` bigint UNSIGNED DEFAULT NULL,
  `nha_cung_cap_id` bigint UNSIGNED DEFAULT NULL,
  `the_loai_id` bigint UNSIGNED DEFAULT NULL,
  `loai_sach` enum('trong_nuoc','nuoc_ngoai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trong_nuoc',
  `hinh_thuc_bia` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `file_anh_bia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_anh_bia` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gia_ban` decimal(12,2) NOT NULL,
  `gia_goc` decimal(12,2) DEFAULT NULL,
  `so_luong_ton` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sach_isbn_unique` (`isbn`),
  KEY `sach_tac_gia_id_foreign` (`tac_gia_id`),
  KEY `sach_nha_xuat_ban_id_foreign` (`nha_xuat_ban_id`),
  KEY `sach_nha_cung_cap_id_foreign` (`nha_cung_cap_id`),
  KEY `sach_the_loai_id_foreign` (`the_loai_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sach`
--

INSERT INTO `sach` (`id`, `tieu_de`, `isbn`, `nam_xuat_ban`, `so_trang`, `tac_gia_id`, `nha_xuat_ban_id`, `nha_cung_cap_id`, `the_loai_id`, `loai_sach`, `hinh_thuc_bia`, `mo_ta`, `file_anh_bia`, `link_anh_bia`, `gia_ban`, `gia_goc`, `so_luong_ton`, `created_at`, `updated_at`) VALUES
(1, 'Hồ Điệp Và Kình Ngư', '8935212370189', '2024', 272, 1, 1, 1, 3, 'trong_nuoc', 'bia_mem', 'Một cô gái trẻ đang sống một cuộc đời bình thường nhưng lại vô tình bị cuốn vào thế giới đen tối đầy bí ẩn của một người đàn ông nguy hiểm nhưng đầy cuốn hút. Anh là kẻ đứng trên đỉnh cao quyền lực, là người mà cô không nên yêu. Nhưng càng muốn trốn chạy, càng không thể thoát.\r\n\r\nGiữa họ là yêu hay hận? Là bảo vệ hay hủy diệt?\r\nLà vận mệnh đã an bài hay chỉ là một trò đùa tàn nhẫn của số phận?\r\n\r\nNhững bí mật chôn giấu dần được phơi bày, những lựa chọn đau đớn buộc phải đưa ra. Khi đã bước vào ván cờ sinh tử này, liệu tình yêu có đủ để cứu rỗi cả hai?\r\n\r\nĐIỀU GÌ KHIẾN BẠN KHÔNG THỂ BỎ LỠ CUỐN SÁCH NÀY?\r\nĐây không chỉ là một câu chuyện tình yêu, mà còn là một bức tranh chân thực về con người giữa những lựa chọn nghiệt ngã.\r\n\r\nSự kết hợp hoàn hảo giữa lãng mạn và kịch tính, giữa những cảm xúc nhẹ nhàng và những cao trào đầy đau đớn.\r\n\r\nChứa đựng những câu chữ tinh tế, sắc bén, lột tả chân thực những góc khuất trong lòng người.\r\n\r\n \r\n\r\n“HỒ ĐIỆP VÀ KÌNH NGƯ” MANG ĐẾN ĐIỀU GÌ?\r\nMột tác phẩm mang đậm màu sắc bi kịch và hiện thực, nơi tình yêu không chỉ có hạnh phúc mà còn là thử thách khắc nghiệt của số phận.\r\n\r\nMột câu chuyện với kết cấu chặt chẽ, tuyến nhân vật có chiều sâu, thể hiện rõ sự giằng xé giữa lý trí và tình cảm, giữa quá khứ và tương lai.\r\n\r\nMột hành trình khai thác nội tâm đầy ám ảnh, nơi từng quyết định nhỏ bé có thể thay đổi cả cuộc đời con người.\r\n\r\nMột cuốn sách mang lại nhiều tầng ý nghĩa, không chỉ dừng lại ở tình yêu mà còn là số phận, sự lựa chọn và cái giá của những khát vọng.\r\n\r\n \r\n​”Hồ điệp và kình ngư” - một cuốn sách đáng đọc, đáng suy ngẫm và đáng có trong tủ sách của bất kỳ ai yêu thích những tác phẩm đầy chiều sâu!', NULL, 'https://cdn1.fahasa.com/media/catalog/product/b/i/bia-2d_ho-diep-va-kinh-ngu_17307.jpg', 119350.00, NULL, 20, '2026-03-08 04:22:41', '2026-03-08 04:24:25'),
(2, 'Hà Thanh Hải Yến - Ngang Qua Ngõ Nhỏ Bình An', '8936213491613', '2024', 324, 2, 2, 2, 3, 'trong_nuoc', 'bia_mem', 'Hà Thanh Hải Yến - Ngang Qua Ngõ Nhỏ Bình An\r\n\r\nBị bố đánh đập dã man, bị bạn học bắt nạt, trong lúc tuyệt vọng cùng quẫn, tôi tìm đến tiệm xăm trong góc ngõ.\r\n\r\nNghe nói ông chủ là một tên côn đồ, rất hung hãn và dữ dằn, người xung quanh đều e sợ anh.\r\n\r\nĐẩy cửa, tôi moi từ trong túi ra một tờ mười tệ nhàu nhĩ, lấy hết dũng khí hỏi:\r\n\r\n“Nghe nói anh thu phí bảo kê, vậy anh... có thể bảo vệ tôi không?”\r\n\r\nGiữa làn khói thuốc lượn lờ, người đàn ông nhếch môi phì cười.\r\n\r\n“Nhóc con nhà ai đây? To gan thật đấy.”\r\n\r\nSau này, anh chỉ vì tờ mười tệ ấy mà bảo vệ tôi suốt mười năm.', NULL, 'https://cdn1.fahasa.com/media/catalog/product/b/_/b_a-_o-h_-thanh-h_i-y_n_b_a-tr_c.jpg', 156500.00, NULL, 20, '2026-03-08 04:37:34', '2026-03-08 04:37:34');

-- --------------------------------------------------------

--
-- Table structure for table `tac_gia`
--

DROP TABLE IF EXISTS `tac_gia`;
CREATE TABLE IF NOT EXISTS `tac_gia` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ten_tac_gia` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tac_gia`
--

INSERT INTO `tac_gia` (`id`, `ten_tac_gia`, `created_at`, `updated_at`) VALUES
(1, 'Tuế Kiến', '2026-03-08 04:19:33', '2026-03-08 04:19:33'),
(2, 'Quất Tử Bất Toan', '2026-03-08 04:33:49', '2026-03-08 04:33:49');

-- --------------------------------------------------------

--
-- Table structure for table `the_loai`
--

DROP TABLE IF EXISTS `the_loai`;
CREATE TABLE IF NOT EXISTS `the_loai` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `ten_the_loai` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `the_loai_parent_id_foreign` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `the_loai`
--

INSERT INTO `the_loai` (`id`, `parent_id`, `ten_the_loai`, `created_at`, `updated_at`) VALUES
(2, NULL, 'Văn học', '2026-03-07 15:47:37', '2026-03-07 15:47:37'),
(3, 2, 'Tiểu Thuyết', '2026-03-07 15:47:59', '2026-03-07 15:47:59'),
(5, 2, 'Light Novel', '2026-03-07 15:48:21', '2026-03-07 15:48:21'),
(6, 2, 'Truyện ngắn - Tản văn', '2026-03-07 15:48:52', '2026-03-07 15:48:52'),
(7, 2, 'Truyện Trinh Thám - Kiếm Hiệp', '2026-03-07 15:49:13', '2026-03-07 15:49:13'),
(8, 2, 'Tác Phẩm Kinh Điển', '2026-03-07 15:49:22', '2026-03-07 15:49:22'),
(9, 2, 'Huyền Bí - Giả Tưởng - Kinh Dị', '2026-03-07 15:49:43', '2026-03-07 15:49:43'),
(10, 2, 'Thơ Ca, Tục Ngữ, Ca Dao, Thành Ngữ', '2026-03-07 15:50:05', '2026-03-07 15:50:05'),
(11, NULL, 'Kinh Tế', '2026-03-07 15:50:55', '2026-03-07 15:50:55'),
(12, 11, 'Nhân Vật - Bài Học Kinh Doanh', '2026-03-07 15:51:14', '2026-03-07 15:51:14'),
(13, 11, 'Quản Trị - Lãnh Đạo', '2026-03-07 15:51:32', '2026-03-07 15:51:32'),
(14, 11, 'Marketing - Bán Hàng', '2026-03-07 15:51:37', '2026-03-07 15:51:37'),
(15, 11, 'Khởi Nghiệp - Làm Giàu', '2026-03-07 15:51:44', '2026-03-07 15:51:44'),
(16, 11, 'Phân Tích Kinh Tế', '2026-03-07 15:51:50', '2026-03-07 15:51:50'),
(17, NULL, 'Tâm lý - Kỹ năng sống', '2026-03-07 15:52:52', '2026-03-07 15:52:52'),
(18, 17, 'Kỹ năng sống', '2026-03-07 15:53:02', '2026-03-07 15:53:02'),
(19, 17, 'Tâm lý', '2026-03-07 15:53:08', '2026-03-07 15:53:08'),
(20, 17, 'Rèn luyện nhân cách', '2026-03-07 15:53:17', '2026-03-07 15:53:17'),
(21, NULL, 'Nuôi dạy con', '2026-03-07 15:55:16', '2026-03-07 15:55:16'),
(22, 21, 'Cẩm Nang Làm Cha Mẹ', '2026-03-07 15:55:30', '2026-03-07 15:55:30'),
(23, 21, 'Phương Pháp Giáo Dục Trẻ Các Nước', '2026-03-07 15:56:38', '2026-03-07 15:56:38'),
(24, 21, 'Phát Triển Trí Tuệ Cho Trẻ', '2026-03-07 15:56:54', '2026-03-07 15:56:54'),
(25, 21, 'Phát Triển Kỹ Năng Cho Trẻ', '2026-03-07 15:57:12', '2026-03-07 15:57:12'),
(26, NULL, 'Sách thiếu nhi', '2026-03-07 15:57:31', '2026-03-07 15:57:31'),
(27, 26, 'Manga - Comic', '2026-03-07 15:57:46', '2026-03-07 15:57:46'),
(28, 26, 'Kiến Thức Bách Khoa', '2026-03-07 15:58:57', '2026-03-07 15:58:57'),
(29, 26, 'Sách Tranh Kỹ Năng Sống Cho Trẻ', '2026-03-07 15:59:11', '2026-03-07 15:59:11'),
(30, 26, 'Vừa Học - Vừa Chơi Với Trẻ', '2026-03-07 15:59:30', '2026-03-07 15:59:30'),
(31, 26, 'Từ Điển Thiếu Nhi', '2026-03-07 16:00:01', '2026-03-07 16:00:01'),
(32, 26, 'Tô Màu, Luyện Chữ', '2026-03-07 16:00:14', '2026-03-07 16:00:14'),
(33, NULL, 'Tiểu sử - Hồi ký', '2026-03-07 16:00:27', '2026-03-07 16:00:27'),
(34, 33, 'Câu Chuyện Cuộc Đời', '2026-03-07 16:01:17', '2026-03-07 16:01:17'),
(35, 33, 'Lịch Sử', '2026-03-07 16:01:23', '2026-03-07 16:01:23'),
(36, 33, 'Kinh Tế', '2026-03-07 16:01:30', '2026-03-07 16:01:30'),
(37, 33, 'Chính Trị', '2026-03-07 16:01:35', '2026-03-07 16:01:35'),
(38, 33, 'Nghệ Thuật - Giải Trí', '2026-03-07 16:01:41', '2026-03-07 16:01:41'),
(39, NULL, 'FICTION', '2026-03-07 16:02:29', '2026-03-07 16:02:29'),
(40, 39, 'Romance', '2026-03-07 16:02:42', '2026-03-07 16:02:42'),
(41, 39, 'Fantasy', '2026-03-07 16:02:50', '2026-03-07 16:02:50'),
(42, 39, 'Graphic Novels, Anime & Manga', '2026-03-07 16:02:57', '2026-03-07 16:02:57'),
(43, 39, 'Thrillers', '2026-03-07 16:03:01', '2026-03-07 16:03:01'),
(44, NULL, 'Business & Management', '2026-03-07 16:03:23', '2026-03-07 16:03:23'),
(45, 44, 'Entrepreneurship', '2026-03-07 16:03:35', '2026-03-07 16:03:35'),
(46, 44, 'Sales & Marketing', '2026-03-07 16:03:40', '2026-03-07 16:03:40'),
(47, NULL, 'Personal Development', '2026-03-07 16:03:59', '2026-03-07 16:03:59'),
(48, 47, 'Popular Psychology', '2026-03-07 16:04:44', '2026-03-07 16:04:44'),
(49, 47, 'Assertiveness, Motivation & Self-esteem', '2026-03-07 16:04:51', '2026-03-07 16:04:51'),
(50, 47, 'Memory Improvement & Thinking Techniques', '2026-03-07 16:04:59', '2026-03-07 16:04:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ho_ten` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `so_dien_thoai` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia_chi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `trang_thai` tinyint NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_role_id_foreign` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ho_ten`, `email`, `email_verified_at`, `password`, `so_dien_thoai`, `dia_chi`, `avatar`, `role_id`, `trang_thai`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Đỗ Duy Thiên', '33doduythien@gmail.com', '2026-03-07 15:44:33', '$2y$10$SeTh0vKn5g9PvYoxWf6OAenVMBYcDW9r/dhqM8xzGT4hZkxgtZJPe', '0942741334', NULL, NULL, 1, 1, NULL, '2026-03-07 15:44:15', '2026-03-07 15:44:33');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD CONSTRAINT `danh_gia_sach_id_foreign` FOREIGN KEY (`sach_id`) REFERENCES `sach` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `danh_gia_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ma_giam_gia_id_foreign` FOREIGN KEY (`ma_giam_gia_id`) REFERENCES `ma_giam_gia` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `don_hang_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `don_hang_chi_tiet`
--
ALTER TABLE `don_hang_chi_tiet`
  ADD CONSTRAINT `don_hang_chi_tiet_don_hang_id_foreign` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `don_hang_chi_tiet_sach_id_foreign` FOREIGN KEY (`sach_id`) REFERENCES `sach` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD CONSTRAINT `email_verifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `gio_hang_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gio_hang_chi_tiet`
--
ALTER TABLE `gio_hang_chi_tiet`
  ADD CONSTRAINT `gio_hang_chi_tiet_gio_hang_id_foreign` FOREIGN KEY (`gio_hang_id`) REFERENCES `gio_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gio_hang_chi_tiet_sach_id_foreign` FOREIGN KEY (`sach_id`) REFERENCES `sach` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sach`
--
ALTER TABLE `sach`
  ADD CONSTRAINT `sach_nha_cung_cap_id_foreign` FOREIGN KEY (`nha_cung_cap_id`) REFERENCES `nha_cung_cap` (`id`),
  ADD CONSTRAINT `sach_nha_xuat_ban_id_foreign` FOREIGN KEY (`nha_xuat_ban_id`) REFERENCES `nha_xuat_ban` (`id`),
  ADD CONSTRAINT `sach_tac_gia_id_foreign` FOREIGN KEY (`tac_gia_id`) REFERENCES `tac_gia` (`id`),
  ADD CONSTRAINT `sach_the_loai_id_foreign` FOREIGN KEY (`the_loai_id`) REFERENCES `the_loai` (`id`);

--
-- Constraints for table `the_loai`
--
ALTER TABLE `the_loai`
  ADD CONSTRAINT `the_loai_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `the_loai` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
