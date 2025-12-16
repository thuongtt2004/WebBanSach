<?php
session_start();
require_once 'config/connect.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_POST['order_id'] ?? 0);
$action = $_POST['action'] ?? '';

try {
    // Kiểm tra đơn hàng có thuộc về user này không
    $check_sql = "SELECT order_id, order_status, completed_date FROM orders WHERE order_id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $order_id, $user_id);
    $check_stmt->execute();
    $order = $check_stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        throw new Exception('Đơn hàng không tồn tại');
    }
    
    // Xử lý theo action
    if ($action === 'confirm_delivery') {
        // Xác nhận đã nhận hàng
        $update_sql = "UPDATE orders SET customer_confirmed = 1 WHERE order_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $order_id);
        
        if ($update_stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Cảm ơn bạn đã xác nhận! Chúc bạn hài lòng với sản phẩm.'
            ]);
        } else {
            throw new Exception('Không thể xác nhận đơn hàng');
        }
        
    } elseif ($action === 'request_return') {
        // Kiểm tra trạng thái đơn hàng
        $valid_statuses = ['Đã giao', 'Hoàn thành'];
        if (!in_array($order['order_status'], $valid_statuses)) {
            throw new Exception('Chỉ có thể yêu cầu trả hàng cho đơn hàng ở trạng thái "Đã giao" hoặc "Hoàn thành"');
        }
        
        // Nếu đơn hàng đã hoàn thành, kiểm tra thời gian
        if ($order['order_status'] === 'Hoàn thành') {
            $completed_date = strtotime($order['completed_date']);
            $days_passed = floor((time() - $completed_date) / 86400);
            $return_days_limit = 7; // Số ngày cho phép trả hàng
            
            if ($days_passed > $return_days_limit) {
                throw new Exception("Đã quá thời hạn trả hàng ({$return_days_limit} ngày kể từ khi hoàn thành đơn hàng)");
            }
        }
        
        $return_reason = trim($_POST['return_reason'] ?? '');
        if (empty($return_reason)) {
            throw new Exception('Vui lòng nhập lý do trả hàng');
        }
        
        // Cập nhật trạng thái đơn hàng và lưu yêu cầu trả hàng
        $update_sql = "UPDATE orders 
                       SET order_status = 'Yêu cầu trả hàng',
                           return_request = 1, 
                           return_reason = ?, 
                           return_request_date = NOW(),
                           return_status = 'Chờ xử lý'
                       WHERE order_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $return_reason, $order_id);
        
        if ($update_stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Yêu cầu trả hàng đã được gửi. Chúng tôi sẽ xem xét và phản hồi trong vòng 24-48h.'
            ]);
        } else {
            throw new Exception('Không thể gửi yêu cầu trả hàng');
        }
        
    } else {
        throw new Exception('Hành động không hợp lệ');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
