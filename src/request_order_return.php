<?php
session_start();
require_once 'config/connect.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để yêu cầu trả hàng']);
    exit();
}

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Lấy dữ liệu từ POST
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$return_reason = isset($_POST['return_reason']) ? trim($_POST['return_reason']) : '';

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Mã đơn hàng không hợp lệ']);
    exit();
}

if (empty($return_reason)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập lý do trả hàng']);
    exit();
}

// Số ngày cho phép trả hàng
define('RETURN_PERIOD_DAYS', 7);

try {
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    // Lấy thông tin đơn hàng
    $order_sql = "SELECT order_id, user_id, order_status, completed_date FROM orders WHERE order_id = ?";
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
        throw new Exception('Bạn không có quyền yêu cầu trả hàng cho đơn hàng này');
    }
    
    // Kiểm tra trạng thái đơn hàng
    $valid_statuses = ['Đã giao', 'Hoàn thành'];
    if (!in_array($order['order_status'], $valid_statuses)) {
        throw new Exception('Chỉ có thể yêu cầu trả hàng cho đơn hàng ở trạng thái "Đã giao" hoặc "Hoàn thành"');
    }
    
    // Nếu đơn hàng đã hoàn thành, kiểm tra thời gian cho phép trả hàng
    if ($order['order_status'] === 'Hoàn thành') {
        if (empty($order['completed_date'])) {
            throw new Exception('Không tìm thấy ngày hoàn thành đơn hàng');
        }
        
        $completed_date = strtotime($order['completed_date']);
        $days_passed = floor((time() - $completed_date) / 86400);
        
        if ($days_passed > RETURN_PERIOD_DAYS) {
            throw new Exception('Đã hết thời gian yêu cầu trả hàng. Thời gian cho phép là ' . RETURN_PERIOD_DAYS . ' ngày kể từ khi hoàn thành đơn hàng');
        }
    }
    
    // Cập nhật đơn hàng với thông tin trả hàng
    $update_sql = "UPDATE orders 
                   SET order_status = 'Yêu cầu trả hàng',
                       return_request = 1,
                       return_reason = ?,
                       return_request_date = NOW(),
                       return_status = 'Chờ xử lý'
                   WHERE order_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $return_reason, $order_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception('Lỗi khi cập nhật yêu cầu trả hàng');
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Đã gửi yêu cầu trả hàng thành công! Chúng tôi sẽ xem xét và liên hệ lại với bạn trong vòng 24-48 giờ.'
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
