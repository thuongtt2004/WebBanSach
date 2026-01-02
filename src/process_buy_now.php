<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);

session_start();
require_once 'config/connect.php';
require_once 'includes/email_helper.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Kiểm tra user_id có tồn tại trong database
$check_user = "SELECT user_id FROM users WHERE user_id = ?";
$stmt_check = $conn->prepare($check_user);
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Tài khoản không hợp lệ']);
    exit();
}

// Lấy thông tin từ form
$product_id = trim($_POST['product_id'] ?? '');
$quantity = intval($_POST['quantity'] ?? 1);
$price = floatval($_POST['price'] ?? 0);
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? 'cod');

// Validate dữ liệu
if (empty($product_id) || empty($full_name) || empty($email) || empty($phone) || empty($address)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
    exit();
}

// Kiểm tra sản phẩm
$product_query = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    exit();
}

// Kiểm tra tồn kho
if ($product['stock_quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không đủ số lượng']);
    exit();
}

try {
    // Tính tổng tiền
    $total_amount = $price * $quantity;
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    // Tạo đơn hàng mới
    $order_query = "INSERT INTO orders (user_id, order_status, payment_method, total_amount, customer_name, customer_email, customer_phone, shipping_address, notes, created_at) 
                    VALUES (?, 'Chờ xác nhận', ?, ?, ?, ?, ?, ?, ?, NOW())";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("isdssssss", $user_id, $payment_method, $total_amount, $full_name, $email, $phone, $address, $notes);
    
    if (!$order_stmt->execute()) {
        throw new Exception('Không thể tạo đơn hàng');
    }
    
    $order_id = $conn->insert_id;
    
    // Thêm chi tiết đơn hàng
    $detail_query = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $detail_stmt = $conn->prepare($detail_query);
    $detail_stmt->bind_param("isid", $order_id, $product_id, $quantity, $price);
    
    if (!$detail_stmt->execute()) {
        throw new Exception('Không thể thêm chi tiết đơn hàng');
    }
    
    // Inventory sẽ được trừ khi khách hàng xác nhận hoàn thành đơn hàng
    // Không trừ inventory ngay khi đặt hàng
    
    // Commit transaction
    $conn->commit();
    
    // Gửi email xác nhận đơn hàng
    $email_items = [[
        'product_name' => $product['product_name'],
        'quantity' => $quantity,
        'price' => $price
    ]];
    
    $email_sent = send_order_confirmation_email(
        $email,
        $full_name,
        $order_id,
        $total_amount,
        $payment_method,
        $email_items
    );
    
    $message = 'Đặt hàng thành công';
    if ($email_sent) {
        $message .= '. Email xác nhận đã được gửi đến ' . $email;
    }
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'payment_method' => $payment_method,
        'message' => $message
    ]);
    
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
