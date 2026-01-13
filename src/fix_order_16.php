<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== KIỂM TRA VÀ SỬA ĐƠN #16 ===\n\n";

// Kiểm tra đơn hiện tại
$check = $conn->query("SELECT order_id, order_status, payment_method, created_at, 
                       TIMESTAMPDIFF(HOUR, created_at, NOW()) as hours
                       FROM orders WHERE order_id = 16");
$order = $check->fetch_assoc();

echo "TRẠNG THÁI HIỆN TẠI:\n";
echo "Đơn #16: {$order['order_status']}\n";
echo "Payment: {$order['payment_method']}\n";
echo "Số giờ: {$order['hours']} giờ (" . floor($order['hours']/24) . " ngày)\n\n";

// Nếu đang là "Đã trả hàng" nhưng trên web hiển thị khác
if ($order['order_status'] == 'Đã trả hàng') {
    echo "✓ Database đúng là 'Đã trả hàng'\n";
    echo "→ Vấn đề là CACHE trình duyệt!\n\n";
    echo "Giải pháp:\n";
    echo "1. Ctrl + Shift + R (hard refresh)\n";
    echo "2. Hoặc xóa cache trình duyệt\n";
    echo "3. Hoặc mở Incognito window\n";
}

// Nếu vẫn là "Chờ thanh toán" thì cần hủy
if ($order['order_status'] == 'Chờ thanh toán') {
    echo "⚠️ Đơn vẫn đang 'Chờ thanh toán' - cần hủy!\n\n";
    
    if ($order['payment_method'] == 'bank_transfer' && $order['hours'] >= 24) {
        echo "Đang hủy đơn...\n";
        $update = "UPDATE orders 
                   SET order_status = 'Đã hủy',
                       notes = CONCAT(IFNULL(notes, ''), '\n[Thủ công] Hủy đơn quá 24h chưa thanh toán')
                   WHERE order_id = 16";
        
        if ($conn->query($update)) {
            echo "✅ Đã hủy đơn #16\n";
        } else {
            echo "❌ Lỗi: " . $conn->error . "\n";
        }
    }
}

// Kiểm tra lại sau khi update
echo "\n=== KIỂM TRA LẠI ===\n";
$recheck = $conn->query("SELECT order_id, order_status FROM orders WHERE order_id = 16");
$final = $recheck->fetch_assoc();
echo "Đơn #16 cuối cùng: {$final['order_status']}\n";
