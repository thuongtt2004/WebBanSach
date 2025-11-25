<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['admin_id'])) {
    header('Location: login_page.php');
    exit();
}

require_once('config/connect.php');
$targetDir = "uploads/";
if (!is_writable($targetDir)) {
    echo "Thư mục $targetDir không có quyền ghi";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product-id'];
    $productName = $_POST['product-name'];
    $author = $_POST['author'] ?? null;
    $publisher = $_POST['publisher'] ?? null;
    $publishYear = $_POST['publish_year'] ?? null;
    $isbn = $_POST['isbn'] ?? null;
    $pages = $_POST['pages'] ?? null;
    $language = $_POST['language'] ?? 'Tiếng Việt';
    $bookFormat = $_POST['book_format'] ?? 'Bìa mềm';
    $dimensions = $_POST['dimensions'] ?? null;
    $weight = $_POST['weight'] ?? null;
    $series = $_POST['series'] ?? null;
    $productPrice = $_POST['product-price'];
    $productQuantity = $_POST['product-quantity'];
    $productDescription = $_POST['product-description'];
    $categoryId = $_POST['category-id'];

    // Xử lý upload hình ảnh
    if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] == 0) {
        $targetDir = "uploads/";
        $fileName = time() . '_' . basename($_FILES["product-image"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Kiểm tra file hình ảnh
        $validExtensions = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $validExtensions)) {
            if (move_uploaded_file($_FILES["product-image"]["tmp_name"], $targetFile)) {
                $imageUrl = $targetFile;
            } else {
                echo "<script>alert('Lỗi khi tải lên hình ảnh!');</script>";
                exit;
            }
        } else {
            echo "<script>alert('Chỉ chấp nhận file JPG, JPEG, PNG & GIF!');</script>";
            exit;
        }
    } else {
        echo "<script>alert('Lỗi khi tải lên hình ảnh!');</script>";
    }

    // Thêm sản phẩm vào database
    $stmt = $conn->prepare("INSERT INTO products (product_id, product_name, author, publisher, publish_year, isbn, pages, language, book_format, dimensions, weight, series, price, description, image_url, stock_quantity, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisssssisdsiis", $productId, $productName, $author, $publisher, $publishYear, $isbn, $pages, $language, $bookFormat, $dimensions, $weight, $series, $productPrice, $productDescription, $imageUrl, $productQuantity, $categoryId);

    if ($stmt->execute()) {
        echo "<script>
            alert('Thêm sản phẩm thành công!');
            window.location.href = 'admin_products.php';
        </script>";
    } else {
        echo "<script>alert('Lỗi: " . $stmt->error . "');</script>";
    }
    $allowedMimes = array('image/jpeg', 'image/png', 'image/gif');
if (!in_array($_FILES['product-image']['type'], $allowedMimes)) {
    echo "<script>alert('Loại file không hợp lệ!');</script>";
    exit;
}
if ($_FILES["product-image"]["size"] > 5000000) { // 5MB
    echo "<script>alert('File quá lớn (tối đa 5MB)');</script>";
    exit;
}
if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] == 0) {
    $targetDir = "uploads/";
    
    // Tạo thư mục nếu chưa tồn tại
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = time() . '_' . basename($_FILES["product-image"]["name"]);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Debug thông tin
    echo "Upload path: " . $targetFile . "<br>";
    echo "File type: " . $imageFileType . "<br>";
    echo "Temp file: " . $_FILES["product-image"]["tmp_name"] . "<br>";
    
    // Kiểm tra file hình ảnh
    $validExtensions = array("jpg", "jpeg", "png", "gif");
    if (in_array($imageFileType, $validExtensions)) {
        if (!move_uploaded_file($_FILES["product-image"]["tmp_name"], $targetFile)) {
            echo "Chi tiết lỗi upload: " . error_get_last()['message'];
            exit;
        }
        $imageUrl = $targetFile;
    } else {
        echo "<script>alert('Chỉ chấp nhận file JPG, JPEG, PNG & GIF!');</script>";
        exit;
    }
}
    $stmt->close();
}

// Lấy danh sách categories cho dropdown
$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sách Mới - TTHUONG BOOKSTORE</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/add_product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .form-group-full {
            grid-column: 1 / -1;
        }
        .section-title {
            grid-column: 1 / -1;
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-top: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <main>
    <div class="container" style="max-width: 1000px; margin: 40px auto; padding: 30px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2><i class="fas fa-book-medical"></i> Thêm Sách Mới</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <!-- Thông tin cơ bản -->
                <div class="section-title"><i class="fas fa-info-circle"></i> Thông tin cơ bản</div>
                
                <div class="form-group">
                    <label for="product-id">Mã sách <span style="color:red;">*</span></label>
                    <input type="text" id="product-id" name="product-id" placeholder="VD: BOOK001" required>
                </div>

                <div class="form-group">
                    <label for="product-name">Tên sách <span style="color:red;">*</span></label>
                    <input type="text" id="product-name" name="product-name" placeholder="VD: Đắc Nhân Tâm" required>
                </div>

                <div class="form-group">
                    <label for="author">Tác giả <span style="color:red;">*</span></label>
                    <input type="text" id="author" name="author" placeholder="VD: Dale Carnegie" required>
                </div>

                <div class="form-group">
                    <label for="publisher">Nhà xuất bản</label>
                    <input type="text" id="publisher" name="publisher" placeholder="VD: NXB Trẻ">
                </div>

                <div class="form-group">
                    <label for="publish_year">Năm xuất bản</label>
                    <input type="number" id="publish_year" name="publish_year" min="1900" max="2025" placeholder="VD: 2024">
                </div>

                <div class="form-group">
                    <label for="isbn">Mã ISBN</label>
                    <input type="text" id="isbn" name="isbn" placeholder="VD: 978-604-1-00001-1">
                </div>

                <div class="form-group">
                    <label for="category-id">Danh mục <span style="color:red;">*</span></label>
                    <select id="category-id" name="category-id" required>
                        <?php 
                        $categories->data_seek(0); // Reset pointer
                        while($category = $categories->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $category['category_id']; ?>">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="series">Bộ sách / Series</label>
                    <input type="text" id="series" name="series" placeholder="VD: Harry Potter Series">
                </div>

                <!-- Thông tin chi tiết sách -->
                <div class="section-title"><i class="fas fa-book-open"></i> Thông tin chi tiết</div>

                <div class="form-group">
                    <label for="pages">Số trang</label>
                    <input type="number" id="pages" name="pages" min="1" placeholder="VD: 320">
                </div>

                <div class="form-group">
                    <label for="language">Ngôn ngữ</label>
                    <select id="language" name="language">
                        <option value="Tiếng Việt" selected>Tiếng Việt</option>
                        <option value="Tiếng Anh">Tiếng Anh</option>
                        <option value="Tiếng Trung">Tiếng Trung</option>
                        <option value="Tiếng Nhật">Tiếng Nhật</option>
                        <option value="Tiếng Hàn">Tiếng Hàn</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="book_format">Hình thức</label>
                    <select id="book_format" name="book_format">
                        <option value="Bìa mềm" selected>Bìa mềm</option>
                        <option value="Bìa cứng">Bìa cứng</option>
                        <option value="Ebook">Ebook</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="dimensions">Kích thước (cm)</label>
                    <input type="text" id="dimensions" name="dimensions" placeholder="VD: 14.5 x 20.5 x 1.5">
                </div>

                <div class="form-group">
                    <label for="weight">Trọng lượng (gram)</label>
                    <input type="number" id="weight" name="weight" min="1" placeholder="VD: 350">
                </div>

                <!-- Giá và tồn kho -->
                <div class="section-title"><i class="fas fa-tags"></i> Giá và tồn kho</div>

                <div class="form-group">
                    <label for="product-price">Giá bán (VNĐ) <span style="color:red;">*</span></label>
                    <input type="number" id="product-price" name="product-price" min="0" placeholder="VD: 89000" required>
                </div>

                <div class="form-group">
                    <label for="product-quantity">Số lượng tồn kho <span style="color:red;">*</span></label>
                    <input type="number" id="product-quantity" name="product-quantity" min="0" placeholder="VD: 100" required>
                </div>

                <!-- Mô tả -->
                <div class="form-group-full">
                    <label for="product-description">Mô tả / Giới thiệu sách <span style="color:red;">*</span></label>
                    <textarea id="product-description" name="product-description" rows="6" placeholder="Nhập mô tả chi tiết về nội dung, tác giả, điểm nổi bật của cuốn sách..." required></textarea>
                </div>

                <!-- Hình ảnh -->
                <div class="form-group-full">
                    <label for="product-image">Hình ảnh bìa sách <span style="color:red;">*</span></label>
                    <input type="file" id="product-image" name="product-image" accept="image/*" required>
                    <img id="preview" class="preview-image" style="display: none; max-width: 300px; margin-top: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="submit-btn" style="flex: 1; padding: 15px; background: #28a745; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-save"></i> Thêm sách
                </button>
                <a href="admin_products.php" style="flex: 1; padding: 15px; background: #6c757d; color: white; border: none; border-radius: 8px; font-weight: 600; text-align: center; text-decoration: none; display: block;">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>
    </main>

    <script>
        // Preview hình ảnh trước khi upload
        document.getElementById('product-image').onchange = function(evt) {
            const [file] = this.files;
            if (file) {
                const preview = document.getElementById('preview');
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        };
    </script>
</body>
</html>
