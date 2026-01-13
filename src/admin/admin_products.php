<?php
session_start();
require_once '../config/connect.php';

/** @var mysqli $conn */

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_page.php');
    exit();
}

// Xử lý xóa sản phẩm
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    
    try {
        // Debug để kiểm tra ID sản phẩm
        error_log("Đang xóa sản phẩm ID: " . $product_id);
        
        // Bắt đầu transaction
        if (method_exists($conn, 'begin_transaction')) {
            $conn->begin_transaction();
        }
        
        // Sử dụng Prepared Statement để tránh SQL injection
        $delete_query = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        if ($delete_query) {
            $delete_query->bind_param("s", $product_id);
            
            if ($delete_query->execute()) {
                if (method_exists($conn, 'commit')) {
                    $conn->commit();
                }
                echo "<script>
                    alert('Xóa sản phẩm ID: " . $product_id . " thành công!');
                    window.location.href = 'admin_products.php';
                </script>";
            } else {
                throw new Exception("Lỗi khi xóa sản phẩm");
            }
            
            $delete_query->close();
        }
        
    } catch (Exception $e) {
        if (method_exists($conn, 'rollback')) {
            $conn->rollback();
        }
        echo "<script>
            alert('Lỗi khi xóa sản phẩm: " . $e->getMessage() . "');
            window.location.href = 'admin_products.php';
        </script>";
    }
}

// Xử lý cập nhật sản phẩm
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = trim($_POST['product_name']);
    $author = trim($_POST['author'] ?? '');
    $publisher = trim($_POST['publisher'] ?? '');
    $publish_year = !empty($_POST['publish_year']) ? intval($_POST['publish_year']) : null;
    $isbn = trim($_POST['isbn'] ?? '');
    $pages = !empty($_POST['pages']) ? intval($_POST['pages']) : null;
    $language = $_POST['language'] ?? 'Tiếng Việt';
    $book_format = $_POST['book_format'] ?? 'Bìa mềm';
    $dimensions = trim($_POST['dimensions'] ?? '');
    $weight = !empty($_POST['weight']) ? intval($_POST['weight']) : null;
    $series = trim($_POST['series'] ?? '');
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $sold_quantity = intval($_POST['sold_quantity']);
    $category_id = intval($_POST['category_id']);
    $description = trim($_POST['description']);
    
    // Xử lý upload ảnh mới nếu có
    $image_url = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Kiểm tra file hình ảnh hợp lệ
        $valid_extensions = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $valid_extensions)) {
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                $image_url = "uploads/" . $file_name;
            }
        }
    }

    // Cập nhật sản phẩm với hoặc không có ảnh mới
    if ($image_url) {
        $stmt = $conn->prepare("UPDATE products SET product_name = ?, author = ?, publisher = ?, publish_year = ?, isbn = ?, pages = ?, language = ?, book_format = ?, dimensions = ?, weight = ?, series = ?, price = ?, stock_quantity = ?, sold_quantity = ?, category_id = ?, description = ?, image_url = ? WHERE product_id = ?");
        if ($stmt) {
            $stmt->bind_param("sssisssssidiiidiss", $product_name, $author, $publisher, $publish_year, $isbn, $pages, $language, $book_format, $dimensions, $weight, $series, $price, $stock_quantity, $sold_quantity, $category_id, $description, $image_url, $product_id);
        }
    } else {
        $stmt = $conn->prepare("UPDATE products SET product_name = ?, author = ?, publisher = ?, publish_year = ?, isbn = ?, pages = ?, language = ?, book_format = ?, dimensions = ?, weight = ?, series = ?, price = ?, stock_quantity = ?, sold_quantity = ?, category_id = ?, description = ? WHERE product_id = ?");
        if ($stmt) {
            $stmt->bind_param("sssisssssidiiidis", $product_name, $author, $publisher, $publish_year, $isbn, $pages, $language, $book_format, $dimensions, $weight, $series, $price, $stock_quantity, $sold_quantity, $category_id, $description, $product_id);
        }
    }
    
    if ($stmt) {
        if ($stmt->execute()) {
            echo "<script>alert('Cập nhật sách thành công!');</script>";
        } else {
            echo "<script>alert('Lỗi khi cập nhật sách');</script>";
        }
        $stmt->close();
    }
}

// Lấy category từ URL nếu có
$selected_category = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Lấy danh sách sản phẩm (có thể lọc theo category)
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id ";
if ($selected_category) {
    $sql .= "WHERE p.category_id = " . $selected_category . " ";
}
$sql .= "ORDER BY p.product_id DESC";
$result = $conn->query($sql);

// Lấy danh sách danh mục cho form cập nhật
$categories_sql = "SELECT * FROM categories";
$categories_result = $conn->query($categories_sql);
$categories = [];
if ($categories_result) {
    while ($category = $categories_result->fetch_assoc()) {
        $categories[] = $category;
    }
}

// Lấy danh sách tác giả
$authors_sql = "SELECT author_id, author_name FROM authors ORDER BY author_name ASC";
$authors_result = $conn->query($authors_sql);
$authors = [];
if ($authors_result) {
    while ($author = $authors_result->fetch_assoc()) {
        $authors[] = $author;
    }
}

// Lấy danh sách nhà xuất bản
$publishers_sql = "SELECT publisher_id, publisher_name FROM publishers ORDER BY publisher_name ASC";
$publishers_result = $conn->query($publishers_sql);
$publishers = [];
if ($publishers_result) {
    while ($publisher = $publishers_result->fetch_assoc()) {
        $publishers[] = $publisher;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sách - TTHUONG Bookstore</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/admin-mobile.css">
    <link rel="stylesheet" href="../css/admin_products.css">
    <link rel="stylesheet" href="../css/mobile-375px.css">
    <link rel="stylesheet" href="../css/fontawesome/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .searchable-select-wrapper {
            position: relative;
        }
        
        .searchable-select-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .searchable-select-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.1);
        }
        
        .searchable-select-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 200px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .searchable-select-dropdown.show {
            display: block;
        }
        
        .searchable-select-item {
            padding: 10px 12px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .searchable-select-item:hover {
            background: #f0f0f0;
        }
        
        .searchable-select-item.hidden {
            display: none;
        }
        
        .button-container {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-section {
            display: flex;
            gap: 10px;
            align-items: center;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <main>
    <div class="container">
        <h1><i class="fas fa-book"></i> Quản Lý Sách</h1>
        
        <?php if ($selected_category): 
            // Lấy tên danh mục
            $cat_name_query = $conn->query("SELECT category_name FROM categories WHERE category_id = $selected_category");
            if ($cat_name_query && $cat_name_query->num_rows > 0) {
                $cat_name = $cat_name_query->fetch_assoc()['category_name'];
        ?>
        <div style="background:#e3f2fd;padding:12px 20px;border-radius:8px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;">
            <span style="color:#1976d2;font-weight:500;">
                <i class="fas fa-filter"></i> Đang hiển thị sản phẩm thuộc danh mục: <strong><?php echo htmlspecialchars($cat_name); ?></strong>
            </span>
            <a href="admin_products.php" style="color:#1976d2;text-decoration:none;font-weight:500;">
                <i class="fas fa-times-circle"></i> Xóa bộ lọc
            </a>
        </div>
        <?php 
            }
        endif; ?>
        
        <div class="button-container">
            <a href="../add_product.php" class="add-product-btn">
                <i class="fas fa-plus"></i> Thêm sách mới
            </a>
            <div class="filter-section" style="display:flex;gap:10px;align-items:center;">
                <label for="categoryFilterSelect" style="font-weight:500;color:#333;">
                    <i class="fas fa-filter"></i> Lọc danh mục:
                </label>
                <select id="categoryFilterSelect" onchange="filterByCategory()" style="padding:8px 12px;border:1px solid #ddd;border-radius:4px;font-size:14px;min-width:200px;">
                    <option value="all" <?php echo !$selected_category ? 'selected' : ''; ?>>Tất cả danh mục</option>
                    <?php
                    $cat_filter_query = $conn->query("SELECT * FROM categories ORDER BY category_name");
                    while($cat = $cat_filter_query->fetch_assoc()): 
                        $is_selected = ($selected_category == $cat['category_id']) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $cat['category_id']; ?>" <?php echo $is_selected; ?>>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Tìm kiếm sách theo tên, tác giả, danh mục..." onkeyup="searchProducts()">
            </div>
        </div>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Sách</th>
                        <th>Tác Giả</th>
                        <th>Danh Mục</th>
                        <th>Giá</th>
                        <th>Tồn Kho</th>
                        <th>Đã Bán</th>
                        <th>Hình Ảnh</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result): while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['product_id']; ?></td>
                            <td><?php echo $row['product_name']; ?></td>
                            <td><?php echo $row['author'] ?? '-'; ?></td>
                            <td><?php echo $row['category_name']; ?></td>
                            <td><?php echo number_format($row['price'], 0, ',', '.'); ?> VNĐ</td>
                            <td><?php echo $row['stock_quantity']; ?></td>
                            <td><?php echo $row['sold_quantity']; ?></td>
                            <td><img src="../<?php echo htmlspecialchars($row['image_url']); ?>" width="50" alt="<?php echo htmlspecialchars($row['product_name']); ?>"></td>
                            <td class="actions-cell">
                                <div class="action-buttons">
                                    <button class="btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                        <i class="fas fa-edit"></i> <span class="btn-text">Sửa</span>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirmDelete(<?php echo $row['product_id']; ?>)">
                                        <input type="hidden" name="delete_product" value="1">
                                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                        <button type="submit" class="btn-delete">
                                            <i class="fas fa-trash"></i> <span class="btn-text">Xóa</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Sửa Sách -->
    <div id="editModal" class="modal">
        <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2><i class="fas fa-edit"></i> Sửa Thông Tin Sách</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <input type="hidden" name="product_id" id="edit_product_id">
                
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Ảnh bìa hiện tại</label>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <img id="edit_current_image" src="" alt="Ảnh bìa" style="width: 100px; height: 150px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px;">Thay đổi ảnh bìa</label>
                            <input type="file" name="product_image" id="edit_product_image" accept="image/*" style="border: 1px solid #ddd; padding: 8px; border-radius: 4px; width: 100%;">
                            <small style="color: #666; display: block; margin-top: 5px;">Chọn file để thay đổi ảnh bìa (JPG, PNG, GIF)</small>
                        </div>
                    </div>
                </div>
                
                <div style="grid-column: 1 / -1; font-weight: 600; color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 5px; margin-top: 10px;">
                    <i class="fas fa-info-circle"></i> Thông tin cơ bản
                </div>
                
                <div class="form-group">
                    <label>Tên sách <span style="color:red;">*</span></label>
                    <input type="text" name="product_name" id="edit_product_name" required>
                </div>
                
                <div class="form-group">
                    <label>Tác giả <span style="color:red;">*</span></label>
                    <div class="searchable-select-wrapper">
                        <input type="text" 
                               name="author" 
                               id="edit_author" 
                               class="searchable-select-input"
                               placeholder="Tìm kiếm hoặc nhập tên tác giả..."
                               autocomplete="off"
                               required>
                        <div class="searchable-select-dropdown" id="edit_author_dropdown">
                            <?php foreach($authors as $author): ?>
                                <div class="searchable-select-item" data-value="<?php echo htmlspecialchars($author['author_name']); ?>">
                                    <?php echo htmlspecialchars($author['author_name']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Nhà xuất bản</label>
                    <div class="searchable-select-wrapper">
                        <input type="text" 
                               name="publisher" 
                               id="edit_publisher" 
                               class="searchable-select-input"
                               placeholder="Tìm kiếm hoặc nhập tên NXB..."
                               autocomplete="off">
                        <div class="searchable-select-dropdown" id="edit_publisher_dropdown">
                            <?php foreach($publishers as $publisher): ?>
                                <div class="searchable-select-item" data-value="<?php echo htmlspecialchars($publisher['publisher_name']); ?>">
                                    <?php echo htmlspecialchars($publisher['publisher_name']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Năm xuất bản</label>
                    <input type="number" name="publish_year" id="edit_publish_year" min="1900" max="2025">
                </div>
                
                <div class="form-group">
                    <label>Mã ISBN</label>
                    <input type="text" name="isbn" id="edit_isbn">
                </div>
                
                <div class="form-group">
                    <label>Danh mục <span style="color:red;">*</span></label>
                    <select name="category_id" id="edit_category_id" required>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category['category_id']; ?>">
                                <?php echo $category['category_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Bộ sách/Series</label>
                    <input type="text" name="series" id="edit_series">
                </div>
                
                <div style="grid-column: 1 / -1; font-weight: 600; color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 5px; margin-top: 10px;">
                    <i class="fas fa-book-open"></i> Thông tin chi tiết
                </div>
                
                <div class="form-group">
                    <label>Số trang</label>
                    <input type="number" name="pages" id="edit_pages" min="1">
                </div>
                
                <div class="form-group">
                    <label>Ngôn ngữ</label>
                    <select name="language" id="edit_language">
                        <option value="Tiếng Việt">Tiếng Việt</option>
                        <option value="Tiếng Anh">Tiếng Anh</option>
                        <option value="Tiếng Trung">Tiếng Trung</option>
                        <option value="Tiếng Nhật">Tiếng Nhật</option>
                        <option value="Tiếng Hàn">Tiếng Hàn</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Hình thức</label>
                    <select name="book_format" id="edit_book_format">
                        <option value="Bìa mềm">Bìa mềm</option>
                        <option value="Bìa cứng">Bìa cứng</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Kích thước (cm)</label>
                    <input type="text" name="dimensions" id="edit_dimensions" placeholder="VD: 14.5 x 20.5 x 1.5">
                </div>
                
                <div class="form-group">
                    <label>Trọng lượng (gram)</label>
                    <input type="number" name="weight" id="edit_weight" min="1">
                </div>
                
                <div style="grid-column: 1 / -1; font-weight: 600; color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 5px; margin-top: 10px;">
                    <i class="fas fa-tags"></i> Giá và tồn kho
                </div>
                
                <div class="form-group">
                    <label>Giá bán (VNĐ) <span style="color:red;">*</span></label>
                    <input type="number" name="price" id="edit_price" required>
                </div>
                
                <div class="form-group">
                    <label>Tồn kho <span style="color:red;">*</span></label>
                    <input type="number" name="stock_quantity" id="edit_stock_quantity" required>
                </div>
                
                <div class="form-group">
                    <label>Đã bán <span style="color:red;">*</span></label>
                    <input type="number" name="sold_quantity" id="edit_sold_quantity" required>
                </div>
                
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Mô tả / Giới thiệu sách</label>
                    <textarea name="description" id="edit_description" rows="4"></textarea>
                </div>
                
                <div style="grid-column: 1 / -1; display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" name="update_product" style="flex: 1; padding: 12px; background: #28a745; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                    <button type="button" onclick="closeEditModal()" style="flex: 1; padding: 12px; background: #6c757d; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Xử lý xóa sản phẩm
        function confirmDelete(productId) {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm có ID: ' + productId + '?')) {
                return true;
            }
            return false;
        }

        // Xử lý modal sửa sách
        function openEditModal(product) {
            const modal = document.getElementById('editModal');
            modal.style.display = 'flex';
            
            // Điền thông tin vào form
            document.getElementById('edit_product_id').value = product.product_id;
            document.getElementById('edit_product_name').value = product.product_name;
            
            // Hiển thị ảnh hiện tại
            const currentImage = document.getElementById('edit_current_image');
            if (product.image_url) {
                currentImage.src = '../' + product.image_url;
            } else {
                currentImage.src = '../uploads/default-book.jpg';
            }
            document.getElementById('edit_author').value = product.author || '';
            document.getElementById('edit_publisher').value = product.publisher || '';
            document.getElementById('edit_publish_year').value = product.publish_year || '';
            document.getElementById('edit_isbn').value = product.isbn || '';
            document.getElementById('edit_pages').value = product.pages || '';
            document.getElementById('edit_language').value = product.language || 'Tiếng Việt';
            document.getElementById('edit_book_format').value = product.book_format || 'Bìa mềm';
            document.getElementById('edit_dimensions').value = product.dimensions || '';
            document.getElementById('edit_weight').value = product.weight || '';
            document.getElementById('edit_series').value = product.series || '';
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_stock_quantity').value = product.stock_quantity;
            document.getElementById('edit_sold_quantity').value = product.sold_quantity;
            document.getElementById('edit_category_id').value = product.category_id;
            document.getElementById('edit_description').value = product.description;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // Khởi tạo searchable select cho tác giả và NXB
        function initSearchableSelect(inputId, dropdownId) {
            const input = document.getElementById(inputId);
            const dropdown = document.getElementById(dropdownId);
            const items = dropdown.querySelectorAll('.searchable-select-item');
            
            // Hiện dropdown khi focus
            input.addEventListener('focus', function() {
                dropdown.classList.add('show');
                filterItems();
            });
            
            // Lọc items khi gõ
            input.addEventListener('input', function() {
                filterItems();
            });
            
            // Chọn item
            items.forEach(item => {
                item.addEventListener('click', function() {
                    input.value = this.dataset.value;
                    dropdown.classList.remove('show');
                });
            });
            
            // Lọc items theo input
            function filterItems() {
                const searchTerm = input.value.toLowerCase();
                let hasVisibleItems = false;
                
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.classList.remove('hidden');
                        hasVisibleItems = true;
                    } else {
                        item.classList.add('hidden');
                    }
                });
                
                // Hiển thị dropdown nếu có items
                if (hasVisibleItems) {
                    dropdown.classList.add('show');
                } else {
                    dropdown.classList.remove('show');
                }
            }
            
            // Đóng dropdown khi click bên ngoài
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        }
        
        // Khởi tạo khi modal mở
        const originalOpenEditModal = openEditModal;
        openEditModal = function(product) {
            originalOpenEditModal(product);
            
            // Khởi tạo searchable selects
            setTimeout(() => {
                initSearchableSelect('edit_author', 'edit_author_dropdown');
                initSearchableSelect('edit_publisher', 'edit_publisher_dropdown');
            }, 100);
        };
        
        // Filter by category function
        function filterByCategory() {
            const categoryId = document.getElementById('categoryFilterSelect').value;
            if (categoryId === 'all') {
                window.location.href = 'admin_products.php';
            } else {
                window.location.href = 'admin_products.php?category=' + categoryId;
            }
        }
        
        // Search products function
        function searchProducts() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.querySelector('table tbody');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                // Search in: name (1), author (2), category (3)
                for (let j = 1; j <= 3; j++) {
                    if (cells[j]) {
                        const text = cells[j].textContent || cells[j].innerText;
                        if (text.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        }
    </script>
    </main>

    <?php include 'admin_footer.php'; ?>
</body>
</html>
