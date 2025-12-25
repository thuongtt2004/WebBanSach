-- Thêm cột order_id vào bảng messages để liên kết tin nhắn với đơn hàng
ALTER TABLE `messages` 
ADD COLUMN `order_id` INT NULL COMMENT 'ID đơn hàng liên quan' AFTER `message`,
ADD COLUMN `message_type` ENUM('text', 'order') DEFAULT 'text' COMMENT 'Loại tin nhắn' AFTER `order_id`,
ADD INDEX `idx_order_id` (`order_id`);
