<?php
session_start();
require_once '../config/db.php';

/** @var mysqli $conn */

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $post_id > 0;

// Lấy thông tin bài viết nếu đang sửa
$post = null;
if ($is_edit) {
    $sql = "SELECT * FROM blog_posts WHERE post_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();
        
        if (!$post) {
            header('Location: blog_posts.php');
            exit();
        }
    }
}

// Lấy danh sách categories
$categories_sql = "SELECT * FROM blog_categories ORDER BY category_name";
$categories_result = $conn->query($categories_sql);

// Xử lý submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    $content = $_POST['content'];
    $excerpt = trim($_POST['excerpt']);
    $category = trim($_POST['category']);
    $status = $_POST['status'];
    $author_id = $_SESSION['admin_id'];
    
    // Xử lý upload ảnh
    $featured_image = $is_edit ? $post['featured_image'] : '';
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $upload_dir = '../uploads/blog/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_exts)) {
            $new_filename = uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path)) {
                $featured_image = 'uploads/blog/' . $new_filename;
            }
        }
    }
    
    // Tạo slug tự động nếu trống
    if (empty($slug)) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s]+/', '-', $slug);
        $slug = trim($slug, '-');
    }
    
    if ($is_edit) {
        // Cập nhật bài viết
        $update_sql = "UPDATE blog_posts SET 
                       title = ?, slug = ?, content = ?, excerpt = ?, 
                       featured_image = ?, category = ?, status = ?,
                       published_at = IF(status = 'published' AND published_at IS NULL, NOW(), published_at)
                       WHERE post_id = ?";
        $stmt = $conn->prepare($update_sql);
        if ($stmt) {
            $stmt->bind_param("sssssssi", $title, $slug, $content, $excerpt, $featured_image, $category, $status, $post_id);
        }
    } else {
        // Thêm bài viết mới
        $published_at = $status === 'published' ? date('Y-m-d H:i:s') : null;
        $insert_sql = "INSERT INTO blog_posts (title, slug, content, excerpt, featured_image, category, author_id, status, published_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        if ($stmt) {
            $stmt->bind_param("sssssssss", $title, $slug, $content, $excerpt, $featured_image, $category, $author_id, $status, $published_at);
        }
    }
    
    if (isset($stmt) && $stmt->execute()) {
        $message = $is_edit ? 'Cập nhật bài viết thành công!' : 'Thêm bài viết thành công!';
        echo "<script>alert('$message'); window.location.href='blog_posts.php';</script>";
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Sửa' : 'Thêm'; ?> bài viết - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/fontawesome/all.min.css">
    <!-- TinyMCE Editor -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <main>
        <div class="admin-container">
            <div class="page-header">
                <h1>
                    <i class="fas fa-<?php echo $is_edit ? 'edit' : 'plus'; ?>"></i>
                    <?php echo $is_edit ? 'Sửa' : 'Thêm'; ?> bài viết
                </h1>
                <a href="blog_posts.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="blog-form">
                <div class="form-row">
                    <div class="form-group col-8">
                        <label for="title">Tiêu đề bài viết <span class="required">*</span></label>
                        <input type="text" id="title" name="title" class="form-control" 
                               value="<?php echo $is_edit ? htmlspecialchars($post['title']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group col-4">
                        <label for="slug">Slug (URL thân thiện)</label>
                        <input type="text" id="slug" name="slug" class="form-control" 
                               value="<?php echo $is_edit ? htmlspecialchars($post['slug']) : ''; ?>"
                               placeholder="tu-dong-tao-neu-bo-trong">
                    </div>
                </div>

                <div class="form-group">
                    <label for="excerpt">Tóm tắt (hiển thị trong danh sách)</label>
                    <textarea id="excerpt" name="excerpt" class="form-control" rows="3" 
                              maxlength="500"><?php echo $is_edit ? htmlspecialchars($post['excerpt']) : ''; ?></textarea>
                    <small class="form-text">Tối đa 500 ký tự</small>
                </div>

                <div class="form-group">
                    <label for="content">Nội dung bài viết <span class="required">*</span></label>
                    <textarea id="content" name="content" class="form-control" rows="20" required><?php echo $is_edit ? htmlspecialchars($post['content']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="category">Danh mục</label>
                        <select id="category" name="category" class="form-control">
                            <?php if ($categories_result): while ($cat = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($cat['category_name']); ?>"
                                        <?php echo ($is_edit && $post['category'] === $cat['category_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group col-6">
                        <label for="status">Trạng thái</label>
                        <select id="status" name="status" class="form-control">
                            <option value="draft" <?php echo ($is_edit && $post['status'] === 'draft') ? 'selected' : ''; ?>>Nháp</option>
                            <option value="published" <?php echo ($is_edit && $post['status'] === 'published') ? 'selected' : ''; ?>>Xuất bản</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="featured_image">Ảnh đại diện</label>
                    <?php if ($is_edit && $post['featured_image']): ?>
                        <div class="current-image">
                            <img src="../<?php echo htmlspecialchars($post['featured_image']); ?>" alt="Current image">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/*">
                    <small class="form-text">Định dạng: JPG, PNG, GIF, WebP. Kích thước đề xuất: 1200x630px</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> <?php echo $is_edit ? 'Cập nhật' : 'Thêm'; ?> bài viết
                    </button>
                    <a href="blog_posts.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });
        
        // Auto generate slug from title
        document.getElementById('title').addEventListener('input', function() {
            const slug = document.getElementById('slug');
            if (!slug.value) {
                let title = this.value.toLowerCase();
                title = title.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                title = title.replace(/đ/g, 'd').replace(/Đ/g, 'D');
                title = title.replace(/[^a-z0-9\s-]/g, '');
                title = title.replace(/\s+/g, '-');
                title = title.replace(/-+/g, '-');
                title = title.trim();
                slug.value = title;
            }
        });
    </script>

    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .blog-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .col-4 { flex: 0 0 33.33%; }
        .col-6 { flex: 0 0 50%; }
        .col-8 { flex: 0 0 66.66%; }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .required {
            color: #dc3545;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-text {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 13px;
        }
        
        .current-image {
            margin-bottom: 10px;
        }
        
        .current-image img {
            max-width: 300px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #f0f0f0;
        }
        
        .btn-lg {
            padding: 12px 30px;
            font-size: 16px;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</body>
</html>
