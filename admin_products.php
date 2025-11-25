<?php
session_start();
require_once 'config/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login_page.php');
    exit();
}

// Xử lý xóa sản phẩm
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    
    try {
        // Debug để kiểm tra ID sản phẩm
        error_log("Đang xóa sản phẩm ID: " . $product_id);
        
        // Bắt đầu transaction
        $conn->begin_transaction();
        
        // Sử dụng Prepared Statement để tránh SQL injection
        $delete_query = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $delete_query->bind_param("s", $product_id);
        
        if ($delete_query->execute()) {
            $conn->commit();
            echo "<script>
                alert('Xóa sản phẩm ID: " . $product_id . " thành công!');
                window.location.href = 'admin_products.php';
            </script>";
        } else {
            throw new Exception($conn->error);
        }
        
        $delete_query->close();
        
    } catch (Exception $e) {
        $conn->rollback();
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

    $stmt = $conn->prepare("UPDATE products SET product_name = ?, author = ?, publisher = ?, publish_year = ?, isbn = ?, pages = ?, language = ?, book_format = ?, dimensions = ?, weight = ?, series = ?, price = ?, stock_quantity = ?, sold_quantity = ?, category_id = ?, description = ? WHERE product_id = ?");
    $stmt->bind_param("sssisssssidiiidis", $product_name, $author, $publisher, $publish_year, $isbn, $pages, $language, $book_format, $dimensions, $weight, $series, $price, $stock_quantity, $sold_quantity, $category_id, $description, $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Cập nhật sách thành công!');</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật sách: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

// Lấy danh sách sản phẩm
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        ORDER BY p.product_id DESC";
$result = $conn->query($sql);

// Lấy danh sách danh mục cho form cập nhật
$categories_sql = "SELECT * FROM categories";
$categories_result = $conn->query($categories_sql);
$categories = [];
while ($category = $categories_result->fetch_assoc()) {
    $categories[] = $category;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sách - TTHUONG Bookstore</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/admin_products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <main>
    <div class="container">
        <h1><i class="fas fa-book"></i> Quản Lý Sách</h1>
        
        <div class="button-container">
            <a href="add_product.php" class="add-product-btn">
                <i class="fas fa-plus"></i> Thêm Sách Mới
            </a>
        </div>
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
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['product_id']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['author'] ?? '-'; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td><?php echo number_format($row['price'], 0, ',', '.'); ?> VNĐ</td>
                        <td><?php echo $row['stock_quantity']; ?></td>
                        <td><?php echo $row['sold_quantity']; ?></td>
                        <td><img src="<?php echo $row['image_url']; ?>" width="50"></td>
                        <td>
                            <button class="btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                Sửa
                            </button>
                            <form method="POST" style="display: inline;" onsubmit="return confirmDelete(<?php echo $row['product_id']; ?>)">
                                <input type="hidden" name="delete_product" value="1">
                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                <button type="submit" class="btn-delete">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Sửa Sách -->
    <div id="editModal" class="modal">
        <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2><i class="fas fa-edit"></i> Sửa Thông Tin Sách</h2>
            <form id="editForm" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <input type="hidden" name="product_id" id="edit_product_id">
                
                <div style="grid-column: 1 / -1; font-weight: 600; color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 5px; margin-top: 10px;">
                    <i class="fas fa-info-circle"></i> Thông tin cơ bản
                </div>
                
                <div class="form-group">
                    <label>Tên sách <span style="color:red;">*</span></label>
                    <input type="text" name="product_name" id="edit_product_name" required>
                </div>
                
                <div class="form-group">
                    <label>Tác giả <span style="color:red;">*</span></label>
                    <input type="text" name="author" id="edit_author" required>
                </div>
                
                <div class="form-group">
                    <label>Nhà xuất bản</label>
                    <input type="text" name="publisher" id="edit_publisher">
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
                        <option value="Ebook">Ebook</option>
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
    </script>
    </main>

    <?php include 'admin_footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
