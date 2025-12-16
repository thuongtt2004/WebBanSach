<?php
session_start();
require_once '../config/connect.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_page.php');
    exit();
}

// Xử lý thêm danh mục
if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    $description = trim($_POST['description']);
    
    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $category_name, $description);
        
        if ($stmt->execute()) {
            $success = 'Thêm danh mục thành công!';
        } else {
            $error = 'Lỗi khi thêm danh mục: ' . $conn->error;
        }
        $stmt->close();
    } else {
        $error = 'Tên danh mục không được để trống!';
    }
}

// Xử lý sửa danh mục
if (isset($_POST['edit_category'])) {
    $category_id = intval($_POST['category_id']);
    $category_name = trim($_POST['category_name']);
    $description = trim($_POST['description']);
    
    if (!empty($category_name)) {
        $stmt = $conn->prepare("UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?");
        $stmt->bind_param("ssi", $category_name, $description, $category_id);
        
        if ($stmt->execute()) {
            $success = 'Cập nhật danh mục thành công!';
        } else {
            $error = 'Lỗi khi cập nhật danh mục: ' . $conn->error;
        }
        $stmt->close();
    } else {
        $error = 'Tên danh mục không được để trống!';
    }
}

// Xử lý xóa danh mục
if (isset($_POST['delete_category'])) {
    $category_id = intval($_POST['category_id']);
    
    // Kiểm tra xem có sản phẩm nào đang dùng danh mục này không
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
    $check_stmt->bind_param("i", $category_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $count = $check_result->fetch_assoc()['count'];
    
    if ($count > 0) {
        $error = "Không thể xóa danh mục này vì có {$count} sản phẩm đang sử dụng!";
    } else {
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        
        if ($stmt->execute()) {
            $success = 'Xóa danh mục thành công!';
        } else {
            $error = 'Lỗi khi xóa danh mục: ' . $conn->error;
        }
        $stmt->close();
    }
}

// Lấy danh sách danh mục
$sql = "SELECT c.*, COUNT(p.product_id) as product_count 
        FROM categories c 
        LEFT JOIN products p ON c.category_id = p.category_id 
        GROUP BY c.category_id 
        ORDER BY c.category_name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Danh Mục - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .categories-container {
            padding: 20px;
        }
        .add-category-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .category-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .category-table th,
        .category-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .category-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
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
            color: #000;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .alert {
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main>
        <div class="categories-container">
            <h1><i class="fas fa-list"></i> Quản Lý Danh Mục Sản Phẩm</h1>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Form thêm danh mục -->
            <div class="add-category-form">
                <h3><i class="fas fa-plus-circle"></i> Thêm Danh Mục Mới</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="category_name">Tên danh mục: <span style="color:red;">*</span></label>
                        <input type="text" id="category_name" name="category_name" required placeholder="Ví dụ: Văn học, Khoa học, Thiếu nhi...">
                    </div>
                    <div class="form-group">
                        <label for="description">Mô tả:</label>
                        <textarea id="description" name="description" rows="3" placeholder="Mô tả về danh mục này..."></textarea>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-success">
                        <i class="fas fa-plus"></i> Thêm Danh Mục
                    </button>
                </form>
            </div>
            
            <!-- Danh sách danh mục -->
            <h3><i class="fas fa-list-ul"></i> Danh Sách Danh Mục</h3>
            <table class="category-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Danh Mục</th>
                        <th>Mô Tả</th>
                        <th>Số Sản Phẩm</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($category = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $category['category_id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($category['category_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($category['description'] ?? 'Chưa có mô tả'); ?></td>
                                <td>
                                    <span style="background:#007bff;color:white;padding:4px 12px;border-radius:12px;font-size:12px;">
                                        <?php echo $category['product_count']; ?> sản phẩm
                                    </span>
                                </td>
                                <td>
                                    <button onclick="editCategory(<?php echo $category['category_id']; ?>, '<?php echo htmlspecialchars($category['category_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($category['description'] ?? '', ENT_QUOTES); ?>')" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');">
                                        <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                        <button type="submit" name="delete_category" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;padding:40px;color:#999;">
                                <i class="fas fa-inbox fa-3x" style="margin-bottom:10px;"></i>
                                <p>Chưa có danh mục nào</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <!-- Modal sửa danh mục -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-edit"></i> Sửa Danh Mục</h3>
            <form method="POST" action="">
                <input type="hidden" id="edit_category_id" name="category_id">
                <div class="form-group">
                    <label for="edit_category_name">Tên danh mục: <span style="color:red;">*</span></label>
                    <input type="text" id="edit_category_name" name="category_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_description">Mô tả:</label>
                    <textarea id="edit_description" name="description" rows="3"></textarea>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Hủy</button>
                    <button type="submit" name="edit_category" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function editCategory(id, name, description) {
            document.getElementById('edit_category_id').value = id;
            document.getElementById('edit_category_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('editModal').style.display = 'flex';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
    
    <?php include 'admin_footer.php'; ?>
</body>
</html>

<?php $conn->close(); ?>
