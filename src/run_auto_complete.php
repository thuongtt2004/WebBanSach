<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== CHẠY TỰ ĐỘNG HOÀN THÀNH (THỰC SỰ CẬP NHẬT) ===\n\n";

// Xóa log để cho phép chạy
$log_file = __DIR__ . '/logs/last_auto_run.txt';
if (file_exists($log_file)) {
    unlink($log_file);
    echo "✓ Đã xóa log file\n\n";
}

// Chạy logic từ session_init
require_once 'session_init.php';

echo "\n=== KIỂM TRA KẾT QUẢ ===\n";

// Kiểm tra các đơn đã được cập nhật
$check_sql = "SELECT order_id, order_status, customer_confirmed, completed_date, notes
              FROM orders 
              WHERE order_id IN (18, 420)
              ORDER BY order_id DESC";

$result = $conn->query($check_sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "\nĐơn #{$row['order_id']}:\n";
        echo "  Trạng thái: {$row['order_status']}\n";
        echo "  Customer confirmed: {$row['customer_confirmed']}\n";
        echo "  Completed date: {$row['completed_date']}\n";
        echo "  Ghi chú: " . substr($row['notes'], -100) . "\n";
    }
} else {
    echo "Không tìm thấy đơn hàng\n";
}

echo "\n✅ HOÀN THÀNH!\n";
