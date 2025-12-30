<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_page.php');
    exit();
}

require_once('../config/connect.php');

/** @var mysqli $conn */

$post_id = isset($_GET['id']) ? $_GET['id'] : null;
$post = null;

if ($post_id) {
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE post_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $post = $result->fetch_assoc();
        }
    }
}

// Xử lý submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $slug = $_POST['slug'];
    $content = $_POST['content'];
    $excerpt = $_POST['excerpt'];
    $category_id = $_POST['category_id'] ?: null;
    $author_id = $_POST['author_id'] ?: null;
    $status = $_POST['status'];
    
    // Xử lý upload ảnh
    $featured_image = $post ? $post['featured_image'] : '';
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
        $target_dir = "../uploads/blog/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
        $featured_image = 'blog_' . time() . '.' . $file_extension;
        move_uploaded_file($_FILES['featured_image']['tmp_name'], $target_dir . $featured_image);
    }
    
    $published_at = ($status == 'published' && !$post) ? date('Y-m-d H:i:s') : ($post['published_at'] ?? null);
    
    if ($post_id) {
        // Cập nhật
        $update_query = "UPDATE blog_posts SET title=?, slug=?, content=?, excerpt=?, featured_image=?, category_id=?, author_id=?, status=?, published_at=? WHERE post_id=?";
        $stmt = $conn->prepare($update_query);
        if ($stmt) {
            $stmt->bind_param("sssssiiisi", $title, $slug, $content, $excerpt, $featured_image, $category_id, $author_id, $status, $published_at, $post_id);
            $stmt->execute();
        }
        header('Location: admin_blog_posts.php');
        exit();
    } else {
        // Thêm mới
        $insert_query = "INSERT INTO blog_posts (title, slug, content, excerpt, featured_image, category_id, author_id, status, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        if ($stmt) {
            $stmt->bind_param("sssssiiis", $title, $slug, $content, $excerpt, $featured_image, $category_id, $author_id, $status, $published_at);
            $stmt->execute();
        }
        header('Location: admin_blog_posts.php');
        exit();
    }
}

// Lấy danh mục và tác giả
$categories = $conn->query("SELECT * FROM blog_categories WHERE status = 'active'");
$authors = $conn->query("SELECT * FROM authors WHERE status = 'active'");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post ? 'Sửa' : 'Thêm'; ?> bài viết</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
    <style>
        .form-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .page-header {
            margin-bottom: 30px;
        }
        .form-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        .btn {
            padding: 12px 25px;
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
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .image-preview {
            max-width: 300px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="form-container">
        <div class="page-header">
            <h1><i class="fas fa-edit"></i> <?php echo $post ? 'Sửa' : 'Thêm'; ?> bài viết</h1>
        </div>

        <div class="form-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Tiêu đề *</label>
                    <input type="text" name="title" id="title" required value="<?php echo $post ? htmlspecialchars($post['title']) : ''; ?>" onkeyup="generateSlug()">
                </div>

                <div class="form-group">
                    <label>Slug (URL thân thiện) *</label>
                    <input type="text" name="slug" id="slug" required value="<?php echo $post ? htmlspecialchars($post['slug']) : ''; ?>">
                    <div class="help-text">Ví dụ: bai-viet-moi-ve-sach-hay</div>
                </div>

                <div class="form-group">
                    <label>Tóm tắt</label>
                    <textarea name="excerpt" rows="3"><?php echo $post ? htmlspecialchars($post['excerpt']) : ''; ?></textarea>
                    <div class="help-text">Hiển thị trong danh sách bài viết</div>
                </div>

                <div class="form-group">
                    <label>Nội dung *</label>
                    <textarea name="content" id="content" required><?php echo $post ? htmlspecialchars($post['content']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Danh mục</label>
                        <select name="category_id">
                            <option value="">-- Chọn danh mục --</option>
                            <?php if ($categories): while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $cat['category_id']; ?>" 
                                    <?php echo ($post && $post['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo $cat['category_name']; ?>
                                </option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tác giả</label>
                        <select name="author_id">
                            <option value="">-- Admin --</option>
                            <?php if ($authors): while ($author = $authors->fetch_assoc()): ?>
                                <option value="<?php echo $author['author_id']; ?>" 
                                    <?php echo ($post && $post['author_id'] == $author['author_id']) ? 'selected' : ''; ?>>
                                    <?php echo $author['author_name']; ?>
                                </option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ảnh đại diện</label>
                        <input type="file" name="featured_image" accept="image/*" onchange="previewImage(this)">
                        <?php if ($post && $post['featured_image']): ?>
                            <img src="../uploads/blog/<?php echo $post['featured_image']; ?>" class="image-preview" id="preview">
                        <?php else: ?>
                            <img src="" class="image-preview" id="preview" style="display: none;">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Trạng thái *</label>
                        <select name="status" required>
                            <option value="draft" <?php echo ($post && $post['status'] == 'draft') ? 'selected' : ''; ?>>Bản nháp</option>
                            <option value="published" <?php echo ($post && $post['status'] == 'published') ? 'selected' : ''; ?>>Xuất bản</option>
                            <option value="archived" <?php echo ($post && $post['status'] == 'archived') ? 'selected' : ''; ?>>Lưu trữ</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu bài viết
                    </button>
                    <?php if ($post): ?>
                        <button type="submit" name="status" value="draft" class="btn btn-secondary">
                            <i class="fas fa-file"></i> Lưu nháp
                        </button>
                    <?php endif; ?>
                    <a href="admin_blog_posts.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });

        function generateSlug() {
            const title = document.getElementById('title').value;
            const slug = title.toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd').replace(/Đ/g, 'D')
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            document.getElementById('slug').value = slug;
        }

        function previewImage(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
