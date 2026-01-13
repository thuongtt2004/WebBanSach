<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== PHÂN TÍCH MÂU THUẪN DỮ LIỆU ĐỚN #16 ===\n\n";

// Kiểm tra chi tiết đơn #16
$result = $conn->query("SELECT * FROM orders WHERE order_id = 16");
$order = $result->fetch_assoc();

echo "CHI TIẾT ĐƠN #16:\n";
echo "-------------------\n";
echo "order_id: {$order['order_id']}\n";
echo "user_id: {$order['user_id']}\n";
echo "order_status: {$order['order_status']}\n";
echo "payment_method: {$order['payment_method']}\n";
echo "total_amount: {$order['total_amount']}\n";
echo "created_at: {$order['created_at']}\n";
echo "updated_at: {$order['updated_at']}\n";
echo "completed_date: {$order['completed_date']}\n";
echo "customer_confirmed: {$order['customer_confirmed']}\n";
echo "refund_date: {$order['refund_date']}\n";
echo "payment_proof: {$order['payment_proof']}\n";
echo "notes: {$order['notes']}\n\n";

// Phân tích thời gian
$created = strtotime($order['created_at']);
$updated = strtotime($order['updated_at']);
$completed = $order['completed_date'] ? strtotime($order['completed_date']) : null;

echo "PHÂN TÍCH THỜI GIAN:\n";
echo "-------------------\n";
echo "Tạo đơn: {$order['created_at']}\n";
echo "Cập nhật: {$order['updated_at']}\n";
echo "Khoảng cách: " . round(($updated - $created) / 60) . " phút\n\n";

if ($completed) {
    echo "Hoàn thành: {$order['completed_date']}\n";
    echo "Khoảng cách tạo -> hoàn thành: " . round(($completed - $created) / 60) . " phút\n\n";
}

// Kiểm tra logic: Đơn chuyển khoản không thể là "Đã trả hàng" ngay từ đầu
if ($order['payment_method'] == 'bank_transfer' && $order['order_status'] == 'Đã trả hàng') {
    echo "⚠️ MÂU THUẪN PHÁT HIỆN!\n";
    echo "-------------------\n";
    echo "- Payment method: Chuyển khoản\n";
    echo "- Trạng thái: Đã trả hàng\n";
    echo "- Completed date: {$order['completed_date']}\n\n";
    
    if ($completed && ($completed - $created) < 300) { // < 5 phút
        echo "🔴 BẤT THƯỜNG: Đơn được 'hoàn thành' chỉ sau " . round(($completed - $created) / 60) . " phút!\n";
        echo "   → Có thể do:\n";
        echo "   1. Lỗi logic khi tạo đơn\n";
        echo "   2. Ai đó thao tác thủ công\n";
        echo "   3. Test data không chuẩn\n\n";
    }
}

// Kiểm tra các đơn khác có vấn đề tương tự
echo "\n=== KIỂM TRA CÁC ĐỚN KHÁC ===\n";
$suspicious = $conn->query("SELECT order_id, order_status, payment_method, created_at, completed_date,
                            TIMESTAMPDIFF(MINUTE, created_at, completed_date) as minutes_to_complete
                            FROM orders 
                            WHERE payment_method = 'bank_transfer'
                            AND order_status IN ('Đã trả hàng', 'Hoàn thành')
                            AND TIMESTAMPDIFF(MINUTE, created_at, completed_date) < 60
                            ORDER BY order_id DESC
                            LIMIT 10");

if ($suspicious->num_rows > 0) {
    echo "Các đơn chuyển khoản hoàn thành/trả hàng < 1 giờ:\n";
    while ($s = $suspicious->fetch_assoc()) {
        echo "  - Đơn #{$s['order_id']}: {$s['order_status']} sau {$s['minutes_to_complete']} phút\n";
    }
} else {
    echo "Không tìm thấy đơn bất thường khác\n";
}

// Kiểm tra xem có bảng activity_logs không
$tables = $conn->query("SHOW TABLES LIKE 'activity_logs'");
if ($tables->num_rows > 0) {
    echo "\n=== KIỂM TRA LOGS ===\n";
    $logs = $conn->query("SELECT * FROM activity_logs WHERE description LIKE '%#16%' OR description LIKE '%order_id = 16%' ORDER BY created_at DESC LIMIT 5");
    if ($logs->num_rows > 0) {
        while ($log = $logs->fetch_assoc()) {
            echo "  [{$log['created_at']}] {$log['action']}: {$log['description']}\n";
        }
    } else {
        echo "Không có log cho đơn #16\n";
    }
}
