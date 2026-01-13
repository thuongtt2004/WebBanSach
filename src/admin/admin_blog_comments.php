<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_page.php');
    exit();
}

require_once('../config/connect.php');

/** @var mysqli $conn */

// Xử lý xóa bình luận
if (isset($_GET['delete'])) {
    $comment_id = $_GET['delete'];
    $delete_query = "DELETE FROM blog_comments WHERE comment_id = ?";
    $stmt = $conn->prepare($delete_query);
    if ($stmt) {
        $stmt->bind_param("i", $comment_id);
        if ($stmt->execute()) {
            $message = "Xóa bình luận thành công!";
        }
    }
}

// Xử lý duyệt bình luận
if (isset($_GET['approve'])) {
    $comment_id = $_GET['approve'];
    $update_query = "UPDATE blog_comments SET status = 'approved' WHERE comment_id = ?";
    $stmt = $conn->prepare($update_query);
    if ($stmt) {
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        $message = "Đã duyệt bình luận!";
    }
}

// Xử lý từ chối bình luận
if (isset($_GET['reject'])) {
    $comment_id = $_GET['reject'];
    $update_query = "UPDATE blog_comments SET status = 'rejected' WHERE comment_id = ?";
    $stmt = $conn->prepare($update_query);
    if ($stmt) {
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        $message = "Đã từ chối bình luận!";
    }
}

// Lấy danh sách bình luận
$search = isset($_GET['search']) ? $_GET['search'] : '';
$post_filter = isset($_GET['post']) ? $_GET['post'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT bc.*, bp.title as post_title, u.username 
          FROM blog_comments bc 
          LEFT JOIN blog_posts bp ON bc.post_id = bp.post_id 
          LEFT JOIN users u ON bc.user_id = u.user_id 
          WHERE 1=1";

if ($search) {
    $query .= " AND (bc.content LIKE '%$search%' OR bc.author_name LIKE '%$search%')";
}
if ($post_filter) {
    $query .= " AND bc.post_id = '$post_filter'";
}
if ($status_filter) {
    $query .= " AND bc.status = '$status_filter'";
}
$query .= " ORDER BY bc.created_at DESC";
$result = $conn->query($query);

// Lấy danh sách bài viết
$posts = $conn->query("SELECT post_id, title FROM blog_posts ORDER BY created_at DESC LIMIT 50");

// Đếm bình luận chờ duyệt
$pending_result = $conn->query("SELECT COUNT(*) as count FROM blog_comments WHERE status = 'pending'");
$pending_count = $pending_result ? $pending_result->fetch_assoc()['count'] : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bình luận</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/fontawesome/all.min.css">
    <style>
        .comments-container {
            padding: 20px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .stats-bar {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-card .number {
            font-size: 24px;
            font-weight: 700;
            color: #007bff;
        }
        .stat-card .label {
            color: #666;
            font-size: 14px;
        }
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .search-bar input, .search-bar select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        .comments-list {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .comment-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        .comment-item:last-child {
            border-bottom: none;
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }
        .comment-author {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .comment-author i {
            font-size: 32px;
            color: #999;
        }
        .author-info strong {
            display: block;
            color: #333;
        }
        .author-info small {
            color: #666;
        }
        .comment-content {
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            line-height: 1.6;
        }
        .comment-meta {
            display: flex;
            gap: 20px;
            font-size: 13px;
            color: #666;
            margin-bottom: 15px;
        }
        .comment-actions {
            display: flex;
            gap: 10px;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background: #d4edda;
            color: #155724;
        }
        .reply-indicator {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="comments-container">
        <div class="page-header">
            <h1><i class="fas fa-comments"></i> Quản lý Bình luận</h1>
            <a href="admin_blog_posts.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Quay lại bài viết
            </a>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="stats-bar">
            <div class="stat-card">
                <div class="number"><?php echo $pending_count; ?></div>
                <div class="label">Chờ duyệt</div>
            </div>
            <div class="stat-card">
                <div class="number">
                    <?php 
                        $approved_result = $conn->query("SELECT COUNT(*) as count FROM blog_comments WHERE status = 'approved'");
                        echo $approved_result ? $approved_result->fetch_assoc()['count'] : 0;
                    ?>
                </div>
                <div class="label">Đã duyệt</div>
            </div>
            <div class="stat-card">
                <div class="number">
                    <?php 
                        $rejected_result = $conn->query("SELECT COUNT(*) as count FROM blog_comments WHERE status = 'rejected'");
                        echo $rejected_result ? $rejected_result->fetch_assoc()['count'] : 0;
                    ?>
                </div>
                <div class="label">Đã từ chối</div>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Tìm kiếm nội dung, tác giả..." value="<?php echo $search; ?>" style="flex: 1; min-width: 250px;">
            <select id="postFilter">
                <option value="">Tất cả bài viết</option>
                <?php if ($posts): while ($post = $posts->fetch_assoc()): ?>
                    <option value="<?php echo $post['post_id']; ?>" <?php echo $post_filter == $post['post_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($post['title']); ?>
                    </option>
                <?php endwhile; endif; ?>
            </select>
            <select id="statusFilter">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Đã từ chối</option>
            </select>
            <button class="btn btn-primary" onclick="searchComments()">
                <i class="fas fa-search"></i> Tìm
            </button>
        </div>

        <div class="comments-list">
            <?php if (!$result || $result->num_rows == 0): ?>
                <div style="padding: 40px; text-align: center; color: #999;">
                    <i class="fas fa-comments fa-3x" style="margin-bottom: 15px;"></i>
                    <p>Chưa có bình luận nào</p>
                </div>
            <?php else: ?>
                <?php if ($result): while ($comment = $result->fetch_assoc()): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <div class="comment-author">
                            <i class="fas fa-user-circle"></i>
                            <div class="author-info">
                                <strong>
                                    <?php echo $comment['username'] ?: htmlspecialchars($comment['author_name']); ?>
                                    <?php if ($comment['parent_id']): ?>
                                        <span class="reply-indicator"><i class="fas fa-reply"></i> Trả lời</span>
                                    <?php endif; ?>
                                </strong>
                                <small>
                                    <?php echo $comment['author_email']; ?>
                                    • <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                        <span class="status-badge status-<?php echo $comment['status']; ?>">
                            <?php 
                                $status_text = ['pending' => 'Chờ duyệt', 'approved' => 'Đã duyệt', 'rejected' => 'Đã từ chối'];
                                echo $status_text[$comment['status']];
                            ?>
                        </span>
                    </div>
                    
                    <div class="comment-meta">
                        <span><i class="fas fa-newspaper"></i> <strong>Bài viết:</strong> <?php echo htmlspecialchars($comment['post_title']); ?></span>
                    </div>
                    
                    <div class="comment-content">
                        <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                    </div>
                    
                    <div class="comment-actions">
                        <?php if ($comment['status'] != 'approved'): ?>
                            <a href="?approve=<?php echo $comment['comment_id']; ?>" class="btn btn-success" style="padding: 8px 15px;">
                                <i class="fas fa-check"></i> Duyệt
                            </a>
                        <?php endif; ?>
                        <?php if ($comment['status'] != 'rejected'): ?>
                            <a href="?reject=<?php echo $comment['comment_id']; ?>" class="btn btn-warning" style="padding: 8px 15px;">
                                <i class="fas fa-times"></i> Từ chối
                            </a>
                        <?php endif; ?>
                        <button class="btn btn-danger" style="padding: 8px 15px;" onclick="deleteComment(<?php echo $comment['comment_id']; ?>)">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </div>
                </div>
                <?php endwhile; endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function searchComments() {
            const search = document.getElementById('searchInput').value;
            const post = document.getElementById('postFilter').value;
            const status = document.getElementById('statusFilter').value;
            window.location.href = '?search=' + search + '&post=' + post + '&status=' + status;
        }

        function deleteComment(id) {
            if (confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
                window.location.href = '?delete=' + id;
            }
        }
    </script>
</body>
</html>
