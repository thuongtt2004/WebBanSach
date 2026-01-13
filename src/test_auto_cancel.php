<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== TEST TỰ ĐỘNG HỦY ĐỚN CHỜ THANH TOÁN ===\n\n";

// Xóa log để force chạy
$log_file = __DIR__ . '/logs/last_auto_run.txt';
if (file_exists($log_file)) {
    unlink($log_file);
    echo "✓ Đã xóa log file\n\n";
}

// Kiểm tra đơn test trước khi chạy
echo "TRƯỚC KHI CHẠY:\n";
$before = $conn->query("SELECT order_id, order_status, notes FROM orders WHERE order_id = 424");
$b = $before->fetch_assoc();
echo "Đơn #424: {$b['order_status']}\n\n";

// Chạy logic từ session_init
echo "Đang chạy session_init.php...\n";
require_once 'session_init.php';
echo "✓ Đã chạy xong\n\n";

// Kiểm tra đơn test sau khi chạy
echo "SAU KHI CHẠY:\n";
$after = $conn->query("SELECT order_id, order_status, notes FROM orders WHERE order_id = 424");
$a = $after->fetch_assoc();
echo "Đơn #424: {$a['order_status']}\n";
echo "Ghi chú: {$a['notes']}\n\n";

if ($a['order_status'] === 'Đã hủy') {
    echo "✅ THÀNH CÔNG! Đơn đã được tự động hủy\n";
} else {
    echo "❌ THẤT BẠI! Đơn vẫn chưa bị hủy\n";
    echo "\nDebug - Kiểm tra query:\n";
    $debug = $conn->query("SELECT order_id, order_status, payment_method, created_at, 
                          TIMESTAMPDIFF(HOUR, created_at, NOW()) as hours
                          FROM orders WHERE order_id = 424");
    $d = $debug->fetch_assoc();
    print_r($d);
}
