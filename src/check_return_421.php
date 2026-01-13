<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== KIỂM TRA YÊU CẦU TRẢ HÀNG #421 ===\n\n";

// Kiểm tra bảng return_requests
$check_table = $conn->query("SHOW TABLES LIKE 'return_requests'");
if ($check_table->num_rows > 0) {
    echo "✓ Bảng return_requests tồn tại\n\n";
    
    // Kiểm tra yêu cầu #421
    $result = $conn->query("SELECT * FROM return_requests WHERE order_id = 421");
    
    if ($result && $result->num_rows > 0) {
        $return = $result->fetch_assoc();
        echo "YÊU CẦU TRẢ HÀNG:\n";
        echo "-------------------\n";
        foreach ($return as $key => $value) {
            echo "$key: $value\n";
        }
        echo "\n";
        
        if (isset($return['status'])) {
            if ($return['status'] == 'pending' || $return['status'] == 'Chờ xử lý') {
                echo "⚠️ Trạng thái: {$return['status']}\n";
                echo "→ Đây là lý do vẫn hiển thị trong danh sách!\n\n";
                
                echo "Bạn muốn:\n";
                echo "1. Hủy yêu cầu này? (status = 'cancelled')\n";
                echo "2. Xóa yêu cầu khỏi database?\n";
            } else {
                echo "Trạng thái: {$return['status']}\n";
            }
        }
    } else {
        echo "Không tìm thấy yêu cầu trả hàng cho đơn #421\n";
    }
    
    // Kiểm tra đơn hàng #421
    echo "\n=== KIỂM TRA ĐỚN HÀNG #421 ===\n";
    $order = $conn->query("SELECT order_id, order_status FROM orders WHERE order_id = 421");
    if ($order && $order->num_rows > 0) {
        $o = $order->fetch_assoc();
        echo "Đơn #421: {$o['order_status']}\n";
    }
    
} else {
    echo "❌ Bảng return_requests không tồn tại!\n";
    echo "Kiểm tra các bảng có chứa 'return'...\n\n";
    
    $tables = $conn->query("SHOW TABLES");
    while ($table = $tables->fetch_array()) {
        if (stripos($table[0], 'return') !== false) {
            echo "  - {$table[0]}\n";
        }
    }
}
