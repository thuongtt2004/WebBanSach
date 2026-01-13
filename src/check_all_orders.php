<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== TẤT CẢ ĐƠN HÀNG HIỆN TẠI ===\n\n";

$result = $conn->query("SELECT order_id, order_status, payment_method, created_at, 
                        TIMESTAMPDIFF(HOUR, created_at, NOW()) as hours
                        FROM orders 
                        WHERE order_id IN (16, 17, 18)
                        ORDER BY order_id DESC");

while ($order = $result->fetch_assoc()) {
    echo "Đơn #{$order['order_id']}: {$order['order_status']} | {$order['payment_method']} | " . floor($order['hours'] / 24) . " ngày\n";
}

echo "\n=== KIỂM TRA ĐƠN CHỜ THANH TOÁN ===\n";
$wait = $conn->query("SELECT order_id, order_status, created_at FROM orders WHERE order_status = 'Chờ thanh toán'");

if ($wait->num_rows > 0) {
    echo "Các đơn 'Chờ thanh toán':\n";
    while ($w = $wait->fetch_assoc()) {
        echo "  - Đơn #{$w['order_id']}: {$w['created_at']}\n";
    }
} else {
    echo "Không có đơn 'Chờ thanh toán' nào\n";
}
