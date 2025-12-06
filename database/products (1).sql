-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 28, 2025 lúc 03:35 PM
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
('SP001', 1, 'Đắc Nhân Tâm', 'Dale Carnegie', 'NXB Tổng Hợp TPHCM', 2020, '9786041096080', 320, 'Tiếng Việt', 'Bìa mềm', '14.5 x 20.5 cm', 350, NULL, 86000.00, 'Cuốn sách kinh điển về nghệ thuật đối nhân xử thế và giao tiếp. Dale Carnegie đã tổng hợp những nguyên tắc cơ bản giúp bạn trở nên thân thiện hơn, thuyết phục người khác theo cách của bạn và chiến thắng trong giao tiếp. Đây là cuốn sách bán chạy nhất mọi thời đại, đã giúp hàng triệu người thay đổi cuộc sống.', 'uploads/dac_nhan_tam.jpg', 150, 230),
('SP002', 1, 'Nhà Giả Kim', 'Paulo Coelho', 'NXB Hội Nhà Văn', 2021, '9786041046733', 227, 'Tiếng Việt', 'Bìa mềm', '13 x 20 cm', 280, NULL, 79000.00, 'Câu chuyện về chuyến hành trình đi tìm kho báu của Santiago - cậu bé chăn cừu Tây Ban Nha. Qua hành trình ấy, chúng ta được sống cùng Santiago những trải nghiệm quý giá, học cách lắng nghe trái tim mình, hiểu được ý nghĩa đích thực của hạnh phúc và theo đuổi ước mơ của chính mình.', 'uploads/nha_gia_kim.jpg', 200, 450),
('SP003', 1, 'Cây Cam Ngọt Của Tôi', 'José Mauro de Vasconcelos', 'NXB Hội Nhà Văn', 2020, '9786041046719', 244, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 320, NULL, 108000.00, 'Một tác phẩm đầy cảm động về tuổi thơ nghèo khó nhưng giàu tình yêu thương. Zezé - cậu bé năm tuổi tinh nghịch, thông minh và nhạy cảm đã trải qua những ngày tháng khó khăn nhất trong cuộc đời mình. Cuốn sách là lời nhắn nhủ sâu sắc về ý nghĩa của tình yêu thương và sự hy sinh.', 'uploads/cay_cam_ngot.jpg', 180, 380),
('SP004', 1, 'Café Sáng Với Tony', 'Tony Buổi Sáng', 'NXB Trẻ', 2019, '9786041109629', 280, 'Tiếng Việt', 'Bìa mềm', '13 x 19 cm', 300, 'Café', 95000.00, 'Tuyển tập những bài viết truyền cảm hứng từ Tony Buổi Sáng. Từng trang sách như những tách café đậm đà, đánh thức những suy nghĩ tích cực về cuộc sống, công việc và hạnh phúc. Một cuốn sách nhẹ nhàng nhưng đầy ý nghĩa cho những ai đang tìm kiếm động lực.', 'uploads/cafe_sang.jpg', 120, 190),
('SP005', 1, 'Tuổi Trẻ Đáng Giá Bao Nhiêu', 'Rosie Nguyễn', 'NXB Hội Nhà Văn', 2019, '9786041109735', 296, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 340, NULL, 89000.00, 'Cuốn sách dành cho tuổi trẻ đang loay hoay tìm kiếm ý nghĩa cuộc sống. Rosie Nguyễn chia sẻ những trải nghiệm thực tế, những bài học quý giá về việc sống có ý nghĩa, làm việc hiệu quả và yêu thương bản thân. Đây là kim chỉ nam cho thế hệ trẻ trong hành trình trưởng thành.', 'uploads/tuoi_tre_dang_gia.jpg', 160, 320),
('SP006', 2, 'Từ Tốt Đến Vĩ Đại', 'Jim Collins', 'NXB Trẻ', 2020, '9786041109957', 432, 'Tiếng Việt', 'Bìa mềm', '15.5 x 23 cm', 580, NULL, 169000.00, 'Jim Collins nghiên cứu những công ty có bước nhảy vọt và bền vững để trả lời câu hỏi: Điều gì làm nên sự khác biệt giữa công ty tốt và công ty vĩ đại? Cuốn sách đưa ra những phát hiện đi ngược lại với nhiều quan niệm trước đây về quản trị, lãnh đạo và chiến lược kinh doanh.', 'uploads/tu_tot_den_vi_dai.jpg', 100, 180),
('SP007', 2, 'Nghĩ Giàu Và Làm Giàu', 'Napoleon Hill', 'NXB Tổng Hợp TPHCM', 2019, '9786041096097', 368, 'Tiếng Việt', 'Bìa mềm', '14.5 x 20.5 cm', 420, NULL, 120000.00, 'Tác phẩm bất hủ về triết lý làm giàu của Napoleon Hill. Cuốn sách tổng hợp 13 nguyên tắc thành công từ việc nghiên cứu 500 triệu phú nổi tiếng. Đây không chỉ là cuốn sách về tiền bạc mà còn là kim chỉ nam về tư duy, thái độ và hành động để đạt được thành công.', 'uploads/nghi_giau_lam_giau.jpg', 90, 150),
('SP008', 2, 'Đời Ngắn Đừng Ngủ Dài', 'Robin Sharma', 'NXB Trẻ', 2021, '9786041110281', 256, 'Tiếng Việt', 'Bìa mềm', '13 x 20 cm', 290, NULL, 98000.00, 'Robin Sharma chia sẻ những chiến lược đơn giản nhưng mạnh mẽ để thay đổi cuộc sống. Cuốn sách hướng dẫn cách tối ưu hóa thời gian, năng lượng và tiềm năng của bạn để đạt được thành công vượt bậc. Mỗi chương là một bài học quý giá về năng suất và hiệu quả.', 'uploads/doi_ngan_dung_ngu_dai.jpg', 130, 210),
('SP009', 2, 'Bí Mật Tư Duy Triệu Phú', 'T. Harv Eker', 'NXB Tổng Hợp TPHCM', 2020, '9786041096103', 312, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 380, NULL, 135000.00, 'T. Harv Eker tiết lộ những nguyên tắc về \"bản đồ tài chính\" - hệ thống niềm tin chi phối mối quan hệ của chúng ta với tiền bạc. Cuốn sách giúp bạn nhận diện và thay đổi những rào cản tâm lý để đạt được tự do tài chính. Đây là cuốn sách thay đổi mindset về tiền bạc.', 'uploads/bi_mat_tu_duy_trieu_phu.jpg', 110, 165),
('SP010', 2, 'Chiến Lược Đại Dương Xanh', 'W. Chan Kim, Renée Mauborgne', 'NXB Trẻ', 2021, '9786041110298', 348, 'Tiếng Việt', 'Bìa cứng', '16 x 24 cm', 620, NULL, 189000.00, 'W. Chan Kim và Renée Mauborgne giới thiệu khái niệm \"đại dương xanh\" - không gian thị trường mới chưa được khai phá. Thay vì cạnh tranh khốc liệt trong \"đại dương đỏ\", các doanh nghiệp nên tạo ra giá trị độc đáo. Cuốn sách đi kèm hàng trăm ví dụ thực tế từ các công ty thành công.', 'uploads/chien_luoc_dai_duong_xanh.jpg', 85, 120),
('SP011', 3, 'Dế Mèn Phiêu Lưu Ký', 'Tô Hoài', 'NXB Kim Đồng', 2020, '9786042097451', 196, 'Tiếng Việt', 'Bìa mềm', '14 x 20 cm', 250, NULL, 65000.00, 'Tác phẩm kinh điển của văn học thiếu nhi Việt Nam. Câu chuyện về chú dế mèn dũng cảm, ham học hỏi và luôn sẵn sàng giúp đỡ bạn bè. Qua những cuộc phiêu lưu, Dế Mèn học được nhiều bài học quý giá về tình bạn, lòng dũng cảm và sự khôn ngoan.', 'uploads/de_men_phieu_luu_ky.jpg', 200, 420),
('SP012', 3, 'Hoàng Tử Bé', 'Antoine de Saint-Exupéry', 'NXB Hội Nhà Văn', 2019, '9786041046740', 128, 'Tiếng Việt', 'Bìa mềm', '13 x 19 cm', 180, NULL, 72000.00, 'Câu chuyện cảm động về hoàng tử bé từ hành tinh nhỏ B612. Cuốn sách là bài học sâu sắc về tình yêu, trách nhiệm và ý nghĩa cuộc sống qua con mắt trong sáng của một đứa trẻ. Một tác phẩm dành cho mọi lứa tuổi, mỗi lần đọc lại là một lần khám phá mới.', 'uploads/hoang_tu_be.jpg', 180, 380),
('SP013', 3, 'Đảo Giấu Vàng', 'Robert Louis Stevenson', 'NXB Kim Đồng', 2020, '9786042097468', 288, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 340, NULL, 89000.00, 'Cuộc phiêu lưu ly kỳ đi tìm kho báu trên đảo hoang. Jim Hawkins - cậu bé dũng cảm cùng thuyền trưởng và thủy thủ đoàn vượt qua ngàn trùng hiểm nguy. Tác phẩm kinh điển về lòng can đảm, sự trung thành và tinh thần phiêu lưu mạo hiểm.', 'uploads/dao_giau_vang.jpg', 140, 280),
('SP014', 3, 'Harry Potter Và Hòn Đá Phù Thủy', 'J.K. Rowling', 'NXB Trẻ', 2021, '9786041110304', 368, 'Tiếng Việt', 'Bìa cứng', '15 x 23 cm', 540, 'Harry Potter', 195000.00, 'Khởi đầu hành trình kỳ diệu của cậu bé Harry Potter tại trường phù thủy Hogwarts. Một thế giới ma thuật đầy màu sắc với những cuộc phiêu lưu gay cấn, tình bạn chân thành và sự đối đầu giữa thiện và ác. Đây là hiện tượng văn học thế giới, cuốn sách mở đầu cho series huyền thoại.', 'uploads/harry_potter_1.jpg', 150, 520),
('SP015', 3, 'Những Cuộc Phiêu Lưu Của Tom Sawyer', 'Mark Twain', 'NXB Kim Đồng', 2019, '9786042097475', 312, 'Tiếng Việt', 'Bìa mềm', '14.5 x 20.5 cm', 390, NULL, 98000.00, 'Câu chuyện về cậu bé nghịch ngợm nhưng thông minh Tom Sawyer sống ở miền nam nước Mỹ thế kỷ 19. Những trò nghịch ngợm, cuộc phiêu lưu đầy màu sắc và tình bạn trong sáng. Mark Twain đã tạo nên tác phẩm bất hủ về tuổi thơ đầy ắp niềm vui và sự tò mò.', 'uploads/tom_sawyer.jpg', 120, 240),
('SP016', 4, 'One Piece - Tập 1', 'Eiichiro Oda', 'NXB Kim Đồng', 2020, '9786042097482', 208, 'Tiếng Việt', 'Bìa mềm', '11.5 x 17.5 cm', 180, 'One Piece', 25000.00, 'Khởi đầu hành trình của Luffy và băng hải tặc Mũ Rơm. Cuộc phiêu lưu vĩ đại để tìm kho báu One Piece và trở thành Vua Hải Tặc. Với câu chuyện hấp dẫn, nhân vật đa dạng và thông điệp về tình bạn, ước mơ, One Piece đã trở thành manga huyền thoại với hơn 500 triệu bản in trên toàn thế giới.', 'uploads/one_piece_1.jpg', 300, 890),
('SP017', 4, 'Naruto - Tập 1', 'Masashi Kishimoto', 'NXB Kim Đồng', 2019, '9786042097499', 192, 'Tiếng Việt', 'Bìa mềm', '11.5 x 17.5 cm', 170, 'Naruto', 25000.00, 'Câu chuyện về Uzumaki Naruto - cậu bé ninja mồ côi mang trong mình con cáo chín đuôi, ước mơ trở thành Hokage. Qua những thử thách, Naruto dần trưởng thành và chinh phục trái tim mọi người. Một tác phẩm về lòng kiên trì, tình bạn và sự hy sinh.', 'uploads/naruto_1.jpg', 280, 850),
('SP018', 4, 'Conan - Thám Tử Lừng Danh - Tập 1', 'Gosho Aoyama', 'NXB Kim Đồng', 2020, '9786042097505', 180, 'Tiếng Việt', 'Bìa mềm', '11.5 x 17.5 cm', 165, 'Detective Conan', 25000.00, 'Kudo Shinichi bị teo nhỏ thành cậu bé Conan sau khi bị ép uống thuốc độc. Với trí tuệ phi thường, Conan giải quyết những vụ án bí ẩn trong khi tìm cách quay về hình dạng ban đầu. Series trinh thám kinh điển với hơn 100 tập, mỗi vụ án là một câu đố hấp dẫn.', 'uploads/conan_1.jpg', 320, 920),
('SP019', 4, 'Dragon Ball - Tập 1', 'Akira Toriyama', 'NXB Kim Đồng', 2021, '9786042097512', 196, 'Tiếng Việt', 'Bìa mềm', '11.5 x 17.5 cm', 175, 'Dragon Ball', 25000.00, 'Cuộc phiêu lưu tìm kiếm bảy viên ngọc rồng của Son Goku. Từ cậu bé hoang dã đến chiến binh mạnh nhất vũ trụ, Dragon Ball là huyền thoại manga với tầm ảnh hưởng toàn cầu. Akira Toriyama đã tạo nên thế giới đầy màu sắc với những trận chiến hoành tráng và thông điệp về lòng dũng cảm.', 'uploads/dragon_ball_1.jpg', 260, 780),
('SP020', 4, 'Doraemon - Tập 1', 'Fujiko F. Fujio', 'NXB Kim Đồng', 2020, '9786042097529', 196, 'Tiếng Việt', 'Bìa mềm', '11.5 x 17.5 cm', 160, 'Doraemon', 20000.00, 'Chú mèo máy đến từ tương lai cùng những bảo bối thần kỳ giúp đỡ Nobita. Doraemon mang đến những câu chuyện ấm áp, hài hước về tình bạn, gia đình và những bài học cuộc sống. Đây là manga kinh điển cho mọi lứa tuổi, đã đồng hành với nhiều thế hệ độc giả Việt Nam.', 'uploads/doraemon_1.jpg', 350, 1200),
('SP021', 1, '1984', 'George Orwell', 'NXB Hội Nhà Văn', 2020, '9786041046757', 396, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 450, NULL, 125000.00, 'Tác phẩm phản địa đàng kinh điển về một xã hội toàn trị, nơi Big Brother giám sát mọi hành động và suy nghĩ. Winston Smith nổi loạn chống lại hệ thống nhưng phải đối mặt với hậu quả khủng khiếp. George Orwell đã tạo nên một tác phẩm cảnh báo sâu sắc về quyền lực và tự do.', 'uploads/1984.jpg', 95, 145),
('SP022', 2, 'Sapiens: Lược Sử Loài Người', 'Yuval Noah Harari', 'NXB Trẻ', 2021, '9786041110311', 544, 'Tiếng Việt', 'Bìa cứng', '15.5 x 23 cm', 720, NULL, 189000.00, 'Yuval Noah Harari dẫn dắt chúng ta qua 70.000 năm lịch sử loài người từ khi là loài vượn không đáng kể cho đến khi thống trị hành tinh. Cuốn sách đặt ra những câu hỏi lớn về bản chất con người, xã hội và tương lai. Một tác phẩm làm thay đổi cách nhìn về chính mình và thế giới.', 'uploads/sapiens.jpg', 120, 280),
('SP023', 1, 'Người Đưa Thư Của Nỗi Buồn', 'Gabriel García Márquez', 'NXB Hội Nhà Văn', 2019, '9786041046764', 284, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 360, NULL, 145000.00, 'Gabriel García Márquez kể về thị trấn nhỏ bị cô lập bởi bão tuyết kéo dài. Câu chuyện huyền bí và buồn bã về tình yêu, cô độc và sự tuyệt vọng. Văn phong ma thuật hiện thực đặc trưng đã tạo nên một tác phẩm nghệ thuật đích thực từ bậc thầy Nobel văn chương.', 'uploads/nguoi_dua_thu.jpg', 80, 110),
('SP024', 1, 'Rừng Na Uy', 'Haruki Murakami', 'NXB Hội Nhà Văn', 2020, '9786041046771', 468, 'Tiếng Việt', 'Bìa mềm', '14 x 20.5 cm', 520, NULL, 165000.00, 'Haruki Murakami kể về Watanabe - chàng sinh viên Tokyo và câu chuyện tình yêu đầy ám ảnh với Naoko - cô gái mang theo nỗi buồn sâu thẳm. Một tác phẩm về tuổi trẻ, sự mất mát và tìm kiếm bản thân trong thế giới hiện đại. Rừng Na Uy đã trở thành biểu tượng văn học Nhật Bản đương đại.', 'uploads/rung_na_uy.jpg', 110, 240),
('SP025', 1, 'Những Tấm Lòng Cao Cả', 'Victor Hugo', 'NXB Văn Học', 2021, '9786041046788', 672, 'Tiếng Việt', 'Bìa cứng', '15.5 x 23 cm', 850, NULL, 198000.00, 'Victor Hugo tái hiện Paris thế kỷ 15 qua câu chuyện của Quasimodo - người gù nhà thờ Đức Bà, cô gái xinh đẹp Esmeralda và linh mục Frollo. Một thiên sử thi về tình yêu, lòng nhân ái và sự phán xét của xã hội. Tác phẩm bất hủ về cái đẹp tâm hồn vượt lên ngoại hình.', 'uploads/nha_tho_duc_ba.jpg', 75, 125),
('SP026', 1, 'Số Đỏ', 'Vũ Trọng Phụng', 'NXB Văn học', 2023, '978-604-56-7890-1', 280, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 320, NULL, 89000.00, 'Tác phẩm kinh điển của Vũ Trọng Phụng, một bức tranh sống động về xã hội Hà Nội những năm 1930 với nhân vật Xuân Tóc Đỏ nổi tiếng. Tác phẩm châm biếm sắc sảo về thói đời, về những kẻ cơ hội, về xã hội suy đồi.', 'uploads/so_do.jpg', 150, 45),
('SP027', 1, 'Lão Hạc', 'Nam Cao', 'NXB Kim Đồng', 2023, '978-604-56-7891-2', 180, 'Tiếng Việt', 'Bìa mềm', '19 x 13 cm', 200, NULL, 65000.00, 'Truyện ngắn nổi tiếng của Nam Cao kể về cuộc đời bi thảm của ông lão nghèo khó Lão Hạc và con chó của ông. Một tác phẩm đầy nhân văn, phản ánh hiện thực đau thương của nông dân Việt Nam trước cách mạng.', 'uploads/lao_hac.jpg', 200, 67),
('SP028', 1, 'Chí Phèo', 'Nam Cao', 'NXB Văn học', 2023, '978-604-56-7892-3', 150, 'Tiếng Việt', 'Bìa mềm', '19 x 13 cm', 180, NULL, 59000.00, 'Truyện ngắn xuất sắc của Nam Cao về nhân vật Chí Phèo - một người nông dân bị xã hội đẩy đưa vào con đường sa đọa. Tác phẩm phê phán sâu sắc chế độ xã hội cũ và thể hiện khả năng miêu tả tâm lý nhân vật tuyệt vời.', 'uploads/chi_pheo.jpg', 180, 52),
('SP029', 1, 'Vợ Nhặt', 'Kim Lân', 'NXB Kim Đồng', 2023, '978-604-56-7893-4', 120, 'Tiếng Việt', 'Bìa mềm', '19 x 13 cm', 150, NULL, 55000.00, 'Truyện ngắn của Kim Lân viết về tình yêu thương chân thành giữa người với người qua hình ảnh người đàn ông nhặt được người vợ trong nạn đói 1945. Một tác phẩm đầy tính nhân văn và cảm động.', 'uploads/vo_nhat.jpg', 160, 48),
('SP030', 1, 'Tắt Đèn', 'Ngô Tất Tố', 'NXB Văn học', 2023, '978-604-56-7894-5', 320, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 350, NULL, 95000.00, 'Tiểu thuyết của Ngô Tất Tố phản ánh cuộc sống khốn khổ của nông dân Việt Nam đầu thế kỷ XX. Qua gia đình cô Dậu - chị Dậu, tác phẩm khắc họa sinh động bi kịch của người nông dân bị bóc lột.', 'uploads/tat_den.jpg', 140, 38),
('SP031', 1, 'Chiến Tranh Và Hòa Bình', 'Lev Tolstoy', 'NXB Văn học', 2023, '978-604-56-7895-6', 1200, 'Tiếng Việt', 'Bìa cứng', '23 x 16 cm', 1400, NULL, 450000.00, 'Kiệt tác của Lev Tolstoy, một trong những tiểu thuyết vĩ đại nhất mọi thời đại. Tác phẩm mô tả cuộc xâm lược nước Nga của Napoleon qua số phận các gia đình quý tộc, đan xen triết lý lịch sử sâu sắc.', 'uploads/chien_tranh_hoa_binh.jpg', 80, 15),
('SP032', 1, 'Tội Ác Và Trừng Phạt', 'Fyodor Dostoevsky', 'NXB Văn học', 2023, '978-604-56-7896-7', 680, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 720, NULL, 198000.00, 'Kiệt tác tâm lý của Dostoevsky kể về sinh viên nghèo Raskolnikov giết người cầm đồ và cuộc đấu tranh nội tâm đau khổ sau đó. Một tác phẩm triết học văn học sâu sắc về tội lỗi và sự cứu rỗi.', 'uploads/toi_ac_trung_phat.jpg', 100, 28),
('SP033', 1, 'Cha Già Goriot', 'Honoré de Balzac', 'NXB Văn học', 2023, '978-604-56-7897-8', 420, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 450, NULL, 135000.00, 'Tác phẩm nổi tiếng của Balzac thuộc bộ truyện Hài kịch nhân sinh. Câu chuyện về người cha già hy sinh tất cả cho hai cô con gái vô ơn, phản ánh xã hội Paris thế kỷ 19 với chủ nghĩa vật chất thống trị.', 'uploads/cha_gia_goriot.jpg', 110, 32),
('SP034', 1, 'Những Người Khốn Khổ', 'Victor Hugo', 'NXB Văn học', 2023, '978-604-56-7898-9', 1100, 'Tiếng Việt', 'Bìa cứng', '23 x 16 cm', 1300, NULL, 380000.00, 'Kiệt tác của Victor Hugo về Jean Valjean - từ tù nhân trở thành người cao thượng. Tác phẩm nhân văn vĩ đại phê phán xã hội bất công, ca ngợi lòng nhân ái và sự cứu rỗi.', 'uploads/nguoi_khon_kho.jpg', 85, 22),
('SP035', 1, 'Người Tình', 'Marguerite Duras', 'NXB Hội Nhà Văn', 2023, '978-604-56-7899-0', 180, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 220, NULL, 98000.00, 'Tiểu thuyết tự truyện của Marguerite Duras kể về mối tình đầu của cô gái Pháp 15 tuổi với người tình Trung Quốc giàu có ở Sài Gòn thập niên 1930. Văn xuôi tinh tế, cảm xúc sâu lắng.', 'uploads/nguoi_tinh.jpg', 130, 45),
('SP036', 2, 'Nghệ Thuật Bán Hàng Vĩ Đại Nhất Thế Giới', 'Og Mandino', 'NXB Lao động', 2023, '978-604-56-7900-3', 280, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 300, NULL, 119000.00, 'Cuốn sách kinh điển của Og Mandino về nghệ thuật bán hàng thông qua câu chuyện Hafid - từ cậu bé chăn lạc đà trở thành thương gia giàu có nhất. 10 cuộn giấy da với những bí quyết thành công vượt thời gian.', 'uploads/nghe_thuat_ban_hang.jpg', 180, 68),
('SP037', 2, 'Khởi Nghiệp Tinh Gọn', 'Eric Ries', 'NXB Trẻ', 2023, '978-604-56-7901-4', 320, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 340, NULL, 145000.00, 'Sách hướng dẫn phương pháp khởi nghiệp hiện đại của Eric Ries - xây dựng sản phẩm nhanh, thử nghiệm và điều chỉnh liên tục. Một cuốn sách bắt buộc cho mọi startup và doanh nghiệp muốn đổi mới.', 'uploads/khoi_nghiep_tinh_gon.jpg', 150, 52),
('SP038', 2, 'Đắc Nhân Tâm Trong Kinh Doanh', 'Dale Carnegie & Associates', 'NXB Tổng hợp TP.HCM', 2023, '978-604-56-7902-5', 350, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 370, NULL, 129000.00, 'Ứng dụng các nguyên tắc trong Đắc Nhân Tâm vào thế giới kinh doanh. Hướng dẫn cách xây dựng mối quan hệ, thuyết phục khách hàng và phát triển doanh nghiệp bền vững.', 'uploads/dac_nhan_tam_kinh_doanh.jpg', 160, 58),
('SP039', 2, 'Tư Duy Nhanh Và Chậm', 'Daniel Kahneman', 'NXB Thế Giới', 2023, '978-604-56-7903-6', 580, 'Tiếng Việt', 'Bìa mềm', '22 x 15 cm', 650, NULL, 189000.00, 'Tác phẩm của nhà tâm lý học đoạt giải Nobel Daniel Kahneman về hai hệ thống tư duy của con người. Giải thích cách chúng ta đưa ra quyết định và những thiên kiến nhận thức.', 'uploads/tu_duy_nhanh_cham.jpg', 140, 42),
('SP040', 2, 'Chơi Lớn Hay Về Nhà', 'Pamela Anderson', 'NXB Lao động', 2023, '978-604-56-7904-7', 240, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 280, NULL, 99000.00, 'Sách của Pamela Anderson về cách xây dựng tư duy sư tử, dám nghĩ lớn và hành động táo bạo trong kinh doanh. Khích lệ người đọc thoát khỏi vùng an toàn để đạt thành công lớn.', 'uploads/choi_lon_ve_nha.jpg', 170, 62),
('SP041', 3, 'Làm Bạn Với Bầu Trời', 'Nguyễn Nhật Ánh', 'NXB Trẻ', 2023, '978-604-56-7905-8', 380, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 400, NULL, 119000.00, 'Truyện thiếu nhi của Nguyễn Nhật Ánh về tuổi thơ miền quê với những câu chuyện đầy cảm xúc. Tình bạn, tình thân, nỗi buồn và niềm vui của trẻ thơ được tái hiện sinh động.', 'uploads/lam_ban_voi_bau_troi.jpg', 200, 89),
('SP042', 3, 'Mắt Biếc', 'Nguyễn Nhật Ánh', 'NXB Trẻ', 2023, '978-604-56-7906-9', 420, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 440, NULL, 135000.00, 'Truyện dài của Nguyễn Nhật Ánh về tình yêu thầm lặng của Ngạn dành cho Hà Lan từ thuở nhỏ. Một câu chuyện tình đẹp, buồn và đầy cảm xúc về tuổi trẻ.', 'uploads/mat_biec.jpg', 180, 95),
('SP043', 3, 'Đồi Gió Hú', 'Emily Brontë', 'NXB Kim Đồng', 2023, '978-604-56-7907-0', 380, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 400, NULL, 115000.00, 'Tiểu thuyết kinh điển của Emily Brontë về tình yêu mãnh liệt và bi thương giữa Heathcliff và Catherine. Phiên bản thiếu nhi được chuyển thể phù hợp.', 'uploads/doi_gio_hu.jpg', 120, 35),
('SP044', 3, 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 'Nguyễn Nhật Ánh', 'NXB Trẻ', 2023, '978-604-56-7908-1', 400, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 420, NULL, 125000.00, 'Tác phẩm nổi tiếng của Nguyễn Nhật Ánh về tuổi thơ ở miền quê với những ký ức đẹp đẽ, chân thật. Câu chuyện về tình anh em, tình làng nghĩa xóm đầy xúc động.', 'uploads/hoa_vang_co_xanh.jpg', 190, 105),
('SP045', 3, 'Sherlock Holmes - Thám Tử Lừng Danh', 'Arthur Conan Doyle', 'NXB Kim Đồng', 2023, '978-604-56-7909-2', 480, 'Tiếng Việt', 'Bìa mềm', '20.5 x 14.5 cm', 500, NULL, 145000.00, 'Tuyển tập truyện trinh thám nổi tiếng của Arthur Conan Doyle về thám tử thiên tài Sherlock Holmes. Phiên bản dành cho thiếu nhi với ngôn ngữ dễ hiểu, hấp dẫn.', 'uploads/sherlock_holmes.jpg', 140, 48),
('SP046', 4, 'Attack On Titan - Tập 1', 'Hajime Isayama', 'NXB Kim Đồng', 2023, '978-604-56-7910-5', 180, 'Tiếng Việt', 'Bìa mềm', '18 x 13 cm', 200, 'Attack On Titan', 35000.00, 'Manga đình đám của Hajime Isayama về thế giới con người bị titan khổng lồ tấn công. Eren Yeager quyết tâm tiêu diệt tất cả titan sau khi chứng kiến mẹ mình bị giết. Cốt truyện hấp dẫn, đầy bất ngờ.', 'uploads/attack_on_titan_1.jpg', 200, 145),
('SP047', 4, 'My Hero Academia - Tập 1', 'Kohei Horikoshi', 'NXB Kim Đồng', 2023, '978-604-56-7911-6', 180, 'Tiếng Việt', 'Bìa mềm', '18 x 13 cm', 200, 'My Hero Academia', 32000.00, 'Manga của Kohei Horikoshi về thế giới 80% dân số có siêu năng lực. Izuku Midoriya sinh ra không có năng lực nhưng vẫn mơ trở thành anh hùng vĩ đại nhất. Câu chuyện truyền cảm hứng mạnh mẽ.', 'uploads/my_hero_academia_1.jpg', 180, 125),
('SP048', 4, 'Demon Slayer - Tập 1', 'Koyoharu Gotouge', 'NXB Kim Đồng', 2023, '978-604-56-7912-7', 180, 'Tiếng Việt', 'Bìa mềm', '18 x 13 cm', 200, 'Demon Slayer', 33000.00, 'Manga của Koyoharu Gotouge về Tanjirou - cậu bé trở thành sát quỷ sau khi gia đình bị giết bởi quỷ. Em gái Nezuko biến thành quỷ nhưng giữ được ý thức. Hành trình tìm cách cứu em và báo thù bắt đầu.', 'uploads/demon_slayer_1.jpg', 190, 138),
('SP049', 4, 'Tokyo Ghoul - Tập 1', 'Sui Ishida', 'NXB Kim Đồng', 2023, '978-604-56-7913-8', 180, 'Tiếng Việt', 'Bìa mềm', '18 x 13 cm', 200, 'Tokyo Ghoul', 34000.00, 'Manga kinh dị của Sui Ishida về Ken Kaneki - sinh viên biến thành nửa người nửa ghoul sau tai nạn. Phải sống trong thế giới ngầm đầy nguy hiểm, đấu tranh giữa hai bản tính.', 'uploads/tokyo_ghoul_1.jpg', 160, 98),
('SP050', 4, 'Fullmetal Alchemist - Tập 1', 'Hiromu Arakawa', 'NXB Kim Đồng', 2023, '978-604-56-7914-9', 180, 'Tiếng Việt', 'Bìa mềm', '18 x 13 cm', 200, 'Fullmetal Alchemist', 35000.00, 'Manga của Hiromu Arakawa về hai anh em nhà Elric - Edward và Alphonse. Sau khi thất bại trong thuật luyện kim để hồi sinh mẹ, họ bắt đầu hành trình tìm Đá Hiền Giả để lấy lại cơ thể. Cốt truyện sâu sắc, cảm động.', 'uploads/fullmetal_alchemist_1.jpg', 170, 112);

--
-- Chỉ mục cho các bảng đã đổ
--

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
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
