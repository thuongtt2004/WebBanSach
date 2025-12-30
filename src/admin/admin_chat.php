<?php
session_start();
require_once '../config/connect.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_page.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Lấy danh sách user đã chat
$users_query = "SELECT DISTINCT 
                    u.user_id, 
                    u.username, 
                    u.full_name,
                    (SELECT COUNT(*) FROM messages 
                     WHERE sender_id = u.user_id 
                     AND sender_type = 'user' 
                     AND is_read = 0) as unread_count,
                    (SELECT created_at FROM messages 
                     WHERE (sender_id = u.user_id AND sender_type = 'user') 
                        OR (receiver_id = u.user_id AND sender_type = 'admin')
                     ORDER BY created_at DESC LIMIT 1) as last_message_time
                FROM users u
                WHERE EXISTS (
                    SELECT 1 FROM messages m 
                    WHERE (m.sender_id = u.user_id AND m.sender_type = 'user')
                       OR (m.receiver_id = u.user_id AND m.sender_type = 'admin')
                )
                ORDER BY last_message_time DESC";
$users_result = $conn->query($users_query);

// Lấy user_id được chọn
$selected_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

// Nếu có user được chọn, lấy tin nhắn
$messages = [];
$order_messages = []; // Lưu thông tin đơn hàng từ tin nhắn
if ($selected_user_id) {
    $messages_query = "SELECT m.*, 
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
    $msg_stmt = $conn->prepare($messages_query);
    $msg_stmt->bind_param("ii", $selected_user_id, $selected_user_id);
    $msg_stmt->execute();
    $messages_result = $msg_stmt->get_result();
    
    while ($row = $messages_result->fetch_assoc()) {
        $messages[] = $row;
        
        // Nếu có order_id, lấy thông tin đơn hàng
        if ($row['order_id'] && !isset($order_messages[$row['order_id']])) {
            $order_query = "SELECT o.*, 
                           (SELECT GROUP_CONCAT(CONCAT(p.product_name, ' (x', od.quantity, ')') SEPARATOR ', ')
                            FROM order_details od 
                            JOIN products p ON od.product_id = p.product_id 
                            WHERE od.order_id = o.order_id) as products
                           FROM orders o WHERE o.order_id = ?";
            $order_stmt = $conn->prepare($order_query);
            $order_stmt->bind_param("i", $row['order_id']);
            $order_stmt->execute();
            $order_info = $order_stmt->get_result()->fetch_assoc();
            if ($order_info) {
                $order_messages[$row['order_id']] = $order_info;
            }
        }
    }
    
    // Đánh dấu tin nhắn đã đọc
    $mark_read = "UPDATE messages SET is_read = 1 WHERE sender_id = ? AND sender_type = 'user' AND is_read = 0";
    $read_stmt = $conn->prepare($mark_read);
    $read_stmt->bind_param("i", $selected_user_id);
    $read_stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Chat - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/admin_chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <main>
        <div class="admin-chat-container">
            <h2><i class="fas fa-comments"></i> Quản Lý Chat</h2>
            
            <div class="chat-layout">
                <!-- Danh sách user -->
                <div class="users-list">
                    <div class="users-header">
                        <h3>Người dùng</h3>
                        <span class="users-count"><?php echo $users_result->num_rows; ?></span>
                    </div>
                    <div style="padding: 10px;">
                        <div style="position: relative;">
                            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                            <input type="text" 
                                   id="searchUserInput" 
                                   placeholder="Tìm kiếm người dùng..." 
                                   style="width: 100%; padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none;"
                                   oninput="filterUsers()">
                        </div>
                    </div>
                    <div class="users-content">
                        <?php if ($users_result->num_rows > 0): ?>
                            <?php while ($user = $users_result->fetch_assoc()): ?>
                                <a href="?user_id=<?php echo $user['user_id']; ?>" 
                                   class="user-item <?php echo $selected_user_id == $user['user_id'] ? 'active' : ''; ?>">
                                    <div class="user-avatar">
                                        <i class="fas fa-user"></i>
                                        <?php if ($user['unread_count'] > 0): ?>
                                            <span class="unread-badge"><?php echo $user['unread_count']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="user-info">
                                        <div class="user-name"><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></div>
                                        <div class="user-time">
                                            <?php 
                                            if ($user['last_message_time']) {
                                                $time = strtotime($user['last_message_time']);
                                                $now = time();
                                                $diff = $now - $time;
                                                
                                                if ($diff < 60) {
                                                    echo 'Vừa xong';
                                                } elseif ($diff < 3600) {
                                                    echo floor($diff / 60) . ' phút trước';
                                                } elseif ($diff < 86400) {
                                                    echo floor($diff / 3600) . ' giờ trước';
                                                } else {
                                                    echo date('d/m/Y', $time);
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="empty-users">
                                <i class="fas fa-inbox"></i>
                                <p>Chưa có tin nhắn nào</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Khung chat -->
                <div class="chat-area">
                    <?php if ($selected_user_id): ?>
                        <?php
                        // Lấy thông tin user được chọn
                        $user_info_query = "SELECT username, full_name FROM users WHERE user_id = ?";
                        $user_info_stmt = $conn->prepare($user_info_query);
                        $user_info_stmt->bind_param("i", $selected_user_id);
                        $user_info_stmt->execute();
                        $selected_user = $user_info_stmt->get_result()->fetch_assoc();
                        ?>
                        
                        <div class="chat-header-info">
                            <div class="chat-user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h3><?php echo htmlspecialchars($selected_user['full_name'] ?: $selected_user['username']); ?></h3>
                                <p>@<?php echo htmlspecialchars($selected_user['username']); ?></p>
                            </div>
                        </div>

                        <div class="chat-messages" id="chatMessages">
                            <?php 
                            // Hiển thị order cards nếu có
                            if (!empty($order_messages)):
                                foreach ($order_messages as $order_id => $order):
                            ?>
                                <div class="order-card-message" style="background:#f8f9fa;border:2px solid #007bff;border-radius:12px;padding:20px;margin:10px 0;max-width:600px;">
                                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:15px;border-bottom:2px solid #007bff;padding-bottom:10px;">
                                        <i class="fas fa-shopping-bag" style="color:#007bff;font-size:24px;"></i>
                                        <div>
                                            <h4 style="margin:0;color:#007bff;">Đơn hàng #<?php echo $order['order_id']; ?></h4>
                                            <p style="margin:3px 0 0 0;font-size:13px;color:#666;">
                                                Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div style="margin-bottom:10px;">
                                        <p style="margin:5px 0;"><strong>Trạng thái:</strong> 
                                            <span style="background:#007bff;color:white;padding:4px 12px;border-radius:12px;font-size:13px;">
                                                <?php echo $order['order_status']; ?>
                                            </span>
                                        </p>
                                        <p style="margin:5px 0;"><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                                        <p style="margin:5px 0;"><strong>SĐT:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                                        <p style="margin:5px 0;"><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                                        <p style="margin:5px 0;"><strong>Sản phẩm:</strong><br>
                                            <span style="color:#666;font-size:14px;"><?php echo htmlspecialchars($order['products']); ?></span>
                                        </p>
                                        <p style="margin:5px 0;"><strong>Tổng tiền:</strong> 
                                            <span style="color:#dc3545;font-weight:600;font-size:16px;">
                                                <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ
                                            </span>
                                        </p>
                                    </div>
                                    <p style="background:#e7f3ff;padding:10px;border-radius:6px;margin:10px 0 0 0;font-size:13px;color:#004085;">
                                        <i class="fas fa-info-circle"></i> Khách hàng đang hỏi về đơn hàng này
                                    </p>
                                </div>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                            
                            <?php foreach ($messages as $msg): ?>
                                <div class="message <?php echo $msg['sender_type'] == 'admin' ? 'message-admin' : 'message-user'; ?>">
                                    <div class="message-avatar">
                                        <?php if ($msg['sender_type'] == 'admin'): ?>
                                            <i class="fas fa-user-shield"></i>
                                        <?php else: ?>
                                            <i class="fas fa-user"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="message-content">
                                        <div class="message-header">
                                            <span class="message-sender"><?php echo htmlspecialchars($msg['sender_name']); ?></span>
                                            <span class="message-time"><?php echo date('H:i d/m/Y', strtotime($msg['created_at'])); ?></span>
                                        </div>
                                        <div class="message-text"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <form class="chat-input-form" id="chatForm">
                            <input type="hidden" id="receiverId" value="<?php echo $selected_user_id; ?>">
                            <textarea 
                                id="messageInput" 
                                placeholder="Nhập tin nhắn..."
                                rows="2"
                                required
                            ></textarea>
                            <button type="submit" class="btn-send">
                                <i class="fas fa-paper-plane"></i> Gửi
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="no-chat-selected">
                            <i class="fas fa-comments"></i>
                            <p>Chọn một người dùng để bắt đầu chat</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'admin_footer.php'; ?>

    <script>
        const chatMessages = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const receiverId = document.getElementById('receiverId')?.value;

        // Auto scroll
        function scrollToBottom() {
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }
        scrollToBottom();

        // Gửi tin nhắn
        if (chatForm) {
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const message = messageInput.value.trim();
                if (!message || !receiverId) return;

                try {
                    const formData = new FormData();
                    formData.append('message', message);
                    formData.append('receiver_id', receiverId);
                    formData.append('sender_type', 'admin');

                    const response = await fetch('send_message.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        messageInput.value = '';
                        loadMessages();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        }

        // Load tin nhắn
        async function loadMessages() {
            if (!receiverId) return;
            
            try {
                const response = await fetch(`load_messages.php?user_id=${receiverId}&type=admin`);
                const result = await response.json();
                
                if (result.success && result.messages) {
                    updateMessages(result.messages);
                    scrollToBottom();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function updateMessages(messages) {
            if (!chatMessages) return;
            
            chatMessages.innerHTML = messages.map(msg => `
                <div class="message ${msg.sender_type == 'admin' ? 'message-admin' : 'message-user'}">
                    <div class="message-avatar">
                        ${msg.sender_type == 'admin' ? '<i class="fas fa-user-shield"></i>' : '<i class="fas fa-user"></i>'}
                    </div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-sender">${msg.sender_name}</span>
                            <span class="message-time">${msg.time}</span>
                        </div>
                        <div class="message-text">${msg.message.replace(/\n/g, '<br>')}</div>
                    </div>
                </div>
            `).join('');
        }

        // Auto reload
        if (receiverId) {
            setInterval(loadMessages, 3000);
        }

        // Reload danh sách user
        setInterval(() => {
            location.reload();
        }, 30000);
        
        // Tìm kiếm người dùng
        function filterUsers() {
            const searchTerm = document.getElementById('searchUserInput').value.toLowerCase();
            const userItems = document.querySelectorAll('.user-item');
            let visibleCount = 0;
            
            userItems.forEach(item => {
                const userName = item.querySelector('.user-name').textContent.toLowerCase();
                const isMatch = userName.includes(searchTerm);
                item.style.display = isMatch ? 'flex' : 'none';
                if (isMatch) visibleCount++;
            });
            
            // Cập nhật số lượng hiển thị
            const usersCount = document.querySelector('.users-count');
            const totalUsers = userItems.length;
            if (searchTerm) {
                usersCount.textContent = `${visibleCount}/${totalUsers}`;
            } else {
                usersCount.textContent = totalUsers;
            }
        }
    </script>
</body>
</html>
