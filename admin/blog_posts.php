<?php
session_start();
require_once '../config/db.php';

/** @var mysqli $conn */

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Xử lý xóa bài viết
if (isset($_POST['delete_post'])) {
    $post_id = intval($_POST['post_id']);
    $delete_sql = "DELETE FROM blog_posts WHERE post_id = ?";
    $stmt = $conn->prepare($delete_sql);
    if ($stmt) {
        $stmt->bind_param("i", $post_id);
        if ($stmt->execute()) {
            echo "<script>alert('Xóa bài viết thành công!'); window.location.href='blog_posts.php';</script>";
        }
    }
}

// Lấy danh sách bài viết
$sql = "SELECT bp.*, au.username as author_name 
        FROM blog_posts bp 
        LEFT JOIN admin_users au ON bp.author_id = au.admin_id 
        ORDER BY bp.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bài viết - TTHUONG Bookstore Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <main>
        <div class="admin-container">
            <div class="page-header">
                <h1><i class="fas fa-newspaper"></i> Quản lý bài viết</h1>
                <a href="add_blog_post.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm bài viết mới
                </a>
            </div>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Danh mục</th>
                            <th>Tác giả</th>
                            <th>Lượt xem</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($post = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $post['post_id']; ?></td>
                                    <td>
                                        <div class="post-title">
                                            <?php if ($post['featured_image']): ?>
                                                <img src="../<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                                     alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                                            <?php endif; ?>
                                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo htmlspecialchars($post['category']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($post['author_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo number_format($post['views']); ?></td>
                                    <td>
                                        <?php if ($post['status'] === 'published'): ?>
                                            <span class="badge badge-success">Đã xuất bản</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Nháp</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></td>
                                    <td class="action-buttons">
                                        <a href="../blog_detail.php?slug=<?php echo $post['slug']; ?>" 
                                           class="btn btn-sm btn-info" target="_blank" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit_blog_post.php?id=<?php echo $post['post_id']; ?>" 
                                           class="btn btn-sm btn-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?');">
                                            <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                                            <button type="submit" name="delete_post" class="btn btn-sm btn-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc;"></i>
                                    <p style="margin-top: 15px; color: #999;">Chưa có bài viết nào</p>
                                    <a href="add_blog_post.php" class="btn btn-primary" style="margin-top: 15px;">
                                        <i class="fas fa-plus"></i> Thêm bài viết đầu tiên
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <style>
        .admin-container {
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .page-header h1 {
            font-size: 28px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .admin-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .admin-table td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .admin-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .post-title {
            display: flex;
            align-items: center;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-info:hover {
            background: #138496;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
    </style>
</body>
</html>
