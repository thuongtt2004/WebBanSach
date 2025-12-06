<?php
session_start();
require_once 'config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để mua hàng';
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; // Lưu URL để quay lại sau khi đăng nhập
    header('Location: login_page.php');
    exit();
}

// Kiểm tra product_id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'Sản phẩm không hợp lệ';
    header('Location: products.php');
    exit();
}

$product_id = trim($_GET['id']); // Giữ nguyên kiểu string
$user_id = intval($_SESSION['user_id']);

// Lấy thông tin sản phẩm
$product_query = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($product_query);
if (!$stmt) {
    $_SESSION['error'] = 'Lỗi hệ thống: ' . $conn->error;
    header('Location: products.php');
    exit();
}

$stmt->bind_param("s", $product_id); // Dùng "s" cho string thay vì "i"
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    $_SESSION['error'] = 'Sản phẩm không tồn tại';
    header('Location: products.php');
    exit();
}

// Kiểm tra tồn kho
if ($product['stock_quantity'] < 1) {
    $_SESSION['error'] = 'Sản phẩm đã hết hàng';
    header('Location: products.php?id=' . $product_id);
    exit();
}

try {
    // Tạo đơn hàng trực tiếp (không qua giỏ hàng)
    // Lấy thông tin người dùng
    $user_query = "SELECT * FROM users WHERE user_id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user = $user_stmt->get_result()->fetch_assoc();
    
    if (!$user) {
        throw new Exception('Không tìm thấy thông tin người dùng');
    }
    
    // Tính tổng tiền
    $total_amount = $product['price'] * 1; // Số lượng = 1
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    // Tạo đơn hàng mới
    $order_query = "INSERT INTO orders (user_id, order_status, payment_method, total_amount, created_at) VALUES (?, 'Chờ xác nhận', 'COD', ?, NOW())";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("id", $user_id, $total_amount);
    
    if (!$order_stmt->execute()) {
        throw new Exception('Không thể tạo đơn hàng');
    }
    
    $order_id = $conn->insert_id;
    
    // Thêm chi tiết đơn hàng
    $detail_query = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, 1, ?)";
    $detail_stmt = $conn->prepare($detail_query);
    $detail_stmt->bind_param("isd", $order_id, $product_id, $product['price']);
    
    if (!$detail_stmt->execute()) {
        throw new Exception('Không thể thêm chi tiết đơn hàng');
    }
    
    // Cập nhật số lượng tồn kho và đã bán
    $update_stock = "UPDATE products SET stock_quantity = stock_quantity - 1, sold_quantity = sold_quantity + 1 WHERE product_id = ?";
    $stock_stmt = $conn->prepare($update_stock);
    $stock_stmt->bind_param("s", $product_id);
    
    if (!$stock_stmt->execute()) {
        throw new Exception('Không thể cập nhật tồn kho');
    }
    
    // Commit transaction
    $conn->commit();
    
    $_SESSION['success'] = 'Đặt hàng thành công! Mã đơn hàng: #' . $order_id;
    
    // Chuyển đến trang theo dõi đơn hàng
    header('Location: track_order.php?order_id=' . $order_id);
    exit();
    
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
    header('Location: products.php?id=' . $product_id);
    exit();
}
?>
