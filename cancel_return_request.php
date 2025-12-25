<?php
session_start();
require_once 'config/connect.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Lấy dữ liệu từ POST
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Mã đơn hàng không hợp lệ']);
    exit();
}

try {
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    // Kiểm tra đơn hàng có thuộc về user hiện tại không
    $check_sql = "SELECT user_id, return_status FROM orders WHERE order_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $order_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        throw new Exception('Không tìm thấy đơn hàng');
    }
    
    $order = $check_result->fetch_assoc();
    
    if ($order['user_id'] != $_SESSION['user_id']) {
        throw new Exception('Bạn không có quyền hủy yêu cầu này');
    }
    
    // Kiểm tra trạng thái có cho phép hủy không
    $allowed_statuses = ['Chờ duyệt', 'Chờ xử lý', 'Đã duyệt', '', null];
    if (!in_array($order['return_status'], $allowed_statuses)) {
        throw new Exception('Không thể hủy yêu cầu ở trạng thái hiện tại: ' . $order['return_status']);
    }
    
    // Cập nhật hủy yêu cầu trả hàng
    $update_sql = "UPDATE orders 
                   SET return_request = 0,
                       return_status = 'Đã hủy yêu cầu',
                       order_status = 'Hoàn thành',
                       return_cancelled_date = NOW()
                   WHERE order_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $order_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception('Lỗi khi hủy yêu cầu trả hàng');
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Đã hủy yêu cầu trả hàng thành công!'
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
