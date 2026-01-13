<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

// Kiểm tra đơn #13
$result = $conn->query("SELECT order_id, order_status, payment_method, created_at, notes FROM orders WHERE order_id = 13");
$order = $result->fetch_assoc();

echo "=== KIỂM TRA ĐỚN #13 ===\n\n";
echo "Trạng thái: {$order['order_status']}\n";
echo "Phương thức: {$order['payment_method']}\n";
echo "Ngày tạo: {$order['created_at']}\n";
echo "Ghi chú:\n{$order['notes']}\n\n";

// Tạo đơn test để kiểm tra tự động hủy
echo "=== TẠO ĐƠN TEST ===\n";

// Lấy user_id thật
$user_result = $conn->query("SELECT user_id FROM users LIMIT 1");
$user = $user_result->fetch_assoc();
$user_id = $user['user_id'];

echo "Sử dụng user_id: $user_id\n";

// Tạo đơn "Chờ thanh toán" với thời gian 25 giờ trước
$test_time = date('Y-m-d H:i:s', strtotime('-25 hours'));

$insert_sql = "INSERT INTO orders (user_id, full_name, email, phone, address, total_amount, payment_method, order_status, created_at, notes) 
               VALUES ($user_id, 'Test User', 'test@test.com', '0123456789', 'Test Address', 100000, 'bank_transfer', 'Chờ thanh toán', '$test_time', 'Đơn test tự động hủy')";

if ($conn->query($insert_sql)) {
    $test_order_id = $conn->insert_id;
    echo "✓ Đã tạo đơn test #$test_order_id với thời gian: $test_time\n";
    echo "  (25 giờ trước = đủ điều kiện hủy)\n\n";
    
    // Kiểm tra đơn này
    $check = $conn->query("SELECT order_id, TIMESTAMPDIFF(HOUR, created_at, NOW()) as hours FROM orders WHERE order_id = $test_order_id");
    $data = $check->fetch_assoc();
    echo "  Số giờ đã qua: {$data['hours']} giờ\n";
    echo "  Đủ điều kiện hủy: " . ($data['hours'] >= 24 ? "CÓ ✓" : "KHÔNG ✗") . "\n";
} else {
    echo "✗ Lỗi tạo đơn: " . $conn->error . "\n";
}
