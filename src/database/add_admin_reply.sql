-- Thêm cột admin_reply và admin_reply_date vào bảng reviews
ALTER TABLE `reviews` 
ADD COLUMN `admin_reply` TEXT NULL COMMENT 'Phản hồi của admin' AFTER `images`,
ADD COLUMN `admin_reply_date` DATETIME NULL COMMENT 'Ngày phản hồi' AFTER `admin_reply`;
