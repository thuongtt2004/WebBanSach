<?php
require_once 'config/connect.php';

// Lấy tên tác giả từ URL
$author = isset($_GET['author']) ? $_GET['author'] : '';

if (empty($author)) {
    header('Location: authors.php');
    exit();
}

// Lấy danh sách sách của tác giả
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.author = ?
        ORDER BY p.publish_year DESC, p.product_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $author);
$stmt->execute();
$result = $stmt->get_result();

// Đếm số sách
$book_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($author); ?> - TTHUONG Bookstore</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/sanpham.css">
    <link rel="stylesheet" href="css/author_detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php require_once 'header.php'; ?>

    <section class="author-detail-section">
        <div class="author-header">
            <div class="author-avatar-large">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="author-info">
                <h1><i class="fas fa-user-edit"></i> <?php echo htmlspecialchars($author); ?></h1>
                <p class="author-stats">
                    <span><i class="fas fa-book"></i> <?php echo $book_count; ?> cuốn sách</span>
                </p>
                <a href="authors.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách tác giả
                </a>
            </div>
        </div>

        <div class="books-section">
            <h2>📚 Sách của <?php echo htmlspecialchars($author); ?></h2>
            <div class="products">
                <?php
                if ($book_count > 0) {
                    while($row = $result->fetch_assoc()) {
                        ?>
                        <div class="product" data-category="<?php echo $row['category_id']; ?>" 
                             data-price="<?php echo $row['price']; ?>" 
                             data-name="<?php echo htmlspecialchars($row['product_name']); ?>"
                             data-author="<?php echo htmlspecialchars($row['author'] ?? ''); ?>"
                             data-language="<?php echo htmlspecialchars($row['language'] ?? ''); ?>"
                             data-format="<?php echo htmlspecialchars($row['book_format'] ?? ''); ?>"
                             onclick="showProductDetails(
                                '<?php echo addslashes($row['product_name']); ?>', 
                                '<?php echo addslashes($row['description']); ?>', 
                                '<?php echo number_format($row['price'], 0, ',', '.'); ?>', 
                                '<?php echo htmlspecialchars($row['image_url']); ?>', 
                                '<?php echo htmlspecialchars($row['product_id']); ?>', 
                                '<?php echo htmlspecialchars($row['category_name']); ?>', 
                                <?php echo $row['stock_quantity']; ?>,
                                '<?php echo addslashes($row['author'] ?? ''); ?>',
                                '<?php echo addslashes($row['publisher'] ?? ''); ?>',
                                '<?php echo $row['publish_year'] ?? ''; ?>',
                                '<?php echo htmlspecialchars($row['isbn'] ?? ''); ?>',
                                <?php echo $row['pages'] ?? 0; ?>,
                                '<?php echo htmlspecialchars($row['language'] ?? ''); ?>',
                                '<?php echo htmlspecialchars($row['book_format'] ?? ''); ?>'
                            )">
                            <button class="wishlist-btn" onclick="event.stopPropagation(); toggleWishlist('<?php echo $row['product_id']; ?>', this);" title="Thêm vào yêu thích">
                                <i class="far fa-heart"></i>
                            </button>
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                            <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                            <?php if (!empty($row['author'])): ?>
                            <p class="book-author"><i class="fas fa-user-edit"></i> <?php echo htmlspecialchars($row['author']); ?></p>
                            <?php endif; ?>
                            <p class="book-price"><?php echo number_format($row['price'], 0, ',', '.'); ?> VNĐ</p>
                            <?php if (!empty($row['book_format'])): ?>
                            <span class="book-format-badge"><?php echo htmlspecialchars($row['book_format']); ?></span>
                            <?php endif; ?>
                            <div class="button-group">
                                <button onclick="event.stopPropagation(); addToCart('<?php echo $row['product_id']; ?>', 
                                                         '<?php echo addslashes($row['product_name']); ?>', 
                                                         <?php echo $row['price']; ?>)" 
                                        class="add-to-cart">
                                    Thêm vào giỏ hàng
                                </button>
                                <button onclick="event.stopPropagation(); window.location.href='order.php?id=<?php echo $row['product_id']; ?>'" 
                                        class="buy-now">
                                    Mua ngay
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>Không có sách nào của tác giả này.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Modal chi tiết sản phẩm -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="product-details">
                <div class="product-image">
                    <img id="modalImage" src="" alt="">
                </div>
                <div class="product-info">
                    <h2 id="modalTitle"></h2>
                    <p class="modal-author" id="modalAuthorContainer" style="display: none;"><i class="fas fa-user-edit"></i> <strong>Tác giả:</strong> <span id="modalAuthor"></span></p>
                    <p><strong>Mã ISBN:</strong> <span id="modalIsbn"></span></p>
                    <p><strong>Danh mục:</strong> <span id="modalCategory"></span></p>
                    
                    <div class="book-details" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 15px 0;">
                        <p id="modalPublisherContainer" style="display: none;"><strong>NXB:</strong> <span id="modalPublisher"></span></p>
                        <p id="modalYearContainer" style="display: none;"><strong>Năm XB:</strong> <span id="modalYear"></span></p>
                        <p id="modalPagesContainer" style="display: none;"><strong>Số trang:</strong> <span id="modalPages"></span></p>
                        <p id="modalLanguageContainer" style="display: none;"><strong>Ngôn ngữ:</strong> <span id="modalLanguage"></span></p>
                        <p id="modalFormatContainer" style="display: none;"><strong>Hình thức:</strong> <span id="modalFormat"></span></p>
                        <p><strong>Tồn kho:</strong> <span id="modalStock"></span></p>
                    </div>
                    
                    <p class="modal-price" style="font-size: 24px; color: #dc3545; font-weight: bold; margin: 15px 0;"><span id="modalPrice"></span> VNĐ</p>
                    
                    <div class="modal-description">
                        <p><strong>Giới thiệu sách:</strong></p>
                        <p id="modalDescription"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showProductDetails(name, description, price, image, id, category, stock, author, publisher, year, isbn, pages, language, format) {
            document.getElementById('modalTitle').textContent = name;
            document.getElementById('modalDescription').textContent = description;
            document.getElementById('modalPrice').textContent = price;
            document.getElementById('modalImage').src = image;
            document.getElementById('modalCategory').textContent = category;
            document.getElementById('modalStock').textContent = stock;
            document.getElementById('modalIsbn').textContent = isbn || 'Chưa cập nhật';
            
            if (author) {
                document.getElementById('modalAuthor').textContent = author;
                document.getElementById('modalAuthorContainer').style.display = 'block';
            } else {
                document.getElementById('modalAuthorContainer').style.display = 'none';
            }
            
            if (publisher) {
                document.getElementById('modalPublisher').textContent = publisher;
                document.getElementById('modalPublisherContainer').style.display = 'block';
            } else {
                document.getElementById('modalPublisherContainer').style.display = 'none';
            }
            
            if (year) {
                document.getElementById('modalYear').textContent = year;
                document.getElementById('modalYearContainer').style.display = 'block';
            } else {
                document.getElementById('modalYearContainer').style.display = 'none';
            }
            
            if (pages && pages > 0) {
                document.getElementById('modalPages').textContent = pages + ' trang';
                document.getElementById('modalPagesContainer').style.display = 'block';
            } else {
                document.getElementById('modalPagesContainer').style.display = 'none';
            }
            
            if (language) {
                document.getElementById('modalLanguage').textContent = language;
                document.getElementById('modalLanguageContainer').style.display = 'block';
            } else {
                document.getElementById('modalLanguageContainer').style.display = 'none';
            }
            
            if (format) {
                document.getElementById('modalFormat').textContent = format;
                document.getElementById('modalFormatContainer').style.display = 'block';
            } else {
                document.getElementById('modalFormatContainer').style.display = 'none';
            }
            
            document.getElementById('productModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('productModal')) {
                closeModal();
            }
        }

        function addToCart(productId, productName, price) {
            event.stopPropagation();
            
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đã thêm ' + productName + ' vào giỏ hàng!');
                } else if (data.message === 'not_logged_in') {
                    if (confirm('Bạn cần đăng nhập để thêm vào giỏ hàng. Đến trang đăng nhập?')) {
                        window.location.href = 'login_page.php';
                    }
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra: ' + error.message);
            });
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
