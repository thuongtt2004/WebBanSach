<?php
require_once('config/connect.php');
require_once 'header.php';

/** @var mysqli $conn */

if (!isset($_SESSION['admin_id'])) {
    header('Location: login_page.php');
    exit();
}

// Lấy danh sách dropdown
$authors = $conn->query("SELECT author_name FROM authors ORDER BY author_name ASC");
$publishers = $conn->query("SELECT publisher_name FROM publishers ORDER BY publisher_name ASC");
$categories = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];
    $categoryId = $_POST['category_id'];
    $productName = $_POST['product_name'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $publishYear = $_POST['publish_year'];
    $isbn = $_POST['isbn'];
    $pages = $_POST['pages'];
    $language = $_POST['language'];
    $bookFormat = $_POST['book_format'];
    $dimensions = $_POST['dimensions'];
    $weight = $_POST['weight'];
    $series = $_POST['series'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stockQuantity = $_POST['stock_quantity'];
    $soldQuantity = $_POST['sold_quantity'];
    $imageUrl = '';
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == 0) {
        $targetDir = "uploads/";
        $fileName = time() . '_' . basename($_FILES["image_url"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $validExtensions = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $validExtensions)) {
            if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $targetFile)) {
                $imageUrl = $targetFile;
            }
        }
    }
    $stmt = $conn->prepare("INSERT INTO products (product_id, category_id, product_name, author, publisher, publish_year, isbn, pages, language, book_format, dimensions, weight, series, price, description, image_url, stock_quantity, sold_quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssissssdsdssii", $productId, $categoryId, $productName, $author, $publisher, $publishYear, $isbn, $pages, $language, $bookFormat, $dimensions, $weight, $series, $price, $description, $imageUrl, $stockQuantity, $soldQuantity);
    if ($stmt->execute()) {
        echo "<script>alert('Thêm sản phẩm thành công!');window.location.href='admin/admin_products.php';</script>";
    } else {
        echo "<script>alert('Lỗi: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản Phẩm Đầy Đủ - TTHUONG Bookstore</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/fontawesome/all.min.css">
    <style>
        .container {max-width: 900px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px;}
        .form-grid {display: grid; grid-template-columns: 1fr 1fr; gap: 20px;}
        .form-group-full {grid-column: 1 / -1;}
        label {font-weight: 500; display: block; margin-bottom: 5px;}
        
        /* Searchable Select Style */
        .searchable-select {
            position: relative;
        }
        .searchable-select input[type="text"] {
            width: 100%;
            padding: 8px 35px 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .searchable-select .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            pointer-events: none;
        }
        .searchable-select .dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 250px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
            display: none;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .searchable-select .dropdown-list.show {
            display: block;
        }
        .searchable-select .dropdown-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }
        .searchable-select .dropdown-item:hover {
            background: #f8f9fa;
        }
        .searchable-select .dropdown-item.no-results {
            color: #999;
            cursor: default;
        }
        .searchable-select .dropdown-item.no-results:hover {
            background: white;
        }
    </style>
</head>
<body>
<?php include 'admin/admin_header.php'; ?>
<main>
<div class="container">
    <h2><i class="fas fa-plus"></i> Thêm Sản Phẩm Mới</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label>Mã sách *</label>
                <input type="text" name="product_id" required>
            </div>
            <div class="form-group">
                <label>Danh mục *</label>
                <select name="category_id" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tên sách *</label>
                <input type="text" name="product_name" required>
            </div>
            <div class="form-group">
                <label>Tác giả *</label>
                <div class="searchable-select">
                    <input type="text" id="authorSearch" placeholder="Tìm kiếm tác giả..." autocomplete="off">
                    <i class="fas fa-search search-icon"></i>
                    <input type="hidden" name="author" id="authorValue" required>
                    <div class="dropdown-list" id="authorDropdown">
                        <?php 
                        $authors->data_seek(0);
                        while($a = $authors->fetch_assoc()): ?>
                            <div class="dropdown-item" data-value="<?php echo htmlspecialchars($a['author_name']); ?>">
                                <?php echo htmlspecialchars($a['author_name']); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Nhà xuất bản *</label>
                <div class="searchable-select">
                    <input type="text" id="publisherSearch" placeholder="Tìm kiếm NXB..." autocomplete="off">
                    <i class="fas fa-search search-icon"></i>
                    <input type="hidden" name="publisher" id="publisherValue" required>
                    <div class="dropdown-list" id="publisherDropdown">
                        <?php 
                        $publishers->data_seek(0);
                        while($p = $publishers->fetch_assoc()): ?>
                            <div class="dropdown-item" data-value="<?php echo htmlspecialchars($p['publisher_name']); ?>">
                                <?php echo htmlspecialchars($p['publisher_name']); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Năm xuất bản</label>
                <input type="number" name="publish_year" min="1900" max="2025">
            </div>
            <div class="form-group">
                <label>Mã ISBN</label>
                <input type="text" name="isbn">
            </div>
            <div class="form-group">
                <label>Số trang</label>
                <input type="number" name="pages" min="1">
            </div>
            <div class="form-group">
                <label>Ngôn ngữ</label>
                <input type="text" name="language" value="Tiếng Việt">
            </div>
            <div class="form-group">
                <label>Hình thức</label>
                <select name="book_format">
                    <option value="Bìa mềm">Bìa mềm</option>
                    <option value="Bìa cứng">Bìa cứng</option>
                </select>
            </div>
            <div class="form-group">
                <label>Kích thước (cm)</label>
                <input type="text" name="dimensions">
            </div>
            <div class="form-group">
                <label>Trọng lượng (gram)</label>
                <input type="number" name="weight" min="1">
            </div>
            <div class="form-group">
                <label>Bộ sách/Series</label>
                <input type="text" name="series">
            </div>
            <div class="form-group">
                <label>Giá bán (VNĐ) *</label>
                <input type="number" name="price" min="0" required>
            </div>
            <div class="form-group">
                <label>Số lượng tồn kho *</label>
                <input type="number" name="stock_quantity" min="0" required>
            </div>
            <div class="form-group">
                <label>Số lượng đã bán</label>
                <input type="number" name="sold_quantity" min="0" value="0">
            </div>
            <div class="form-group-full">
                <label>Mô tả / Giới thiệu sách *</label>
                <textarea name="description" rows="5" required></textarea>
            </div>
            <div class="form-group-full">
                <label>Hình ảnh bìa sách *</label>
                <input type="file" name="image_url" accept="image/*" required>
            </div>
        </div>
        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" style="flex: 1; padding: 15px; background: #28a745; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-save"></i> Thêm sản phẩm
            </button>
            <a href="admin/admin_products.php" style="flex: 1; padding: 15px; background: #6c757d; color: white; border: none; border-radius: 8px; font-weight: 600; text-align: center; text-decoration: none; display: block;">
                <i class="fas fa-times"></i> Hủy
            </a>
        </div>
    </form>
</div>
</main>

<script>
// Searchable Select Component
function initSearchableSelect(searchInputId, dropdownId, hiddenInputId) {
    const searchInput = document.getElementById(searchInputId);
    const dropdown = document.getElementById(dropdownId);
    const hiddenInput = document.getElementById(hiddenInputId);
    const items = dropdown.querySelectorAll('.dropdown-item');
    
    // Show dropdown when input is focused
    searchInput.addEventListener('focus', function() {
        dropdown.classList.add('show');
        filterItems('');
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.closest('.searchable-select').contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
    
    // Filter items when typing
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterItems(searchTerm);
        dropdown.classList.add('show');
        
        // Clear hidden value when typing
        if (hiddenInput.value && this.value !== hiddenInput.value) {
            hiddenInput.value = '';
        }
    });
    
    // Select item when clicked
    items.forEach(item => {
        item.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            searchInput.value = value;
            hiddenInput.value = value;
            dropdown.classList.remove('show');
        });
    });
    
    // Filter function
    function filterItems(searchTerm) {
        let visibleCount = 0;
        
        items.forEach(item => {
            if (!item.classList.contains('no-results')) {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            }
        });
        
        // Show no results message
        let noResultsDiv = dropdown.querySelector('.no-results');
        if (visibleCount === 0) {
            if (!noResultsDiv) {
                noResultsDiv = document.createElement('div');
                noResultsDiv.className = 'dropdown-item no-results';
                noResultsDiv.textContent = 'Không tìm thấy kết quả';
                dropdown.appendChild(noResultsDiv);
            }
            noResultsDiv.style.display = 'block';
        } else {
            if (noResultsDiv) {
                noResultsDiv.style.display = 'none';
            }
        }
    }
}

// Initialize both searchable selects
initSearchableSelect('authorSearch', 'authorDropdown', 'authorValue');
initSearchableSelect('publisherSearch', 'publisherDropdown', 'publisherValue');
</script>

</body>
</html>
