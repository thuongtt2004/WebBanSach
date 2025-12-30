<?php
session_start();
require_once 'config/connect.php';

// Sau khi đặt hàng thành công, gọi function này để ghi nhận sử dụng khuyến mãi
function recordPromotionUsage($conn, $promotion_id, $user_id, $order_id, $discount_amount) {
    // Tăng số lượng đã sử dụng
    $update_usage = $conn->prepare("UPDATE promotions SET used_count = used_count + 1 WHERE promotion_id = ?");
    $update_usage->bind_param("i", $promotion_id);
    $update_usage->execute();
    $update_usage->close();
    
    // Lưu lịch sử sử dụng
    $insert_usage = $conn->prepare("INSERT INTO promotion_usage (promotion_id, user_id, order_id, discount_amount) VALUES (?, ?, ?, ?)");
    $insert_usage->bind_param("iiid", $promotion_id, $user_id, $order_id, $discount_amount);
    $insert_usage->execute();
    $insert_usage->close();
    
    // Kiểm tra và cập nhật trạng thái nếu đã hết lượt sử dụng
    $check_limit = $conn->query("SELECT usage_limit, used_count FROM promotions WHERE promotion_id = $promotion_id");
    if ($check_limit) {
        $limit_data = $check_limit->fetch_assoc();
        if ($limit_data['usage_limit'] !== null && $limit_data['used_count'] >= $limit_data['usage_limit']) {
            $conn->query("UPDATE promotions SET status = 'inactive' WHERE promotion_id = $promotion_id");
        }
    }
}

// Ví dụ sử dụng trong trang xử lý đặt hàng:
/*
if (isset($_SESSION['pending_promotion']) && $order_success) {
    $promotion = $_SESSION['pending_promotion'];
    recordPromotionUsage(
        $conn,
        $promotion['promotion_id'],
        $_SESSION['user_id'],
        $order_id,
        $promotion['discount_amount']
    );
    unset($_SESSION['pending_promotion']);
}
*/
?>
