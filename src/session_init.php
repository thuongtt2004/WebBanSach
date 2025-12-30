<?php
// Khởi tạo session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tự động chạy script xác nhận hài lòng và hủy đơn quá hạn (chạy mỗi 1h một lần)
$last_auto_run_file = __DIR__ . '/logs/last_auto_run.txt';
$should_run = true;

if (file_exists($last_auto_run_file)) {
    $last_run = file_get_contents($last_auto_run_file);
    if (time() - (int)$last_run < 3600) { // 1 giờ = 3600 giây
        $should_run = false;
    }
}

if ($should_run) {
    require_once __DIR__ . '/config/connect.php';
    
    // Cập nhật completed_date cho đơn cũ
    $conn->query("UPDATE orders SET completed_date = created_at WHERE order_status = 'Hoàn thành' AND completed_date IS NULL");
    
    // Tự động xác nhận đơn hàng sau 7 ngày
    $conn->query("UPDATE orders SET customer_confirmed = 1 WHERE order_status = 'Hoàn thành' AND customer_confirmed = 0 AND ((completed_date IS NOT NULL AND DATEDIFF(NOW(), completed_date) >= 7) OR (completed_date IS NULL AND DATEDIFF(NOW(), created_at) >= 7))");
    
    // Tự động hủy đơn chờ thanh toán quá 24h
    $conn->query("UPDATE orders SET order_status = 'Đã hủy', notes = CONCAT(IFNULL(notes, ''), '\nTự động hủy: Quá thời gian thanh toán (24h)') WHERE order_status = 'Chờ thanh toán' AND payment_method = 'bank_transfer' AND TIMESTAMPDIFF(HOUR, created_at, NOW()) >= 24");
    
    // Lưu thời gian chạy
    if (!is_dir(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0777, true);
    }
    file_put_contents($last_auto_run_file, time());
}
