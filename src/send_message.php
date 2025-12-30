<?php
session_start();
require_once 'config/connect.php';
header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$message = trim($_POST['message'] ?? '');
$sender_type = $_POST['sender_type'] ?? '';
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : null;
$message_type = $order_id ? 'order' : 'text';

if (empty($message) && !$order_id) {
    echo json_encode(['success' => false, 'message' => 'Tin nhắn không được để trống']);
    exit();
}

try {
    if ($sender_type === 'user') {
        // User gửi tin nhắn cho admin
        $sender_id = $_SESSION['user_id'];
        $receiver_id = null; // Admin sẽ nhận được
        
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message, order_id, message_type) VALUES (?, ?, 'user', ?, ?, ?)");
        $stmt->bind_param("iisis", $sender_id, $receiver_id, $message, $order_id, $message_type);
        
    } elseif ($sender_type === 'admin') {
        // Admin gửi tin nhắn cho user
        $sender_id = $_SESSION['admin_id'];
        $receiver_id = (int)$_POST['receiver_id'];
        
        if (!$receiver_id) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy người nhận']);
            exit();
        }
        
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message, order_id, message_type) VALUES (?, ?, 'admin', ?, ?, ?)");
        $stmt->bind_param("iisis", $sender_id, $receiver_id, $message, $order_id, $message_type);
    } else {
        echo json_encode(['success' => false, 'message' => 'Loại người gửi không hợp lệ']);
        exit();
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Gửi tin nhắn thành công',
            'message_id' => $stmt->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi gửi tin nhắn: ' . $stmt->error]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>
