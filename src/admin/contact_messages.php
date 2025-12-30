<?php
session_start();
require_once '../config/db.php';

/** @var mysqli $conn */

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle delete message
if (isset($_POST['delete_message'])) {
    $message_id = $_POST['message_id'];
    $delete_sql = "DELETE FROM contact_messages WHERE message_id = ?";
    $stmt = $conn->prepare($delete_sql);
    if ($stmt) {
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
    }
    header('Location: contact_messages.php?deleted=1');
    exit();
}

// Handle mark as read
if (isset($_POST['mark_read'])) {
    $message_id = $_POST['message_id'];
    $update_sql = "UPDATE contact_messages SET status = 'read' WHERE message_id = ?";
    $stmt = $conn->prepare($update_sql);
    if ($stmt) {
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
    }
}

// Handle reply
if (isset($_POST['send_reply'])) {
    $message_id = $_POST['message_id'];
    $reply_message = trim($_POST['reply_message']);
    
    if (!empty($reply_message)) {
        $reply_sql = "UPDATE contact_messages SET status = 'replied', reply_message = ?, replied_at = NOW() WHERE message_id = ?";
        $stmt = $conn->prepare($reply_sql);
        if ($stmt) {
            $stmt->bind_param("si", $reply_message, $message_id);
            $stmt->execute();
            $success_message = "Đã gửi phản hồi thành công!";
        }
    }
}

// Filter by status
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$where_clauses = [];
$params = [];
$types = '';

if ($status_filter !== 'all') {
    $where_clauses[] = "status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($search)) {
    $where_clauses[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ssss';
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Get total messages count
$count_sql = "SELECT COUNT(*) as total FROM contact_messages $where_sql";
if (!empty($params)) {
    $stmt = $conn->prepare($count_sql);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $count_result = $stmt->get_result();
    }
} else {
    $count_result = $conn->query($count_sql);
}
if ($count_result) {
    $total_messages = $count_result->fetch_assoc()['total'];
} else {
    $total_messages = 0;
}

// Get messages
$sql = "SELECT * FROM contact_messages $where_sql ORDER BY created_at DESC";
if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    }
} else {
    $result = $conn->query($sql);
}

// Get counts by status
$status_counts = [
    'new' => 0,
    'read' => 0,
    'replied' => 0
];

$count_sql = "SELECT status, COUNT(*) as count FROM contact_messages GROUP BY status";
$count_result = $conn->query($count_sql);
while ($row = $count_result->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tin nhắn liên hệ</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-envelope"></i> Quản lý tin nhắn liên hệ</h1>
                <div class="header-stats">
                    <span class="stat-badge new"><?php echo $status_counts['new']; ?> mới</span>
                    <span class="stat-badge read"><?php echo $status_counts['read']; ?> đã đọc</span>
                    <span class="stat-badge replied"><?php echo $status_counts['replied']; ?> đã trả lời</span>
                </div>
            </div>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">Đã xóa tin nhắn thành công!</div>
            <?php endif; ?>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="filters-bar">
                <form method="GET" action="" class="filter-form">
                    <div class="filter-tabs">
                        <a href="?status=all" class="filter-tab <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                            Tất cả (<?php echo $total_messages; ?>)
                        </a>
                        <a href="?status=new" class="filter-tab <?php echo $status_filter === 'new' ? 'active' : ''; ?>">
                            Mới (<?php echo $status_counts['new']; ?>)
                        </a>
                        <a href="?status=read" class="filter-tab <?php echo $status_filter === 'read' ? 'active' : ''; ?>">
                            Đã đọc (<?php echo $status_counts['read']; ?>)
                        </a>
                        <a href="?status=replied" class="filter-tab <?php echo $status_filter === 'replied' ? 'active' : ''; ?>">
                            Đã trả lời (<?php echo $status_counts['replied']; ?>)
                        </a>
                    </div>
                    
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Tìm kiếm theo tên, email, chủ đề..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>

            <!-- Messages List -->
            <?php if ($result->num_rows > 0): ?>
                <div class="messages-list">
                    <?php while ($message = $result->fetch_assoc()): ?>
                        <div class="message-card status-<?php echo $message['status']; ?>">
                            <div class="message-header">
                                <div class="sender-info">
                                    <h3><?php echo htmlspecialchars($message['name']); ?></h3>
                                    <div class="contact-details">
                                        <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($message['email']); ?></span>
                                        <?php if ($message['phone']): ?>
                                            <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($message['phone']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="message-meta">
                                    <span class="status-badge status-<?php echo $message['status']; ?>">
                                        <?php 
                                        echo $message['status'] === 'new' ? 'Mới' : 
                                             ($message['status'] === 'read' ? 'Đã đọc' : 'Đã trả lời'); 
                                        ?>
                                    </span>
                                    <span class="message-date">
                                        <i class="fas fa-clock"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="message-subject">
                                <strong>Chủ đề:</strong> <?php echo htmlspecialchars($message['subject']); ?>
                            </div>

                            <div class="message-content">
                                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                            </div>

                            <?php if ($message['status'] === 'replied' && $message['reply_message']): ?>
                                <div class="reply-section">
                                    <strong><i class="fas fa-reply"></i> Phản hồi của bạn:</strong>
                                    <p><?php echo nl2br(htmlspecialchars($message['reply_message'])); ?></p>
                                    <small>Đã trả lời lúc: <?php echo date('d/m/Y H:i', strtotime($message['replied_at'])); ?></small>
                                </div>
                            <?php endif; ?>

                            <div class="message-actions">
                                <?php if ($message['status'] === 'new'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="message_id" value="<?php echo $message['message_id']; ?>">
                                        <button type="submit" name="mark_read" class="btn btn-read">
                                            <i class="fas fa-check"></i> Đánh dấu đã đọc
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($message['status'] !== 'replied'): ?>
                                    <button onclick="showReplyForm(<?php echo $message['message_id']; ?>)" class="btn btn-reply">
                                        <i class="fas fa-reply"></i> Trả lời
                                    </button>
                                <?php endif; ?>

                                <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa tin nhắn này?');">
                                    <input type="hidden" name="message_id" value="<?php echo $message['message_id']; ?>">
                                    <button type="submit" name="delete_message" class="btn btn-delete">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>

                            <!-- Reply Form (Hidden by default) -->
                            <div id="reply-form-<?php echo $message['message_id']; ?>" class="reply-form" style="display: none;">
                                <form method="POST">
                                    <input type="hidden" name="message_id" value="<?php echo $message['message_id']; ?>">
                                    <label><strong>Phản hồi tới <?php echo htmlspecialchars($message['name']); ?>:</strong></label>
                                    <textarea name="reply_message" rows="4" placeholder="Nhập nội dung phản hồi..." required></textarea>
                                    <div class="reply-actions">
                                        <button type="submit" name="send_reply" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Gửi phản hồi
                                        </button>
                                        <button type="button" onclick="hideReplyForm(<?php echo $message['message_id']; ?>)" class="btn btn-secondary">
                                            Hủy
                                        </button>
                                    </div>
                                    <small class="note">* Lưu ý: Phản hồi sẽ được lưu trong hệ thống. Bạn cần gửi email riêng cho khách hàng.</small>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Chưa có tin nhắn nào</h3>
                    <p>Các tin nhắn liên hệ từ khách hàng sẽ hiển thị ở đây</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-stats {
            display: flex;
            gap: 10px;
        }

        .stat-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .stat-badge.new {
            background: #fff3cd;
            color: #856404;
        }

        .stat-badge.read {
            background: #d1ecf1;
            color: #0c5460;
        }

        .stat-badge.replied {
            background: #d4edda;
            color: #155724;
        }

        .filters-bar {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }

        .filter-tab {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            transition: all 0.3s;
        }

        .filter-tab:hover {
            background: #f5f5f5;
            color: #333;
        }

        .filter-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .search-box {
            display: flex;
            gap: 10px;
        }

        .search-box input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
        }

        .search-box button {
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .messages-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .message-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid #e0e0e0;
        }

        .message-card.status-new {
            border-left-color: #ffc107;
            background: #fffef5;
        }

        .message-card.status-read {
            border-left-color: #17a2b8;
        }

        .message-card.status-replied {
            border-left-color: #28a745;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .sender-info h3 {
            margin: 0 0 8px 0;
            font-size: 18px;
            color: #333;
        }

        .contact-details {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: #666;
        }

        .message-meta {
            text-align: right;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.status-new {
            background: #ffc107;
            color: #856404;
        }

        .status-badge.status-read {
            background: #17a2b8;
            color: white;
        }

        .status-badge.status-replied {
            background: #28a745;
            color: white;
        }

        .message-date {
            font-size: 13px;
            color: #999;
        }

        .message-subject {
            margin-bottom: 12px;
            font-size: 15px;
            color: #555;
        }

        .message-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            line-height: 1.6;
            color: #333;
        }

        .reply-section {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 3px solid #28a745;
        }

        .reply-section strong {
            display: block;
            margin-bottom: 8px;
            color: #155724;
        }

        .reply-section p {
            margin: 8px 0;
            color: #333;
        }

        .message-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }

        .btn-read {
            background: #17a2b8;
            color: white;
        }

        .btn-reply {
            background: #667eea;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .reply-form {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .reply-form label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        .reply-form textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }

        .reply-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .note {
            display: block;
            margin-top: 8px;
            color: #666;
            font-style: italic;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
        }

        .empty-state i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #666;
            margin-bottom: 10px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>

    <script>
        function showReplyForm(messageId) {
            document.getElementById('reply-form-' + messageId).style.display = 'block';
        }

        function hideReplyForm(messageId) {
            document.getElementById('reply-form-' + messageId).style.display = 'none';
        }
    </script>
</body>
</html>
