<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== KIỂM TRA LẠI ĐƠN #16 ===\n\n";

$result = $conn->query("SELECT order_id, order_status, payment_method, created_at, 
                        TIMESTAMPDIFF(HOUR, created_at, NOW()) as hours_passed
                        FROM orders WHERE order_id = 16");
$order = $result->fetch_assoc();

echo "Đơn #{$order['order_id']}\n";
echo "Trạng thái: {$order['order_status']}\n";
echo "Phương thức: {$order['payment_method']}\n";
echo "Ngày tạo: {$order['created_at']}\n";
echo "Số giờ đã qua: {$order['hours_passed']} giờ\n";
echo "Số ngày: " . floor($order['hours_passed'] / 24) . " ngày\n\n";

if ($order['order_status'] == 'Chờ thanh toán' && $order['payment_method'] == 'bank_transfer') {
    if ($order['hours_passed'] >= 24) {
        echo "⚠️ ĐƠN NÀY ĐÃ QUÁ 24 GIỜ VÀ ĐÁNG LẼ PHẢI BỊ HỦY!\n";
        echo "   Đang ở trạng thái 'Chờ thanh toán' được {$order['hours_passed']} giờ\n\n";
        
        echo "Chạy lệnh hủy thủ công...\n";
        $update = "UPDATE orders 
                   SET order_status = 'Đã hủy', 
                       notes = CONCAT(IFNULL(notes, ''), '\n[Thủ công] Hủy đơn quá thời gian thanh toán') 
                   WHERE order_id = 16";
        
        if ($conn->query($update)) {
            echo "✅ Đã hủy đơn #16 thành công!\n";
        } else {
            echo "❌ Lỗi: " . $conn->error . "\n";
        }
    } else {
        echo "Đơn chưa đủ 24 giờ\n";
    }
} else {
    echo "Trạng thái: {$order['order_status']}\n";
}
