<?php
session_start();
require_once 'config/connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$product_id = $input['product_id'] ?? '';
$action = $input['action'] ?? 'toggle'; // toggle, add, remove

if (empty($product_id)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin sản phẩm']);
    exit();
}

try {
    if ($action === 'remove') {
        // Xóa khỏi wishlist
        $delete_sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("is", $user_id, $product_id);
        $delete_stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Đã xóa khỏi danh sách yêu thích',
            'in_wishlist' => false
        ]);
        
    } elseif ($action === 'add') {
        // Thêm vào wishlist
        $insert_sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("is", $user_id, $product_id);
        
        if ($insert_stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Đã thêm vào danh sách yêu thích',
                'in_wishlist' => true
            ]);
        } else {
            // Có thể đã tồn tại
            echo json_encode([
                'success' => false,
                'message' => 'Sản phẩm đã có trong danh sách yêu thích'
            ]);
        }
        
    } else {
        // Toggle - kiểm tra và thêm/xóa
        $check_sql = "SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("is", $user_id, $product_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Đã có -> Xóa
            $delete_sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("is", $user_id, $product_id);
            $delete_stmt->execute();
            
            echo json_encode([
                'success' => true,
                'message' => 'Đã xóa khỏi danh sách yêu thích',
                'in_wishlist' => false
            ]);
        } else {
            // Chưa có -> Thêm
            $insert_sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("is", $user_id, $product_id);
            $insert_stmt->execute();
            
            echo json_encode([
                'success' => true,
                'message' => 'Đã thêm vào danh sách yêu thích',
                'in_wishlist' => true
            ]);
        }
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
    ]);
}

$conn->close();
