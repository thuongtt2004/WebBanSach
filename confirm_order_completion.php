<?php
session_start();
require_once 'config/connect.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để xác nhận đơn hàng']);
    exit();
}

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Lấy order_id từ POST
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Mã đơn hàng không hợp lệ']);
    exit();
}

try {
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    // Lấy thông tin đơn hàng
    $order_sql = "SELECT order_id, user_id, order_status FROM orders WHERE order_id = ?";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("i", $order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    
    if ($order_result->num_rows === 0) {
        throw new Exception('Không tìm thấy đơn hàng');
    }
    
    $order = $order_result->fetch_assoc();
    
    // Kiểm tra đơn hàng có thuộc về user hiện tại không
    if ($order['user_id'] != $_SESSION['user_id']) {
        throw new Exception('Bạn không có quyền xác nhận đơn hàng này');
    }
    
    // Kiểm tra trạng thái đơn hàng phải là "Đã giao"
    if ($order['order_status'] !== 'Đã giao') {
        throw new Exception('Chỉ có thể xác nhận đơn hàng ở trạng thái "Đã giao"');
    }
    
    // Lấy chi tiết đơn hàng để trừ tồn kho
    $details_sql = "SELECT product_id, quantity FROM order_details WHERE order_id = ?";
    $details_stmt = $conn->prepare($details_sql);
    $details_stmt->bind_param("i", $order_id);
    $details_stmt->execute();
    $details_result = $details_stmt->get_result();
    
    // Trừ tồn kho và tăng số lượng đã bán
    $update_stock_sql = "UPDATE products 
                         SET stock_quantity = stock_quantity - ?, 
                             sold_quantity = sold_quantity + ? 
                         WHERE product_id = ?";
    $update_stock_stmt = $conn->prepare($update_stock_sql);
    
    while ($detail = $details_result->fetch_assoc()) {
        $update_stock_stmt->bind_param("iii", $detail['quantity'], $detail['quantity'], $detail['product_id']);
        if (!$update_stock_stmt->execute()) {
            throw new Exception('Lỗi khi cập nhật tồn kho');
        }
    }
    
    // Cập nhật trạng thái đơn hàng sang "Hoàn thành" và lưu thời gian hoàn thành
    $update_order_sql = "UPDATE orders 
                         SET order_status = 'Hoàn thành', 
                             completed_date = NOW(),
                             customer_confirmed = 1
                         WHERE order_id = ?";
    $update_order_stmt = $conn->prepare($update_order_sql);
    $update_order_stmt->bind_param("i", $order_id);
    
    if (!$update_order_stmt->execute()) {
        throw new Exception('Lỗi khi cập nhật trạng thái đơn hàng');
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Xác nhận đơn hàng thành công! Cảm ơn bạn đã mua hàng.'
    ]);
    
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
