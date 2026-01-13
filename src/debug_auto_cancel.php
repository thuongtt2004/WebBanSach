<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== DEBUG TỰ ĐỘNG HỦY ĐỚN CHỜ THANH TOÁN ===\n\n";

// Kiểm tra các đơn "Chờ thanh toán"
$check_sql = "SELECT order_id, order_status, payment_method, created_at, 
              TIMESTAMPDIFF(HOUR, created_at, NOW()) as hours_passed
              FROM orders 
              WHERE order_status = 'Chờ thanh toán' 
              AND payment_method = 'bank_transfer'
              ORDER BY created_at DESC";

$result = $conn->query($check_sql);

echo "Đơn hàng 'Chờ thanh toán' (chuyển khoản):\n";
echo "----------------------------------------\n";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Đơn #{$row['order_id']}\n";
        echo "  - Ngày tạo: {$row['created_at']}\n";
        echo "  - Số giờ đã qua: {$row['hours_passed']} giờ\n";
        echo "  - Đủ điều kiện hủy (>=24h): " . ($row['hours_passed'] >= 24 ? "CÓ ✓" : "KHÔNG ✗") . "\n\n";
    }
} else {
    echo "Không có đơn 'Chờ thanh toán' nào\n";
}

// Test query tự động hủy
echo "\n=== TEST QUERY TỰ ĐỘNG HỦY ===\n";
$auto_cancel_sql = "SELECT order_id, created_at, 
                    TIMESTAMPDIFF(HOUR, created_at, NOW()) as hours_diff
                    FROM orders 
                    WHERE order_status = 'Chờ thanh toán' 
                    AND payment_method = 'bank_transfer' 
                    AND TIMESTAMPDIFF(HOUR, created_at, NOW()) >= 24";

$cancel_result = $conn->query($auto_cancel_sql);

if ($cancel_result && $cancel_result->num_rows > 0) {
    echo "Tìm thấy {$cancel_result->num_rows} đơn cần hủy:\n";
    while ($row = $cancel_result->fetch_assoc()) {
        echo "  - Đơn #{$row['order_id']}: đã {$row['hours_diff']} giờ\n";
    }
} else {
    echo "KHÔNG tìm thấy đơn nào đủ điều kiện hủy!\n";
}

// Kiểm tra tất cả đơn chuyển khoản
echo "\n=== TẤT CẢ ĐỚN CHUYỂN KHOẢN ===\n";
$all_bank = "SELECT order_id, order_status, created_at, 
             TIMESTAMPDIFF(HOUR, created_at, NOW()) as hours_passed
             FROM orders 
             WHERE payment_method = 'bank_transfer'
             ORDER BY created_at DESC
             LIMIT 10";

$all_result = $conn->query($all_bank);
while ($row = $all_result->fetch_assoc()) {
    echo "Đơn #{$row['order_id']}: {$row['order_status']} ({$row['hours_passed']}h)\n";
}
