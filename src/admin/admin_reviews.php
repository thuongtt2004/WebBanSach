<?php
session_start();
require_once '../config/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Xử lý xóa đánh giá
if (isset($_POST['delete_review'])) {
    $review_id = $_POST['review_id'];
    $delete_sql = "DELETE FROM reviews WHERE review_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $review_id);
    $delete_stmt->execute();
}

// Xử lý phản hồi đánh giá
if (isset($_POST['reply_review'])) {
    $review_id = $_POST['review_id'];
    $admin_reply = trim($_POST['admin_reply']);
    
    if (!empty($admin_reply)) {
        $reply_sql = "UPDATE reviews SET admin_reply = ?, admin_reply_date = NOW() WHERE review_id = ?";
        $reply_stmt = $conn->prepare($reply_sql);
        $reply_stmt->bind_param("si", $admin_reply, $review_id);
        if ($reply_stmt->execute()) {
            echo "<script>alert('Phản hồi thành công!');</script>";
        } else {
            echo "<script>alert('Lỗi khi phản hồi!');</script>";
        }
    }
}

// Lấy danh sách sản phẩm có đánh giá (group by product)
$sql = "SELECT 
            p.product_id,
            p.product_name,
            p.image_url,
            COUNT(r.review_id) as total_reviews,
            AVG(r.rating) as avg_rating,
            SUM(CASE WHEN r.images IS NOT NULL AND r.images != '' THEN 1 ELSE 0 END) as reviews_with_images
        FROM products p
        INNER JOIN reviews r ON p.product_id = r.product_id
        GROUP BY p.product_id, p.product_name, p.image_url
        ORDER BY total_reviews DESC, avg_rating DESC";
$products_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đánh Giá - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/admin-mobile.css">
    <link rel="stylesheet" href="../css/admin_reviews.css">
    <link rel="stylesheet" href="../css/mobile-375px.css">
    <link rel="stylesheet" href="../css/fontawesome/all.min.css">
    <style>
        .products-list {
            display: grid;
            gap: 20px;
            margin-top: 20px;
        }
        
        .product-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .product-header {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .product-image {
            width: 80px;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .product-info {
            flex: 1;
        }
        
        .product-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .product-stats {
            display: flex;
            gap: 20px;
            color: #666;
            font-size: 14px;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .stars {
            color: #ffd700;
        }
        
        .expand-icon {
            font-size: 24px;
            color: #007bff;
            transition: transform 0.3s;
        }
        
        .product-card.expanded .expand-icon {
            transform: rotate(180deg);
        }
        
        .reviews-details {
            display: none;
            margin-top: 20px;
            border-top: 2px solid #f0f0f0;
            padding-top: 20px;
        }
        
        .product-card.expanded .reviews-details {
            display: block;
        }
        
        .review-item {
            background: #f9f9f9;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .review-user {
            font-weight: 600;
            color: #333;
        }
        
        .review-date {
            color: #999;
            font-size: 13px;
        }
        
        .review-rating {
            margin-bottom: 8px;
        }
        
        .review-content {
            color: #555;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        
        .review-images {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        
        .review-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .review-image:hover {
            transform: scale(1.05);
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }
        
        .delete-btn:hover {
            background: #c82333;
        }
        
        .no-reviews {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        /* Modal để xem ảnh lớn */
        .image-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
        }
        
        .image-modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .close-modal {
            position: absolute;
            top: 20px;
            right: 40px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <main>
    <div class="container" style="padding: 20px;">
        <h1><i class="fas fa-star"></i> Quản Lý Đánh Giá</h1>
        
        <div style="margin: 20px 0;">
            <div style="display: flex; align-items: center; gap: 10px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <i class="fas fa-search" style="color: #666;"></i>
                <input type="text" 
                       id="searchInput" 
                       placeholder="Tìm kiếm theo tên sản phẩm, người dùng, nội dung đánh giá..." 
                       style="flex: 1; border: none; outline: none; font-size: 15px;"
                       oninput="filterReviews()">
                <span id="searchCount" style="color: #666; font-size: 14px;"></span>
            </div>
        </div>
        
        <div class="products-list">
            <?php if ($products_result && $products_result->num_rows > 0): ?>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <div class="product-card" onclick="toggleReviews('<?php echo htmlspecialchars($product['product_id']); ?>')">
                        <div class="product-header">
                            <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                 class="product-image">
                            
                            <div class="product-info">
                                <div class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                <div class="product-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-comments"></i>
                                        <span><?php echo $product['total_reviews']; ?> đánh giá</span>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stars">
                                            <?php 
                                            $avg_rating = round($product['avg_rating'], 1);
                                            for($i = 1; $i <= 5; $i++): 
                                                echo $i <= $avg_rating ? '★' : '☆';
                                            endfor; 
                                            ?>
                                        </div>
                                        <span><?php echo $avg_rating; ?>/5</span>
                                    </div>
                                    <?php if ($product['reviews_with_images'] > 0): ?>
                                    <div class="stat-item">
                                        <i class="fas fa-camera"></i>
                                        <span><?php echo $product['reviews_with_images']; ?> có ảnh</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="expand-icon">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                        
                        <div class="reviews-details" id="reviews-<?php echo $product['product_id']; ?>">
                            <!-- Reviews sẽ được load ở đây -->
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-reviews">
                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 10px;"></i>
                    <p>Chưa có đánh giá nào</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Modal xem ảnh -->
    <div id="imageModal" class="image-modal" onclick="closeImageModal()">
        <span class="close-modal">&times;</span>
        <img class="image-modal-content" id="modalImage">
    </div>
    </main>

    <script>
        let loadedProducts = new Set();
        let allProducts = [];
        
        // Lưu thông tin sản phẩm để tìm kiếm
        document.addEventListener('DOMContentLoaded', function() {
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                const productName = card.querySelector('.product-name').textContent.toLowerCase();
                allProducts.push({
                    element: card,
                    name: productName
                });
            });
            updateSearchCount();
        });
        
        function filterReviews() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            let visibleCount = 0;
            
            allProducts.forEach(product => {
                const isMatch = product.name.includes(searchTerm);
                product.element.style.display = isMatch ? 'block' : 'none';
                if (isMatch) visibleCount++;
            });
            
            updateSearchCount(visibleCount);
        }
        
        function updateSearchCount(count) {
            const total = allProducts.length;
            const searchCount = document.getElementById('searchCount');
            if (count !== undefined && count < total) {
                searchCount.textContent = `Hiển thị ${count}/${total} sản phẩm`;
            } else {
                searchCount.textContent = `${total} sản phẩm`;
            }
        }
        
        function toggleReviews(productId) {
            const card = event.currentTarget;
            const reviewsDiv = document.getElementById('reviews-' + productId);
            
            // Toggle expanded class
            card.classList.toggle('expanded');
            
            // Load reviews nếu chưa load
            if (!loadedProducts.has(productId) && card.classList.contains('expanded')) {
                loadReviews(productId);
                loadedProducts.add(productId);
            }
            
            // Prevent event bubbling
            event.stopPropagation();
        }
        
        function loadReviews(productId) {
            const reviewsDiv = document.getElementById('reviews-' + productId);
            reviewsDiv.innerHTML = '<p style="text-align:center;"><i class="fas fa-spinner fa-spin"></i> Đang tải...</p>';
            
            fetch('get_product_reviews_admin.php?product_id=' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayReviews(reviewsDiv, data.reviews);
                    } else {
                        reviewsDiv.innerHTML = '<p style="text-align:center; color:#dc3545;">Lỗi khi tải đánh giá</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    reviewsDiv.innerHTML = '<p style="text-align:center; color:#dc3545;">Lỗi khi tải đánh giá</p>';
                });
        }
        
        function displayReviews(container, reviews) {
            if (reviews.length === 0) {
                container.innerHTML = '<p style="text-align:center; color:#999;">Không có đánh giá nào</p>';
                return;
            }
            
            let html = '';
            reviews.forEach(review => {
                html += `
                    <div class="review-item">
                        <div class="review-header">
                            <div>
                                <span class="review-user">${escapeHtml(review.username)}</span>
                                <span class="review-date"> - ${review.review_date}</span>
                            </div>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?');">
                                <input type="hidden" name="review_id" value="${review.review_id}">
                                <button type="submit" name="delete_review" class="delete-btn" onclick="event.stopPropagation();">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                        <div class="review-rating">
                            <div class="stars">
                                ${'★'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}
                            </div>
                        </div>
                        <div class="review-content">${escapeHtml(review.content)}</div>
                `;
                
                if (review.images && review.images.length > 0) {
                    html += '<div class="review-images">';
                    review.images.forEach(image => {
                        html += `<img src="../${image}" class="review-image" onclick="openImageModal(event, '../${image}')">`;
                    });
                    html += '</div>';
                }
                
                // Hiển thị phản hồi của admin nếu có
                if (review.admin_reply) {
                    html += `
                        <div style="background:#f0f8ff;border-left:3px solid #007bff;padding:10px;margin-top:10px;border-radius:4px;">
                            <div style="display:flex;align-items:center;gap:5px;margin-bottom:5px;">
                                <i class="fas fa-user-shield" style="color:#007bff;"></i>
                                <strong style="color:#007bff;">Phản hồi từ Admin</strong>
                                <span style="color:#999;font-size:12px;margin-left:auto;">${review.admin_reply_date}</span>
                            </div>
                            <div style="color:#555;">${escapeHtml(review.admin_reply)}</div>
                        </div>
                    `;
                }
                
                // Form phản hồi
                html += `
                    <form method="POST" style="margin-top:10px;" onsubmit="event.stopPropagation();">
                        <input type="hidden" name="review_id" value="${review.review_id}">
                        <div style="display:flex;gap:10px;align-items:flex-start;">
                            <textarea name="admin_reply" placeholder="${review.admin_reply ? 'Cập nhật phản hồi...' : 'Viết phản hồi...'}" 
                                      style="flex:1;padding:8px;border:1px solid #ddd;border-radius:4px;resize:vertical;min-height:60px;"
                                      onclick="event.stopPropagation();">${review.admin_reply ? escapeHtml(review.admin_reply) : ''}</textarea>
                            <button type="submit" name="reply_review" 
                                    style="padding:8px 15px;background:#28a745;color:white;border:none;border-radius:4px;cursor:pointer;white-space:nowrap;"
                                    onclick="event.stopPropagation();">
                                <i class="fas fa-reply"></i> ${review.admin_reply ? 'Cập nhật' : 'Gửi'}
                            </button>
                        </div>
                    </form>
                `;
                
                html += '</div>';
            });
            
            container.innerHTML = html;
        }
        
        function openImageModal(event, imageSrc) {
            event.stopPropagation();
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = 'block';
            modalImg.src = imageSrc;
        }
        
        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }
        
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    </script>

    <?php include 'admin_footer.php'; ?>
</body>
</html>
