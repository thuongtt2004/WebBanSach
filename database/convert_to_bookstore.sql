-- Chuyển đổi database sang web bán sách
-- Thêm các cột đặc thù cho sách vào bảng products

ALTER TABLE `products`
ADD COLUMN `author` VARCHAR(255) NULL COMMENT 'Tác giả' AFTER `product_name`,
ADD COLUMN `publisher` VARCHAR(255) NULL COMMENT 'Nhà xuất bản' AFTER `author`,
ADD COLUMN `publish_year` INT NULL COMMENT 'Năm xuất bản' AFTER `publisher`,
ADD COLUMN `isbn` VARCHAR(20) NULL COMMENT 'Mã ISBN' AFTER `publish_year`,
ADD COLUMN `pages` INT NULL COMMENT 'Số trang' AFTER `isbn`,
ADD COLUMN `language` VARCHAR(50) DEFAULT 'Tiếng Việt' COMMENT 'Ngôn ngữ' AFTER `pages`,
ADD COLUMN `book_format` ENUM('Bìa mềm','Bìa cứng','Ebook') DEFAULT 'Bìa mềm' COMMENT 'Hình thức sách' AFTER `language`,
ADD COLUMN `dimensions` VARCHAR(50) NULL COMMENT 'Kích thước (cm)' AFTER `book_format`,
ADD COLUMN `weight` INT NULL COMMENT 'Trọng lượng (gram)' AFTER `dimensions`,
ADD COLUMN `series` VARCHAR(255) NULL COMMENT 'Bộ sách/Series' AFTER `weight`;

-- Thêm index cho tìm kiếm
ALTER TABLE `products`
ADD INDEX `idx_author` (`author`),
ADD INDEX `idx_publisher` (`publisher`),
ADD INDEX `idx_isbn` (`isbn`),
ADD INDEX `idx_series` (`series`);

-- Cập nhật tên danh mục phù hợp với sách
UPDATE `categories` SET `category_name` = 'Văn học' WHERE `category_name` LIKE '%văn%' OR `category_id` = 1;
UPDATE `categories` SET `category_name` = 'Kinh tế' WHERE `category_name` LIKE '%kinh%' OR `category_id` = 2;
UPDATE `categories` SET `category_name` = 'Tâm lý - Kỹ năng' WHERE `category_name` LIKE '%kỹ năng%' OR `category_id` = 3;

-- Thêm danh mục sách mới (nếu chưa có)
INSERT INTO `categories` (`category_name`, `description`) VALUES
('Tiểu thuyết', 'Sách tiểu thuyết Việt Nam và nước ngoài'),
('Thiếu nhi', 'Sách dành cho trẻ em'),
('Manga - Comic', 'Truyện tranh Nhật Bản và Hàn Quốc'),
('Sách giáo khoa', 'Sách giáo khoa các cấp'),
('Sách ngoại văn', 'Sách tiếng Anh và các ngôn ngữ khác'),
('Self-help', 'Sách phát triển bản thân')
ON DUPLICATE KEY UPDATE `category_name` = VALUES(`category_name`);

-- Kiểm tra cấu trúc bảng
DESCRIBE products;

-- Xem danh mục hiện có
SELECT * FROM categories;
