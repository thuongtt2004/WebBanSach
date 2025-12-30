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
    echo json_encode(['success' => false, 'message' => 'Phương thức không được phép']);
    exit();
}

$order_id = $_POST['order_id'] ?? null;
$user_id = $_SESSION['user_id'];

// Validate
if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
    exit();
}

try {
    // Kiểm tra đơn hàng có thuộc về user này không
    $check_sql = "SELECT order_id, order_status FROM orders WHERE order_id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $order_id, $user_id);
    $check_stmt->execute();
    $order = $check_stmt->get_result()->fetch_assoc();
    
    if (!$order) {
        throw new Exception("Không tìm thấy đơn hàng");
    }
    
    if ($order['order_status'] !== 'Chờ thanh toán') {
        throw new Exception("Đơn hàng này không cần upload chứng từ");
    }

    // Xử lý upload ảnh
    if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== 0) {
        throw new Exception("Vui lòng chọn ảnh chứng từ");
    }

    $upload_dir = 'uploads/payment_proofs/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $file_type = $_FILES['payment_proof']['type'];
    if (!in_array($file_type, $allowed_types)) {
        throw new Exception("Chỉ chấp nhận file ảnh (jpg, png, gif)");
    }

    // Validate file size (max 5MB)
    if ($_FILES['payment_proof']['size'] > 5 * 1024 * 1024) {
        throw new Exception("File ảnh quá lớn (tối đa 5MB)");
    }

    $file_extension = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
    $new_filename = 'payment_' . $order_id . '_' . time() . '.' . $file_extension;
    $payment_proof_path = $upload_dir . $new_filename;

    if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $payment_proof_path)) {
        throw new Exception("Không thể lưu file");
    }

    // Cập nhật đơn hàng
    $update_sql = "UPDATE orders SET payment_proof = ? WHERE order_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $payment_proof_path, $order_id);
    $update_stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Upload chứng từ thành công! Admin sẽ xác nhận thanh toán trong thời gian sớm nhất.',
        'file_path' => $payment_proof_path
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
