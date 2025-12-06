-- Thêm mã khuyến mãi test để kiểm tra chức năng
-- Mã giảm giá coupon
INSERT INTO `promotions` (`promotion_code`, `promotion_name`, `promotion_type`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount`, `start_date`, `end_date`, `usage_limit`, `used_count`, `status`, `description`) VALUES
('GIAM50K', 'Giảm 50K cho đơn từ 200K', 'coupon', 'fixed_amount', 50000.00, 200000.00, NULL, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 100, 0, 'active', 'Mã giảm 50K cho đơn hàng từ 200K'),
('GIAM10', 'Giảm 10% tối đa 100K', 'coupon', 'percentage', 10.00, 100000.00, 100000.00, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 200, 0, 'active', 'Giảm 10% tối đa 100K cho đơn từ 100K'),
('GIAM20', 'Giảm 20% tối đa 200K', 'coupon', 'percentage', 20.00, 300000.00, 200000.00, '2025-01-01 00:00:00', '2025-12-31 23:59:59', 50, 0, 'active', 'Giảm 20% tối đa 200K cho đơn từ 300K');

-- Flash sale
INSERT INTO `promotions` (`promotion_code`, `promotion_name`, `promotion_type`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount`, `start_date`, `end_date`, `usage_limit`, `used_count`, `status`, `description`) VALUES
('FLASHDEC2025', 'Flash Sale Tháng 12', 'flash_sale', 'percentage', 15.00, 0.00, 150000.00, '2025-12-01 00:00:00', '2025-12-31 23:59:59', NULL, 0, 'active', 'Flash sale giảm 15% tối đa 150K');

-- Minimum order
INSERT INTO `promotions` (`promotion_code`, `promotion_name`, `promotion_type`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount`, `start_date`, `end_date`, `usage_limit`, `used_count`, `status`, `description`) VALUES
('MINDH500K', 'Giảm 100K cho đơn từ 500K', 'minimum_order', 'fixed_amount', 100000.00, 500000.00, NULL, '2025-01-01 00:00:00', '2025-12-31 23:59:59', NULL, 0, 'active', 'Tự động giảm 100K khi đơn hàng từ 500K trở lên');
