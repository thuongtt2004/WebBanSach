<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== KIỂM TRA ĐỚN #16 ===\n\n";

$result = $conn->query("SELECT * FROM orders WHERE order_id = 16");
$order = $result->fetch_assoc();

echo "Mã đơn: #{$order['order_id']}\n";
echo "Trạng thái: {$order['order_status']}\n";
echo "Phương thức thanh toán: {$order['payment_method']}\n";
echo "Ngày tạo: {$order['created_at']}\n";
echo "Ngày cập nhật: {$order['updated_at']}\n";
echo "Ngày hoàn thành: {$order['completed_date']}\n";
echo "Customer confirmed: {$order['customer_confirmed']}\n";
echo "Ghi chú:\n{$order['notes']}\n\n";

// Tính số ngày
$created = strtotime($order['created_at']);
$updated = strtotime($order['updated_at']);
$now = time();

$days_since_created = floor(($now - $created) / 86400);
$days_since_updated = floor(($now - $updated) / 86400);

echo "Số ngày từ khi tạo: $days_since_created ngày\n";
echo "Số ngày từ lần cập nhật cuối: $days_since_updated ngày\n\n";

// Kiểm tra điều kiện
if ($order['order_status'] == 'Đã trả hàng') {
    echo "⚠️ Đơn hàng này đang ở trạng thái 'Đã trả hàng'\n";
    echo "   → Trạng thái này là CUỐI CÙNG, không có logic tự động nào áp dụng\n";
} elseif ($order['order_status'] == 'Đã giao') {
    if ($days_since_updated >= 7) {
        echo "✓ Đủ điều kiện tự động hoàn thành (đã {$days_since_updated} ngày)\n";
        echo "  → Sẽ được tự động chuyển sang 'Hoàn thành' khi script chạy\n";
    } else {
        echo "✗ Chưa đủ 7 ngày (mới {$days_since_updated} ngày)\n";
    }
}
