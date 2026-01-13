<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== KIỂM TRA ĐƠN #421 ===\n\n";

$result = $conn->query("SELECT order_id, order_status, return_request, return_status, return_reason, return_request_date FROM orders WHERE order_id = 421");

if ($result && $result->num_rows > 0) {
    $order = $result->fetch_assoc();
    
    echo "ĐƠN HÀNG #421:\n";
    echo "-------------------\n";
    echo "order_status: {$order['order_status']}\n";
    echo "return_request: {$order['return_request']}\n";
    echo "return_status: {$order['return_status']}\n";
    echo "return_reason: {$order['return_reason']}\n";
    echo "return_request_date: {$order['return_request_date']}\n\n";
    
    if ($order['return_request'] == 1) {
        echo "⚠️ VẤN ĐỀ: return_request vẫn = 1\n";
        echo "→ Đây là lý do vẫn hiển thị trong danh sách!\n\n";
        
        echo "Đang sửa...\n";
        if ($order['return_status'] == 'Đã hủy yêu cầu' || $order['order_status'] == 'Đã hủy') {
            $update = "UPDATE orders SET return_request = 0 WHERE order_id = 421";
            if ($conn->query($update)) {
                echo "✅ Đã set return_request = 0\n";
            } else {
                echo "❌ Lỗi: " . $conn->error . "\n";
            }
        }
    } else {
        echo "✓ return_request = 0 (đúng)\n";
    }
} else {
    echo "Không tìm thấy đơn #421\n";
}
