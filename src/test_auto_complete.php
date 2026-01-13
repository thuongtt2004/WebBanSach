<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== CHẠY THỬ LOGIC TỰ ĐỘNG HOÀN THÀNH ===\n\n";

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // TỰ ĐỘNG HOÀN THÀNH ĐỚN HÀNG: Chuyển "Đã giao" sang "Hoàn thành" sau 7 ngày
    // Lấy các đơn hàng "Đã giao" quá 7 ngày
    $auto_complete_sql = "SELECT o.order_id, od.product_id, od.quantity 
                          FROM orders o
                          JOIN order_details od ON o.order_id = od.order_id
                          WHERE o.order_status = 'Đã giao' 
                          AND DATEDIFF(NOW(), o.updated_at) >= 7";
    
    echo "Query: $auto_complete_sql\n\n";
    
    $auto_complete_result = $conn->query($auto_complete_sql);
    
    if ($auto_complete_result && $auto_complete_result->num_rows > 0) {
        echo "Tìm thấy {$auto_complete_result->num_rows} sản phẩm cần xử lý\n\n";
        
        $processed_orders = [];
        
        while ($row = $auto_complete_result->fetch_assoc()) {
            $order_id = $row['order_id'];
            
            echo "Xử lý đơn #$order_id...\n";
            
            // Trừ tồn kho cho mỗi sản phẩm (chỉ xử lý 1 lần mỗi đơn)
            if (!in_array($order_id, $processed_orders)) {
                // Lấy tất cả sản phẩm của đơn này
                $details_sql = "SELECT product_id, quantity FROM order_details WHERE order_id = $order_id";
                $details_result = $conn->query($details_sql);
                
                echo "  Trừ tồn kho:\n";
                while ($detail = $details_result->fetch_assoc()) {
                    $update_stock = "UPDATE products 
                                     SET stock_quantity = stock_quantity - {$detail['quantity']}, 
                                         sold_quantity = sold_quantity + {$detail['quantity']} 
                                     WHERE product_id = '{$detail['product_id']}'";
                    
                    $conn->query($update_stock);
                    echo "    - Sản phẩm {$detail['product_id']}: -{$detail['quantity']}\n";
                }
                
                // Cập nhật trạng thái đơn hàng
                $update_order = "UPDATE orders 
                                 SET order_status = 'Hoàn thành', 
                                     completed_date = NOW(),
                                     customer_confirmed = 1,
                                     notes = CONCAT(IFNULL(notes, ''), '\n[Tự động] Đơn hàng đã được xác nhận hoàn thành sau 7 ngày giao hàng.')
                                 WHERE order_id = $order_id";
                
                $conn->query($update_order);
                echo "  ✓ Đã chuyển sang 'Hoàn thành'\n\n";
                
                $processed_orders[] = $order_id;
            }
        }
        
        echo "Đã xử lý " . count($processed_orders) . " đơn hàng\n";
        echo "Danh sách: " . implode(", ", $processed_orders) . "\n\n";
        
        // ROLLBACK để không thực sự update - CHỈ TEST
        echo "===  ROLLBACK (Chỉ test, không lưu thay đổi) ===\n";
        $conn->rollback();
        
        echo "\nNếu muốn THỰC SỰ CẬP NHẬT, sửa rollback() thành commit()\n";
        
    } else {
        echo "KHÔNG tìm thấy đơn nào đủ điều kiện!\n";
        $conn->rollback();
    }
    
} catch (Exception $e) {
    echo "LỖI: " . $e->getMessage() . "\n";
    $conn->rollback();
}
