<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_page.php');
    exit();
}

require_once('../config/connect.php');

/** @var mysqli $conn */

// Xử lý xóa bài viết
if (isset($_GET['delete'])) {
    $post_id = $_GET['delete'];
    $delete_query = "DELETE FROM blog_posts WHERE post_id = ?";
    $stmt = $conn->prepare($delete_query);
    if ($stmt) {
        $stmt->bind_param("i", $post_id);
        if ($stmt->execute()) {
            $message = "Xóa bài viết thành công!";
        }
    }
}

// Xử lý đổi trạng thái
if (isset($_GET['toggle_status'])) {
    $post_id = $_GET['toggle_status'];
    $new_status = $_GET['status'];
    $update_query = "UPDATE blog_posts SET status = ? WHERE post_id = ?";
    $stmt = $conn->prepare($update_query);
    if ($stmt) {
        $stmt->bind_param("si", $new_status, $post_id);
        $stmt->execute();
    }
}

// Lấy danh sách bài viết
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT bp.*, bc.category_name 
          FROM blog_posts bp 
          LEFT JOIN blog_categories bc ON bp.category_id = bc.category_id 
          WHERE 1=1";

if ($search) {
    $query .= " AND (bp.title LIKE '%$search%' OR bp.content LIKE '%$search%')";
}
if ($category_filter) {
    $query .= " AND bp.category_id = '$category_filter'";
}
if ($status_filter) {
    $query .= " AND bp.status = '$status_filter'";
}
$query .= " ORDER BY bp.created_at DESC";
$result = $conn->query($query);

// Debug
if (!$result) {
    echo "<!-- Query Error: " . $conn->error . " -->";
    echo "<!-- Query: " . htmlspecialchars($query) . " -->";
}

// Lấy danh mục
$categories = $conn->query("SELECT * FROM blog_categories WHERE status = 'active'");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Blog</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/fontawesome/all.min.css">
    <style>
        .blog-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
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
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .featured-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-published {
            background: #d4edda;
            color: #155724;
        }
        .status-draft {
            background: #fff3cd;
            color: #856404;
        }
        .status-archived {
            background: #f8d7da;
            color: #721c24;
        }
        .post-stats {
            display: flex;
            gap: 15px;
            font-size: 13px;
            color: #666;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="blog-container">
        <div class="page-header">
            <h1><i class="fas fa-newspaper"></i> Quản lý Bài viết Blog</h1>
            <div>
                <a href="admin_blog_comments.php" class="btn btn-info">
                    <i class="fas fa-comments"></i> Quản lý bình luận
                </a>
                <a href="admin_blog_form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm bài viết
                </a>
            </div>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Tìm kiếm tiêu đề, nội dung..." value="<?php echo $search; ?>" style="flex: 1; min-width: 250px;">
            <select id="categoryFilter">
                <option value="">Tất cả danh mục</option>
                <?php if ($categories): while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $cat['category_id']; ?>" <?php echo $category_filter == $cat['category_id'] ? 'selected' : ''; ?>>
                        <?php echo $cat['category_name']; ?>
                    </option>
                <?php endwhile; endif; ?>
            </select>
            <select id="statusFilter">
                <option value="">Tất cả trạng thái</option>
                <option value="published" <?php echo $status_filter == 'published' ? 'selected' : ''; ?>>Đã xuất bản</option>
                <option value="draft" <?php echo $status_filter == 'draft' ? 'selected' : ''; ?>>Bản nháp</option>
                <option value="archived" <?php echo $status_filter == 'archived' ? 'selected' : ''; ?>>Lưu trữ</option>
            </select>
            <button class="btn btn-primary" onclick="searchPosts()">
                <i class="fas fa-search"></i> Tìm
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Tác giả</th>
                        <th>Thống kê</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($post = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $post['post_id']; ?></td>
                        <td>
                            <?php if ($post['featured_image']): ?>
                                <img src="../uploads/blog/<?php echo $post['featured_image']; ?>" class="featured-img">
                            <?php else: ?>
                                <div style="width: 80px; height: 60px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image" style="color: #999;"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                            <br>
                            <small style="color: #666;"><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></small>
                        </td>
                        <td><?php echo $post['category_name'] ?: '-'; ?></td>
                        <td>Admin</td>
                        <td>
                            <div class="post-stats">
                                <span><i class="fas fa-eye"></i> <?php echo $post['views']; ?></span>
                                <?php
                                $comments_count = $conn->query("SELECT COUNT(*) as count FROM blog_comments WHERE post_id = {$post['post_id']}")->fetch_assoc()['count'];
                                ?>
                                <span><i class="fas fa-comment"></i> <?php echo $comments_count; ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $post['status'] ?? 'draft'; ?>">
                                <?php 
                                    $status_text = ['published' => 'Đã xuất bản', 'draft' => 'Bản nháp', 'archived' => 'Lưu trữ'];
                                    echo $status_text[$post['status']] ?? 'Không xác định';
                                ?>
                            </span>
                        </td>
                        <td>
                            <a href="admin_blog_form.php?id=<?php echo $post['post_id']; ?>" class="btn btn-success" style="padding: 8px 12px;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($post['status'] == 'published'): ?>
                                <a href="?toggle_status=<?php echo $post['post_id']; ?>&status=draft" class="btn btn-warning" style="padding: 8px 12px;" title="Chuyển về nháp">
                                    <i class="fas fa-eye-slash"></i>
                                </a>
                            <?php else: ?>
                                <a href="?toggle_status=<?php echo $post['post_id']; ?>&status=published" class="btn btn-info" style="padding: 8px 12px;" title="Xuất bản">
                                    <i class="fas fa-eye"></i>
                                </a>
                            <?php endif; ?>
                            <button class="btn btn-danger" style="padding: 8px 12px;" onclick="deletePost(<?php echo $post['post_id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                            <i class="fas fa-inbox fa-3x" style="margin-bottom: 15px; display: block;"></i>
                            <p>Chưa có bài viết nào. Hãy thêm bài viết mới!</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function searchPosts() {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            const status = document.getElementById('statusFilter').value;
            window.location.href = '?search=' + search + '&category=' + category + '&status=' + status;
        }

        function deletePost(id) {
            if (confirm('Bạn có chắc chắn muốn xóa bài viết này? Tất cả bình luận liên quan cũng sẽ bị xóa.')) {
                window.location.href = '?delete=' + id;
            }
        }
    </script>
</body>
</html>
