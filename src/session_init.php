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
    
    /** @var mysqli $conn */
    
    // Cập nhật completed_date cho đơn cũ
    $conn->query("UPDATE orders SET completed_date = created_at WHERE order_status = 'Hoàn thành' AND completed_date IS NULL");
    
    // TỰ ĐỘNG HOÀN THÀNH ĐỚN HÀNG: Chuyển "Đã giao" sang "Hoàn thành" sau 7 ngày
    $auto_complete_sql = "SELECT o.order_id
                          FROM orders o
                          WHERE o.order_status = 'Đã giao' 
                          AND DATEDIFF(NOW(), o.updated_at) >= 7";
    $auto_complete_result = $conn->query($auto_complete_sql);
    
    if ($auto_complete_result && $auto_complete_result->num_rows > 0) {
        while ($row = $auto_complete_result->fetch_assoc()) {
            $order_id = $row['order_id'];
            
            // Lấy tất cả sản phẩm của đơn này và trừ tồn kho
            $details_sql = "SELECT product_id, quantity FROM order_details WHERE order_id = $order_id";
            $details_result = $conn->query($details_sql);
            
            if ($details_result) {
                while ($detail = $details_result->fetch_assoc()) {
                    $product_id = $conn->real_escape_string($detail['product_id']);
                    $quantity = intval($detail['quantity']);
                    
                    $conn->query("UPDATE products 
                                 SET stock_quantity = stock_quantity - $quantity, 
                                     sold_quantity = sold_quantity + $quantity 
                                 WHERE product_id = '$product_id'");
                }
            }
            
            // Cập nhật trạng thái đơn hàng
            $conn->query("UPDATE orders 
                         SET order_status = 'Hoàn thành', 
                             completed_date = NOW(),
                             customer_confirmed = 1,
                             notes = CONCAT(IFNULL(notes, ''), '\\n[Tự động] Đơn hàng đã được xác nhận hoàn thành sau 7 ngày giao hàng.')
                         WHERE order_id = $order_id");
        }
    }
    
    // Tự động xác nhận customer_confirmed cho đơn "Hoàn thành" sau 7 ngày (cho các đơn đã hoàn thành thủ công)
    $conn->query("UPDATE orders 
                 SET customer_confirmed = 1,
                     notes = CONCAT(IFNULL(notes, ''), '\\n[Tự động] Xác nhận hài lòng sau 7 ngày.')
                 WHERE order_status = 'Hoàn thành' 
                 AND customer_confirmed = 0 
                 AND ((completed_date IS NOT NULL AND DATEDIFF(NOW(), completed_date) >= 7) 
                      OR (completed_date IS NULL AND DATEDIFF(NOW(), created_at) >= 7))");
    
    // Tự động hủy đơn chờ thanh toán quá 24h
    $conn->query("UPDATE orders 
                 SET order_status = 'Đã hủy', 
                     notes = CONCAT(IFNULL(notes, ''), '\\nTự động hủy: Quá thời gian thanh toán (24h)') 
                 WHERE order_status = 'Chờ thanh toán' 
                 AND payment_method = 'bank_transfer' 
                 AND TIMESTAMPDIFF(HOUR, created_at, NOW()) >= 24");
    
    // Lưu thời gian chạy
    if (!is_dir(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0777, true);
    }
    file_put_contents($last_auto_run_file, time());
}
