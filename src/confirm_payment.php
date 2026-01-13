<?php
session_start();
require_once 'config/connect.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Lấy dữ liệu JSON
$input = json_decode(file_get_contents('php://input'), true);

$order_id = $input['order_id'] ?? null;
$action = $input['action'] ?? null;
$reason = $input['reason'] ?? '';

// Validate
if (!$order_id || !$action) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit();
}

try {
    // Kiểm tra đơn hàng tồn tại
    $check_sql = "SELECT order_id, order_status, payment_method FROM orders WHERE order_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $order_id);
    $check_stmt->execute();
    $order = $check_stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        throw new Exception("Không tìm thấy đơn hàng");
    }
    
    if ($order['payment_method'] !== 'bank_transfer') {
        throw new Exception("Đơn hàng này không phải thanh toán chuyển khoản");
    }
    
    // Chấp nhận cả "Chờ thanh toán" và "Chờ xác nhận" với bank_transfer
    $allowed_statuses = ['Chờ thanh toán', 'Chờ xác nhận'];
    if (!in_array($order['order_status'], $allowed_statuses)) {
        throw new Exception("Đơn hàng này đã được xử lý");
    }

    if ($action === 'approve') {
        // Xác nhận thanh toán
        // Nếu đang "Chờ thanh toán" -> chuyển sang "Đã xác nhận" (đã thanh toán rồi)
        // Nếu đang "Chờ xác nhận" -> chuyển sang "Đã xác nhận" (xác nhận đơn hàng)
        $new_status = 'Đã xác nhận';
        
        $update_sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $new_status, $order_id);
        $update_stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Đã xác nhận thanh toán. Đơn hàng chuyển sang trạng thái "Đã xác nhận"'
        ]);
        
    } elseif ($action === 'reject') {
        // Từ chối thanh toán - chuyển sang "Đã hủy"
        $notes = "Từ chối thanh toán: " . $reason;
        $update_sql = "UPDATE orders SET order_status = 'Đã hủy', notes = CONCAT(COALESCE(notes, ''), '\n', ?) WHERE order_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $notes, $order_id);
        $update_stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Đã từ chối thanh toán và hủy đơn hàng'
        ]);
        
    } else {
        throw new Exception("Hành động không hợp lệ");
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
