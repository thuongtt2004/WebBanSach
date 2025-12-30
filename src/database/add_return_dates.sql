-- Thêm các cột ngày tháng cho việc theo dõi trạng thái trả hàng
-- Chạy script này trong phpMyAdmin

-- Thêm cột ngày hoàn tất trả hàng
ALTER TABLE `orders` 
ADD COLUMN `return_completed_date` DATETIME NULL AFTER `return_status`,
ADD COLUMN `return_rejected_date` DATETIME NULL AFTER `return_completed_date`,
ADD COLUMN `return_cancelled_date` DATETIME NULL AFTER `return_rejected_date`;

-- Thêm comment cho các cột
ALTER TABLE `orders` 
MODIFY COLUMN `return_completed_date` DATETIME NULL COMMENT 'Ngày hoàn tất trả hàng và cập nhật kho',
MODIFY COLUMN `return_rejected_date` DATETIME NULL COMMENT 'Ngày từ chối yêu cầu trả hàng',
MODIFY COLUMN `return_cancelled_date` DATETIME NULL COMMENT 'Ngày hủy yêu cầu trả hàng';
