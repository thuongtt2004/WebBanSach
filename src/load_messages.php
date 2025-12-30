<?php
session_start();
require_once 'config/connect.php';
header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit();
}

try {
    $type = $_GET['type'] ?? 'user';
    
    if ($type === 'user') {
        // Load tin nhắn cho user
        $user_id = $_SESSION['user_id'];
        
        $query = "SELECT m.*, 
                  CASE 
                      WHEN m.sender_type = 'admin' THEN a.full_name
                      ELSE u.full_name
                  END as sender_name
                  FROM messages m
                  LEFT JOIN administrators a ON m.sender_id = a.admin_id AND m.sender_type = 'admin'
                  LEFT JOIN users u ON m.sender_id = u.user_id AND m.sender_type = 'user'
                  WHERE (m.sender_id = ? AND m.sender_type = 'user') 
                     OR (m.receiver_id = ? AND m.sender_type = 'admin')
                  ORDER BY m.created_at ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $user_id);
        
        // Đánh dấu đã đọc
        $mark_read = "UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_type = 'admin' AND is_read = 0";
        $read_stmt = $conn->prepare($mark_read);
        $read_stmt->bind_param("i", $user_id);
        $read_stmt->execute();
        
    } elseif ($type === 'admin') {
        // Load tin nhắn cho admin
        $user_id = (int)$_GET['user_id'];
        
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy user']);
            exit();
        }
        
        $query = "SELECT m.*, 
                  CASE 
                      WHEN m.sender_type = 'admin' THEN a.full_name
                      ELSE u.full_name
                  END as sender_name
                  FROM messages m
                  LEFT JOIN administrators a ON m.sender_id = a.admin_id AND m.sender_type = 'admin'
                  LEFT JOIN users u ON m.sender_id = u.user_id AND m.sender_type = 'user'
                  WHERE (m.sender_id = ? AND m.sender_type = 'user') 
                     OR (m.receiver_id = ? AND m.sender_type = 'admin')
                  ORDER BY m.created_at ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $user_id);
        
        // Đánh dấu đã đọc
        $mark_read = "UPDATE messages SET is_read = 1 WHERE sender_id = ? AND sender_type = 'user' AND is_read = 0";
        $read_stmt = $conn->prepare($mark_read);
        $read_stmt->bind_param("i", $user_id);
        $read_stmt->execute();
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'message_id' => $row['message_id'],
            'sender_id' => $row['sender_id'],
            'sender_type' => $row['sender_type'],
            'sender_name' => $row['sender_name'],
            'message' => $row['message'],
            'time' => date('H:i d/m/Y', strtotime($row['created_at'])),
            'is_read' => $row['is_read']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'count' => count($messages)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>
