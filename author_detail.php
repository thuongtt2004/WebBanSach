<?php
require_once 'config/connect.php';

// Lấy tên tác giả từ URL
$author = isset($_GET['author']) ? $_GET['author'] : '';

if (empty($author)) {
    header('Location: authors.php');
    exit();
}

// Lấy danh sách sách của tác giả với rating
$sql = "SELECT p.*, c.category_name,
        COALESCE(AVG(r.rating), 0) as average_rating,
        COUNT(r.review_id) as review_count
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        LEFT JOIN reviews r ON p.product_id = r.product_id
        WHERE p.author = ?
        GROUP BY p.product_id
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
                            
                            <!-- Rating ở giữa card -->
                            <div class="product-rating" style="margin: 8px 0; display: flex; align-items: center; justify-content: center; gap: 5px;">
                                <?php if ($row['review_count'] > 0): ?>
                                    <div style="color: #ffc107; font-size: 14px;">
                                        <?php 
                                        $avg_rating = round($row['average_rating'], 1);
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= floor($avg_rating)) {
                                                echo '★';
                                            } elseif ($i - 0.5 <= $avg_rating) {
                                                echo '⯨';
                                            } else {
                                                echo '☆';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <span style="color: #666; font-size: 13px;"><?php echo number_format($avg_rating, 1); ?> (<?php echo $row['review_count']; ?>)</span>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 13px; font-style: italic;">Chưa có đánh giá</span>
                                <?php endif; ?>
                            </div>
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
                    
                    <!-- Phần đánh giá -->
                    <div class="modal-reviews" id="modalReviews" style="margin-top: 20px; border-top: 2px solid #eee; padding-top: 20px;">
                        <h3 style="margin-bottom: 15px;"><i class="fas fa-star" style="color: #ffc107;"></i> Đánh giá sản phẩm</h3>
                        <div id="reviewsContent">
                            <p style="text-align: center; color: #999;">Đang tải đánh giá...</p>
                        </div>
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
            
            // Load reviews cho sản phẩm
            loadProductReviews(id);
            
            document.getElementById('productModal').style.display = 'block';
        }
        
        function loadProductReviews(productId) {
            fetch('get_product_reviews.php?product_id=' + productId)
                .then(response => response.json())
                .then(data => {
                    const reviewsContent = document.getElementById('reviewsContent');
                    
                    if (data.success) {
                        let html = '';
                        
                        // Hiển thị rating trung bình
                        if (data.average_rating > 0) {
                            html += '<div style="background:#f8f9fa;padding:15px;border-radius:8px;margin-bottom:20px;">';
                            html += '<div style="display:flex;align-items:center;gap:15px;">';
                            html += '<div style="text-align:center;">';
                            html += '<div style="font-size:36px;font-weight:bold;color:#ffc107;">' + data.average_rating.toFixed(1) + '</div>';
                            html += '<div style="color:#ffc107;font-size:20px;">';
                            for (let i = 1; i <= 5; i++) {
                                if (i <= Math.floor(data.average_rating)) {
                                    html += '★';
                                } else if (i - 0.5 <= data.average_rating) {
                                    html += '⯨';
                                } else {
                                    html += '☆';
                                }
                            }
                            html += '</div>';
                            html += '<div style="color:#666;font-size:14px;margin-top:5px;">' + data.total_reviews + ' đánh giá</div>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        }
                        
                        // Hiển thị danh sách reviews
                        if (data.reviews && data.reviews.length > 0) {
                            html += '<div style="max-height:400px;overflow-y:auto;">';
                            data.reviews.forEach(review => {
                                html += '<div style="border-bottom:1px solid #eee;padding:15px 0;">';
                                html += '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">';
                                html += '<strong style="color:#333;">' + review.user_name + '</strong>';
                                html += '<span style="color:#999;font-size:13px;">' + review.created_at + '</span>';
                                html += '</div>';
                                html += '<div style="color:#ffc107;margin-bottom:8px;">';
                                for (let i = 1; i <= 5; i++) {
                                    html += i <= review.rating ? '★' : '☆';
                                }
                                html += '</div>';
                                html += '<p style="color:#666;margin:0;">' + review.content + '</p>';
                                html += '</div>';
                            });
                            html += '</div>';
                        } else {
                            html += '<p style="text-align:center;color:#999;padding:20px;">Chưa có đánh giá nào cho sản phẩm này</p>';
                        }
                        
                        reviewsContent.innerHTML = html;
                    } else {
                        reviewsContent.innerHTML = '<p style="text-align:center;color:#999;">Không thể tải đánh giá</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading reviews:', error);
                    document.getElementById('reviewsContent').innerHTML = '<p style="text-align:center;color:#999;">Lỗi khi tải đánh giá</p>';
                });
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
