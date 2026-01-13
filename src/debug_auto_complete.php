<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== DEBUG TỰ ĐỘNG HOÀN THÀNH ĐỚN HÀNG ===\n\n";

// Kiểm tra các đơn "Đã giao"
$check_sql = "SELECT order_id, order_status, created_at, updated_at, 
              DATEDIFF(NOW(), created_at) as days_since_created,
              DATEDIFF(NOW(), updated_at) as days_since_updated
              FROM orders 
              WHERE order_status = 'Đã giao'
              ORDER BY created_at DESC";

$result = $conn->query($check_sql);

echo "Đơn hàng 'Đã giao':\n";
echo "----------------------------------------\n";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Đơn #{$row['order_id']}\n";
        echo "  - Ngày tạo: {$row['created_at']} ({$row['days_since_created']} ngày trước)\n";
        echo "  - Cập nhật: {$row['updated_at']} ({$row['days_since_updated']} ngày trước)\n";
        echo "  - Đủ điều kiện hoàn thành: " . ($row['days_since_updated'] >= 7 ? "CÓ" : "KHÔNG") . "\n\n";
    }
} else {
    echo "Không có đơn 'Đã giao' nào\n";
}

// Test chạy query tự động hoàn thành
echo "\n=== TEST QUERY TỰ ĐỘNG HOÀN THÀNH ===\n";
$auto_complete_sql = "SELECT o.order_id, od.product_id, od.quantity,
                      o.created_at, o.updated_at,
                      DATEDIFF(NOW(), o.updated_at) as days_diff
                      FROM orders o
                      JOIN order_details od ON o.order_id = od.order_id
                      WHERE o.order_status = 'Đã giao' 
                      AND DATEDIFF(NOW(), o.updated_at) >= 7";

$auto_result = $conn->query($auto_complete_sql);

if ($auto_result && $auto_result->num_rows > 0) {
    echo "Tìm thấy {$auto_result->num_rows} sản phẩm từ các đơn cần hoàn thành:\n";
    while ($row = $auto_result->fetch_assoc()) {
        echo "  - Đơn #{$row['order_id']}: Sản phẩm {$row['product_id']} x{$row['quantity']} (đã {$row['days_diff']} ngày)\n";
    }
} else {
    echo "KHÔNG tìm thấy đơn nào đủ điều kiện!\n";
}

echo "\n=== KIỂM TRA TRƯỜNG UPDATED_AT ===\n";
$updated_check = "SELECT order_id, order_status, created_at, updated_at FROM orders WHERE order_id IN (420, 18) ORDER BY order_id DESC";
$updated_result = $conn->query($updated_check);

while ($row = $updated_result->fetch_assoc()) {
    echo "Đơn #{$row['order_id']}: created={$row['created_at']}, updated={$row['updated_at']}, status={$row['order_status']}\n";
}
