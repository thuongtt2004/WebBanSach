<?php
require_once 'config/connect.php';

// Lấy flash sale đang hoạt động
$now = date('Y-m-d H:i:s');
$flash_sale_query = "SELECT * FROM promotions 
                     WHERE promotion_type = 'flash_sale' 
                     AND status = 'active' 
                     AND '$now' BETWEEN start_date AND end_date 
                     ORDER BY discount_value DESC 
                     LIMIT 1";
$flash_sale_result = $conn->query($flash_sale_query);
$flash_sale = $flash_sale_result->num_rows > 0 ? $flash_sale_result->fetch_assoc() : null;

// Lấy các khuyến mãi nổi bật khác
$promotions_query = "SELECT * FROM promotions 
                     WHERE status = 'active' 
                     AND '$now' BETWEEN start_date AND end_date 
                     AND promotion_type IN ('coupon', 'minimum_order')
                     ORDER BY created_at DESC 
                     LIMIT 3";
$promotions_result = $conn->query($promotions_query);

// Lấy 4 sản phẩm mới nhất với rating
$sql = "SELECT p.*, c.category_name,
        COALESCE(AVG(r.rating), 0) as average_rating,
        COUNT(r.review_id) as review_count
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        LEFT JOIN reviews r ON p.product_id = r.product_id
        GROUP BY p.product_id
        ORDER BY p.product_id DESC 
        LIMIT 5";
$result = $conn->query($sql);

if (!$result) {
    die("Lỗi truy vấn: " . $conn->error);
}

// Lấy top 4 sách bán chạy trong 1 tháng gần đây với rating
$bestsellers_query = "SELECT p.*, c.category_name,
                      COALESCE(SUM(od.quantity), 0) as total_sold,
                      COALESCE(AVG(r.rating), 0) as average_rating,
                      COUNT(DISTINCT r.review_id) as review_count
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.category_id
                      LEFT JOIN order_details od ON p.product_id = od.product_id
                      LEFT JOIN orders o ON od.order_id = o.order_id
                      LEFT JOIN reviews r ON p.product_id = r.product_id
                      WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                      AND o.order_status IN ('Hoàn thành', 'Đang giao', 'Đã xác nhận')
                      GROUP BY p.product_id
                      ORDER BY total_sold DESC, p.sold_quantity DESC
                      LIMIT 4";
$bestsellers_result = $conn->query($bestsellers_query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TTHUONG Bookstore - Nhà sách trực tuyến</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/trangchu.css">
    <link rel="stylesheet" href="css/promotions.css">
    <link rel="stylesheet" href="css/mobile-optimization.css">
    <link rel="stylesheet" href="css/mobile-375px.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php require_once 'header.php'; ?>

    <!-- Flash Sale Banner -->
    <?php if ($flash_sale): ?>
    <section class="flash-sale-banner">
        <div class="flash-sale-content">
            <div class="flash-sale-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="flash-sale-info">
                <h2><i class="fas fa-fire"></i> FLASH SALE - <?php echo htmlspecialchars($flash_sale['promotion_name']); ?></h2>
                <p class="flash-sale-desc">
                    Giảm ngay 
                    <strong>
                        <?php echo $flash_sale['discount_type'] == 'percentage' 
                            ? $flash_sale['discount_value'] . '%' 
                            : number_format($flash_sale['discount_value']) . 'đ'; ?>
                    </strong>
                    cho toàn bộ đơn hàng!
                    <?php if ($flash_sale['min_order_amount'] > 0): ?>
                        <span class="min-order">Đơn tối thiểu: <?php echo number_format($flash_sale['min_order_amount']); ?>đ</span>
                    <?php endif; ?>
                </p>
                <p class="flash-sale-time">
                    <i class="fas fa-clock"></i> Kết thúc: 
                    <span class="countdown" data-end="<?php echo $flash_sale['end_date']; ?>"></span>
                </p>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Promotions Section -->
    <?php if ($promotions_result->num_rows > 0): ?>
    <section class="promotions-section">
        <h2><i class="fas fa-tags"></i> Khuyến mãi hot</h2>
        <div class="promotions-grid">
            <?php while ($promo = $promotions_result->fetch_assoc()): ?>
            <div class="promo-card">
                <div class="promo-badge">
                    <?php 
                    $badge_icon = $promo['promotion_type'] == 'coupon' ? 'ticket-alt' : 'gift';
                    ?>
                    <i class="fas fa-<?php echo $badge_icon; ?>"></i>
                </div>
                <div class="promo-content">
                    <h3><?php echo htmlspecialchars($promo['promotion_name']); ?></h3>
                    <p class="promo-discount">
                        Giảm <strong>
                            <?php echo $promo['discount_type'] == 'percentage' 
                                ? $promo['discount_value'] . '%' 
                                : number_format($promo['discount_value']) . 'đ'; ?>
                        </strong>
                    </p>
                    <?php if ($promo['promotion_type'] == 'coupon'): ?>
                        <div class="promo-code">
                            Mã: <span class="code-text"><?php echo htmlspecialchars($promo['promotion_code']); ?></span>
                            <button class="copy-code" onclick="copyCode('<?php echo $promo['promotion_code']; ?>')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if ($promo['min_order_amount'] > 0): ?>
                        <p class="promo-condition">
                            <i class="fas fa-info-circle"></i> Đơn từ <?php echo number_format($promo['min_order_amount']); ?>đ
                        </p>
                    <?php endif; ?>
                    <p class="promo-expire">
                        HSD: <?php echo date('d/m/Y', strtotime($promo['end_date'])); ?>
                    </p>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php endif; ?>

    <section id="home">
        <div class="slideshow-container">
            <div class="mySlides fade">
                <img src="uploads/banner1.png" style="width:100%">
            </div>
            <div class="mySlides fade">
                <img src="uploads/banner3.png" style="width:100%">
            </div>
            <div class="mySlides fade">
                <img src="uploads/banner2.png" style="width:100%">
            </div>
        </div>
        <h2>Chào mừng đến với TTHUONG Bookstore</h2>
        <p>TTHUONG Bookstore - "Mở ra thế giới kiến thức qua từng trang sách"</p>
        <p>TTHUONG Bookstore là điểm đến hoàn hảo cho những người yêu thích đọc sách và khát khao kiến thức. Với kho sách phong phú bao gồm văn học, kinh tế, tâm lý, thiếu nhi, và nhiều thể loại khác, chúng tôi không chỉ cung cấp sách mà còn truyền cảm hứng cho hành trình khám phá của bạn.</p>
        <p>Từng cuốn sách tại TTHUONG Bookstore đều được chọn lọc kỹ lưỡng, từ những tác phẩm kinh điển đến những ấn phẩm mới nhất, giúp bạn dễ dàng tìm thấy cuốn sách ưng ý cho riêng mình.</p>
        <p>Hãy để TTHUONG Bookstore đồng hành cùng bạn trong hành trình phát triển bản thân và biến mỗi khoảnh khắc đọc sách trở nên ý nghĩa.</p>
    </section>

    <!-- Bestsellers Section -->
    <section id="bestsellers">
        <h2>🔥 Sách bán chạy tháng này</h2>
        <div class="bestsellers-container">
            <?php
            if ($bestsellers_result && $bestsellers_result->num_rows > 0) {
                $rank = 1;
                while($book = $bestsellers_result->fetch_assoc()) {
                    ?>
                    <div class="bestseller-item" onclick="showProductDetails(
                        '<?php echo htmlspecialchars($book['product_name']); ?>',
                        '<?php echo htmlspecialchars($book['description']); ?>',
                        '<?php echo number_format($book['price'], 0, ',', '.'); ?>',
                        '<?php echo htmlspecialchars($book['image_url']); ?>',
                        '<?php echo htmlspecialchars($book['product_id']); ?>',
                        '<?php echo htmlspecialchars($book['category_name']); ?>',
                        <?php echo $book['stock_quantity']; ?>,
                        '<?php echo addslashes($book['author'] ?? ''); ?>',
                        '<?php echo addslashes($book['publisher'] ?? ''); ?>',
                        '<?php echo $book['publish_year'] ?? ''; ?>',
                        '<?php echo htmlspecialchars($book['isbn'] ?? ''); ?>',
                        <?php echo $book['pages'] ?? 0; ?>,
                        '<?php echo htmlspecialchars($book['language'] ?? ''); ?>',
                        '<?php echo htmlspecialchars($book['book_format'] ?? ''); ?>'
                    )">
                        <div class="bestseller-rank">
                            <span class="rank-number">#<?php echo $rank; ?></span>
                        </div>
                        <div class="bestseller-image">
                            <img src="<?php echo htmlspecialchars($book['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($book['product_name']); ?>">
                            <?php if ($rank <= 3): ?>
                                <div class="hot-badge">
                                    <i class="fas fa-fire"></i> HOT
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="bestseller-info">
                            <h3><?php echo htmlspecialchars($book['product_name']); ?></h3>
                            <?php if (!empty($book['author'])): ?>
                            <p class="bestseller-author"><i class="fas fa-user-edit"></i> <?php echo htmlspecialchars($book['author']); ?></p>
                            <?php endif; ?>
                            <p class="bestseller-price"><?php echo number_format($book['price'], 0, ',', '.'); ?> VNĐ</p>
                            
                            <!-- Rating ở giữa card -->
                            <div class="product-rating" style="margin: 8px 0; display: flex; align-items: center; justify-content: center; gap: 5px;">
                                <?php if ($book['review_count'] > 0): ?>
                                    <div style="color: #ffc107; font-size: 14px;">
                                        <?php 
                                        $avg_rating = round($book['average_rating'], 1);
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
                                    <span style="color: #666; font-size: 13px;"><?php echo number_format($avg_rating, 1); ?> (<?php echo $book['review_count']; ?>)</span>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 13px; font-style: italic;">Chưa có đánh giá</span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="bestseller-sold">
                                <i class="fas fa-chart-line"></i> Đã bán: <strong><?php echo $book['total_sold'] ?? $book['sold_quantity']; ?></strong>
                            </p>
                            <div class="bestseller-buttons">
                                <button onclick="event.stopPropagation(); addToCart('<?php echo $book['product_id']; ?>', 
                                                         '<?php echo addslashes($book['product_name']); ?>', 
                                                         <?php echo $book['price']; ?>)" 
                                        class="btn-add-cart">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                </button>
                                <button onclick="event.stopPropagation(); window.location.href='buy_now_page.php?id=<?php echo $book['product_id']; ?>'" 
                                        class="btn-buy-now">
                                    Mua ngay
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                    $rank++;
                }
            } else {
                echo "<p style='text-align: center; color: #666;'>Chưa có dữ liệu bán hàng trong tháng này.</p>";
            }
            ?>
        </div>
    </section>

    <section id="products">
        <h2>📚 Sách mới nhất</h2>
        <div class="products">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <div class="product" onclick="showProductDetails(
                        '<?php echo htmlspecialchars($row['product_name']); ?>',
                        '<?php echo htmlspecialchars($row['description']); ?>',
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
                        <div class="new-badge">
                            <i class="fas fa-star"></i> NEW
                        </div>
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
                        <div class="button-group">
                            <button onclick="event.stopPropagation(); addToCart('<?php echo $row['product_id']; ?>', 
                                                     '<?php echo addslashes($row['product_name']); ?>', 
                                                     <?php echo $row['price']; ?>)" 
                                    class="add-to-cart">
                                Thêm vào giỏ hàng
                            </button>
                            <button onclick="event.stopPropagation(); window.location.href='buy_now_page.php?id=<?php echo $row['product_id']; ?>'" 
                                    class="buy-now">
                                Mua ngay
                            </button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>Không có sản phẩm nào.</p>";
            }
            ?>
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
        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";  
            }
            
            slideIndex++;
            if (slideIndex > slides.length) {slideIndex = 1}
            
            slides[slideIndex-1].style.display = "block";
            setTimeout(showSlides, 4000);
        }

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

        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            if (event.target == document.getElementById('productModal')) {
                closeModal();
            }
        }
        function addToCart(productId, productName, price) {
    // Ngăn chặn sự kiện click lan ra ngoài
    event.stopPropagation();
    
    // Gửi request AJAX để thêm vào giỏ hàng
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

// Countdown timer for flash sale
document.addEventListener('DOMContentLoaded', function() {
    const countdownEl = document.querySelector('.countdown');
    if (countdownEl) {
        const endDate = new Date(countdownEl.dataset.end).getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endDate - now;
            
            if (distance < 0) {
                countdownEl.textContent = 'Đã kết thúc';
                clearInterval(countdownInterval);
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            let countdown = '';
            if (days > 0) countdown += days + ' ngày ';
            countdown += hours.toString().padStart(2, '0') + ':' + 
                        minutes.toString().padStart(2, '0') + ':' + 
                        seconds.toString().padStart(2, '0');
            
            countdownEl.textContent = countdown;
        }
        
        updateCountdown();
        const countdownInterval = setInterval(updateCountdown, 1000);
    }
});

// Copy coupon code
function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Đã sao chép mã: ' + code);
    }).catch(err => {
        console.error('Lỗi sao chép:', err);
        // Fallback method
        const textArea = document.createElement('textarea');
        textArea.value = code;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Đã sao chép mã: ' + code);
    });
}
    </script>
      <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
