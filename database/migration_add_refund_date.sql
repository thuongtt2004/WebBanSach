-- Migration: Add refund_date column to orders table
-- Date: 2024-12-16
-- Description: Add refund_date column to track when refund is completed

ALTER TABLE `orders` 
ADD COLUMN `refund_date` DATETIME NULL AFTER `return_status`;

-- Update comment
ALTER TABLE `orders` 
COMMENT = 'Orders table with return/refund support';
