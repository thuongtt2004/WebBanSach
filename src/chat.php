<?php
require_once 'config/connect.php';
require_once 'session_init.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login_page.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy order_id nếu có
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
$order_info = null;

// Nếu có order_id, lấy thông tin đơn hàng
if ($order_id) {
    $order_query = "SELECT o.*, 
                    (SELECT GROUP_CONCAT(CONCAT(p.product_name, ' (x', od.quantity, ')') SEPARATOR ', ')
                     FROM order_details od 
                     JOIN products p ON od.product_id = p.product_id 
                     WHERE od.order_id = o.order_id) as products
                    FROM orders o
                    WHERE o.order_id = ? AND o.user_id = ?";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("ii", $order_id, $user_id);
    $order_stmt->execute();
    $order_info = $order_stmt->get_result()->fetch_assoc();
}

// Lấy thông tin user
$user_query = "SELECT username, full_name FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_info = $user_stmt->get_result()->fetch_assoc();

// Lấy tin nhắn giữa user và admin
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
$msg_stmt->bind_param("ii", $user_id, $user_id);
$msg_stmt->execute();
$messages = $msg_stmt->get_result();

// Đánh dấu tin nhắn từ admin đã đọc
$mark_read = "UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_type = 'admin' AND is_read = 0";
$read_stmt = $conn->prepare($mark_read);
$read_stmt->bind_param("i", $user_id);
$read_stmt->execute();

require_once 'header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat với Admin - TTHUONG Store</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/chat.css">
    <link rel="stylesheet" href="css/fontawesome/all.min.css">
</head>
<body>

    <div class="chat-container">
        <div class="chat-header">
            <div class="chat-info">
                <i class="fas fa-headset"></i>
                <div>
                    <h3>Hỗ trợ khách hàng</h3>
                    <p>Chúng tôi luôn sẵn sàng hỗ trợ bạn</p>
                </div>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <?php if ($order_info): ?>
                <!-- Hiển thị card đơn hàng -->
                <div class="order-card-message" style="background:#f8f9fa;border:2px solid #007bff;border-radius:12px;padding:20px;margin:10px 0;max-width:600px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:15px;border-bottom:2px solid #007bff;padding-bottom:10px;">
                        <i class="fas fa-shopping-bag" style="color:#007bff;font-size:24px;"></i>
                        <div>
                            <h4 style="margin:0;color:#007bff;">Đơn hàng #<?php echo $order_info['order_id']; ?></h4>
                            <p style="margin:3px 0 0 0;font-size:13px;color:#666;">
                                Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order_info['order_date'])); ?>
                            </p>
                        </div>
                    </div>
                    <div style="margin-bottom:10px;">
                        <p style="margin:5px 0;"><strong>Trạng thái:</strong> 
                            <span style="background:#007bff;color:white;padding:4px 12px;border-radius:12px;font-size:13px;">
                                <?php echo $order_info['order_status']; ?>
                            </span>
                        </p>
                        <p style="margin:5px 0;"><strong>Người nhận:</strong> <?php echo htmlspecialchars($order_info['full_name']); ?></p>
                        <p style="margin:5px 0;"><strong>SĐT:</strong> <?php echo htmlspecialchars($order_info['phone']); ?></p>
                        <p style="margin:5px 0;"><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order_info['address']); ?></p>
                        <p style="margin:5px 0;"><strong>Sản phẩm:</strong><br>
                            <span style="color:#666;font-size:14px;"><?php echo htmlspecialchars($order_info['products']); ?></span>
                        </p>
                        <p style="margin:5px 0;"><strong>Tổng tiền:</strong> 
                            <span style="color:#dc3545;font-weight:600;font-size:16px;">
                                <?php echo number_format($order_info['total_amount'], 0, ',', '.'); ?> VNĐ
                            </span>
                        </p>
                    </div>
                    <p style="background:#e7f3ff;padding:10px;border-radius:6px;margin:10px 0 0 0;font-size:13px;color:#004085;">
                        <i class="fas fa-info-circle"></i> Bạn có thể chat với chúng tôi về đơn hàng này
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if ($messages->num_rows > 0): ?>
                <?php while ($msg = $messages->fetch_assoc()): ?>
                    <div class="message <?php echo $msg['sender_type'] == 'user' ? 'message-user' : 'message-admin'; ?>">
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
                            
                            <?php if ($msg['message_type'] == 'order' && $msg['order_id']): ?>
                                <!-- Hiển thị tin nhắn dạng đơn hàng -->
                                <?php
                                $msg_order_query = "SELECT o.*, 
                                    (SELECT GROUP_CONCAT(CONCAT(p.product_name, ' (x', od.quantity, ')') SEPARATOR ', ')
                                     FROM order_details od 
                                     JOIN products p ON od.product_id = p.product_id 
                                     WHERE od.order_id = o.order_id) as products
                                    FROM orders o WHERE o.order_id = ?";
                                $msg_order_stmt = $conn->prepare($msg_order_query);
                                $msg_order_stmt->bind_param("i", $msg['order_id']);
                                $msg_order_stmt->execute();
                                $msg_order = $msg_order_stmt->get_result()->fetch_assoc();
                                if ($msg_order):
                                ?>
                                <div style="background:#f0f0f0;border-left:3px solid #007bff;padding:12px;border-radius:6px;margin-top:5px;">
                                    <div style="font-weight:600;color:#007bff;margin-bottom:5px;">
                                        <i class="fas fa-shopping-bag"></i> Đơn hàng #<?php echo $msg_order['order_id']; ?>
                                    </div>
                                    <div style="font-size:13px;color:#666;">
                                        Tổng: <?php echo number_format($msg_order['total_amount'], 0, ',', '.'); ?> VNĐ
                                    </div>
                                    <div style="font-size:13px;color:#666;">
                                        <?php echo htmlspecialchars($msg_order['products']); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if ($msg['message']): ?>
                                <div class="message-text"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php elseif (!$order_info): ?>
                <div class="empty-chat">
                    <i class="fas fa-comments"></i>
                    <p>Chưa có tin nhắn nào. Hãy gửi tin nhắn đầu tiên!</p>
                </div>
            <?php endif; ?>
        </div>

        <form class="chat-input-form" id="chatForm">
            <textarea 
                id="messageInput" 
                placeholder="Nhập tin nhắn của bạn..."
                rows="3"
                required
            ></textarea>
            <button type="submit" class="btn-send">
                <i class="fas fa-paper-plane"></i> Gửi
            </button>
        </form>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        const userId = <?php echo $user_id; ?>;
        const orderId = <?php echo $order_id ? $order_id : 'null'; ?>;
        const chatMessages = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        
        // Lưu order info nếu có
        const orderInfo = <?php echo $order_info ? json_encode($order_info) : 'null'; ?>;

        // Auto scroll to bottom
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        scrollToBottom();

        // Gửi tin nhắn
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message) return;

            try {
                const formData = new FormData();
                formData.append('message', message);
                formData.append('sender_type', 'user');
                
                // Gửi kèm order_id nếu có
                if (orderId) {
                    formData.append('order_id', orderId);
                }

                const response = await fetch('send_message.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    messageInput.value = '';
                    loadMessages();
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi tin nhắn');
            }
        });

        // Load tin nhắn mới
        async function loadMessages() {
            try {
                const response = await fetch('load_messages.php');
                const result = await response.json();
                
                if (result.success && result.messages) {
                    updateMessages(result.messages);
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }

        // Cập nhật tin nhắn
        function updateMessages(messages) {
            console.log('Updating messages, orderInfo:', orderInfo);
            console.log('Messages count:', messages.length);
            
            // Lưu vị trí scroll hiện tại
            const currentScroll = chatMessages.scrollTop;
            const isAtBottom = chatMessages.scrollHeight - chatMessages.clientHeight <= currentScroll + 1;
            
            let html = '';
            
            // Luôn thêm order card nếu có orderInfo
            if (orderInfo) {
                html += `
                    <div class="order-card-message" style="background:#f8f9fa;border:2px solid #007bff;border-radius:12px;padding:20px;margin:10px 0;max-width:600px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:15px;border-bottom:2px solid #007bff;padding-bottom:10px;">
                            <i class="fas fa-shopping-bag" style="color:#007bff;font-size:24px;"></i>
                            <div>
                                <h4 style="margin:0;color:#007bff;">Đơn hàng #${orderInfo.order_id}</h4>
                                <p style="margin:3px 0 0 0;font-size:13px;color:#666;">
                                    Ngày đặt: ${new Date(orderInfo.order_date).toLocaleString('vi-VN')}
                                </p>
                            </div>
                        </div>
                        <div style="margin-bottom:10px;">
                            <p style="margin:5px 0;"><strong>Trạng thái:</strong> 
                                <span style="background:#007bff;color:white;padding:4px 12px;border-radius:12px;font-size:13px;">
                                    ${orderInfo.order_status}
                                </span>
                            </p>
                            <p style="margin:5px 0;"><strong>Người nhận:</strong> ${orderInfo.full_name}</p>
                            <p style="margin:5px 0;"><strong>SĐT:</strong> ${orderInfo.phone}</p>
                            <p style="margin:5px 0;"><strong>Địa chỉ:</strong> ${orderInfo.address}</p>
                            <p style="margin:5px 0;"><strong>Sản phẩm:</strong><br>
                                <span style="color:#666;font-size:14px;">${orderInfo.products}</span>
                            </p>
                            <p style="margin:5px 0;"><strong>Tổng tiền:</strong> 
                                <span style="color:#dc3545;font-weight:600;font-size:16px;">
                                    ${new Intl.NumberFormat('vi-VN').format(orderInfo.total_amount)} VNĐ
                                </span>
                            </p>
                        </div>
                        <p style="background:#e7f3ff;padding:10px;border-radius:6px;margin:10px 0 0 0;font-size:13px;color:#004085;">
                            <i class="fas fa-info-circle"></i> Bạn có thể chat với chúng tôi về đơn hàng này
                        </p>
                    </div>
                `;
            }
            
            if (messages.length === 0) {
                if (!orderInfo) {
                    html = `
                        <div class="empty-chat">
                            <i class="fas fa-comments"></i>
                            <p>Chưa có tin nhắn nào. Hãy gửi tin nhắn đầu tiên!</p>
                        </div>
                    `;
                }
                chatMessages.innerHTML = html;
                if (isAtBottom) scrollToBottom();
                return;
            }
            
            // Thêm các tin nhắn
            html += messages.map(msg => `
                <div class="message ${msg.sender_type == 'user' ? 'message-user' : 'message-admin'}">
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
            
            chatMessages.innerHTML = html;
            
            // Chỉ scroll xuống nếu đang ở cuối
            if (isAtBottom) {
                scrollToBottom();
            }
        }

        // Tắt auto reload để giữ order card
        // setInterval(loadMessages, 3000);

        // Enter để gửi, Shift+Enter để xuống dòng
        messageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>
