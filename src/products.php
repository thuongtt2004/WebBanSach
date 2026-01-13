<?php
require_once 'config/connect.php';
require_once 'header.php';

/** @var mysqli $conn */

// Function để tính giá sau khuyến mãi
function getDiscountedPrice($conn, $product_id, $original_price, $category_id) {
    $now = date('Y-m-d H:i:s');
    $discount_info = ['price' => $original_price, 'has_discount' => false, 'discount_percent' => 0, 'promotion_name' => ''];
    
    // Kiểm tra flash sale
    $flash_query = "SELECT * FROM promotions WHERE promotion_type='flash_sale' AND status='active' AND '$now' BETWEEN start_date AND end_date ORDER BY discount_value DESC LIMIT 1";
    $flash_result = $conn->query($flash_query);
    if ($flash_result && $flash_result->num_rows > 0) {
        $promo = $flash_result->fetch_assoc();
        $discount = ($promo['discount_type'] == 'percentage') ? ($original_price * $promo['discount_value'] / 100) : $promo['discount_value'];
        if ($promo['max_discount'] && $discount > $promo['max_discount']) $discount = $promo['max_discount'];
        $discount_info = [
            'price' => $original_price - $discount,
            'has_discount' => true,
            'discount_percent' => round(($discount / $original_price) * 100),
            'promotion_name' => $promo['promotion_name'],
            'original_price' => $original_price
        ];
        return $discount_info;
    }
    
    // Kiểm tra khuyến mãi sản phẩm
    $product_promo_query = "SELECT p.* FROM promotions p INNER JOIN promotion_products pp ON p.promotion_id=pp.promotion_id WHERE pp.product_id COLLATE utf8mb4_unicode_ci='$product_id' COLLATE utf8mb4_unicode_ci AND p.status='active' AND '$now' BETWEEN p.start_date AND p.end_date ORDER BY p.discount_value DESC LIMIT 1";
    $product_promo_result = $conn->query($product_promo_query);
    if ($product_promo_result && $product_promo_result->num_rows > 0) {
        $promo = $product_promo_result->fetch_assoc();
        $discount = ($promo['discount_type'] == 'percentage') ? ($original_price * $promo['discount_value'] / 100) : $promo['discount_value'];
        if ($promo['max_discount'] && $discount > $promo['max_discount']) $discount = $promo['max_discount'];
        $discount_info = [
            'price' => $original_price - $discount,
            'has_discount' => true,
            'discount_percent' => round(($discount / $original_price) * 100),
            'promotion_name' => $promo['promotion_name'],
            'original_price' => $original_price
        ];
        return $discount_info;
    }
    
    // Kiểm tra khuyến mãi danh mục
    if ($category_id) {
        $category_promo_query = "SELECT p.* FROM promotions p INNER JOIN promotion_categories pc ON p.promotion_id=pc.promotion_id WHERE pc.category_id=$category_id AND p.status='active' AND '$now' BETWEEN p.start_date AND p.end_date ORDER BY p.discount_value DESC LIMIT 1";
        $category_promo_result = $conn->query($category_promo_query);
        if ($category_promo_result && $category_promo_result->num_rows > 0) {
            $promo = $category_promo_result->fetch_assoc();
            $discount = ($promo['discount_type'] == 'percentage') ? ($original_price * $promo['discount_value'] / 100) : $promo['discount_value'];
            if ($promo['max_discount'] && $discount > $promo['max_discount']) $discount = $promo['max_discount'];
            $discount_info = [
                'price' => $original_price - $discount,
                'has_discount' => true,
                'discount_percent' => round(($discount / $original_price) * 100),
                'promotion_name' => $promo['promotion_name'],
                'original_price' => $original_price
            ];
            return $discount_info;
        }
    }
    
    return $discount_info;
}

// Lấy category từ URL nếu có
$selected_category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // Hiển thị 12 sản phẩm mỗi trang
$offset = ($page - 1) * $limit;

// Đếm tổng số sản phẩm
$count_sql = "SELECT COUNT(DISTINCT p.product_id) as total FROM products p";
$count_result = $conn->query($count_sql);
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

// Lấy TOÀN BỘ sản phẩm từ database (không phân trang) để bộ lọc hoạt động trên tất cả
$sql = "SELECT p.*, c.category_name,
        COALESCE(AVG(r.rating), 0) as average_rating,
        COUNT(r.review_id) as review_count
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        LEFT JOIN reviews r ON p.product_id = r.product_id
        GROUP BY p.product_id
        ORDER BY p.product_id DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Lỗi truy vấn: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sách - TTHUONG Bookstore</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/sanpham.css">
</head>
<body>

    <!-- Breadcrumb -->
    <div class="breadcrumb-container">
        <div class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
            <span class="separator">/</span>
            <span class="current">Sách</span>
        </div>
    </div>

    <!-- Main Container -->
    <div class="products-page-container">
        <!-- Sidebar Filters -->
        <aside class="filters-sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-filter"></i> Bộ lọc</h3>
                <button class="clear-filters" onclick="clearAllFilters()">Xóa bộ lọc</button>
            </div>

            <!-- Search Box -->
            <div class="filter-section">
                <h4>Tìm kiếm</h4>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Tìm sách, tác giả...">
                    <button onclick="searchProducts()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="filter-section">
                <h4><i class="fas fa-list"></i> Danh mục</h4>
                <select id="categoryFilter">
                    <option value="all" <?php echo $selected_category === 'all' ? 'selected' : ''; ?>>Tất cả danh mục</option>
                    <?php
                    $categories_query = "SELECT * FROM categories ORDER BY category_name";
                    $categories_result = $conn->query($categories_query);
                    while($cat = $categories_result->fetch_assoc()): 
                        $is_selected = ($selected_category == $cat['category_id']) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $cat['category_id']; ?>" <?php echo $is_selected; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Price Filter -->
            <div class="filter-section">
                <h4><i class="fas fa-tag"></i> Khoảng giá</h4>
                <select id="priceFilter">
                    <option value="all">Tất cả</option>
                    <option value="low">Dưới 100,000 VNĐ</option>
                    <option value="medium">100,000 - 300,000 VNĐ</option>
                    <option value="high">Trên 300,000 VNĐ</option>
                </select>
            </div>

            <!-- Language Filter -->
            <div class="filter-section">
                <h4><i class="fas fa-language"></i> Ngôn ngữ</h4>
                <select id="languageFilter">
                    <option value="all">Tất cả</option>
                    <option value="Tiếng Việt">Tiếng Việt</option>
                    <option value="Tiếng Anh">Tiếng Anh</option>
                    <option value="Tiếng Trung">Tiếng Trung</option>
                    <option value="Tiếng Nhật">Tiếng Nhật</option>
                    <option value="Tiếng Hàn">Tiếng Hàn</option>
                </select>
            </div>

            <!-- Format Filter -->
            <div class="filter-section">
                <h4><i class="fas fa-book"></i> Hình thức</h4>
                <select id="formatFilter">
                    <option value="all">Tất cả</option>
                    <option value="Bìa mềm">Bìa mềm</option>
                    <option value="Bìa cứng">Bìa cứng</option>
                    <option value="Ebook">Ebook</option>
                </select>
            </div>
        </aside>

        <!-- Main Products Area -->
        <main class="products-main">
            <!-- Products Header -->
            <div class="products-header">
                <div class="header-left">
                    <h2><i class="fas fa-book"></i> Tất cả sách</h2>
                    <span class="product-count" id="productCount">Hiển thị <?php echo $result->num_rows; ?> / <?php echo $total_products; ?> sản phẩm</span>
                </div>
                <div class="header-right">
                    <label for="sortFilter">Sắp xếp:</label>
                    <select id="sortFilter" onchange="sortProducts()">
                        <option value="default">Mặc định</option>
                        <option value="name-asc">Tên A-Z</option>
                        <option value="name-desc">Tên Z-A</option>
                        <option value="price-asc">Giá thấp đến cao</option>
                        <option value="price-desc">Giá cao đến thấp</option>
                        <option value="newest">Mới nhất</option>
                    </select>
                </div>
            </div>

            <section id="all-products">
        <div class="products">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <div class="product" data-category="<?php echo $row['category_id']; ?>" 
                         data-price="<?php echo $row['price']; ?>" 
                         data-name="<?php echo htmlspecialchars($row['product_name']); ?>"
                         data-author="<?php echo htmlspecialchars($row['author'] ?? ''); ?>"
                         data-language="<?php echo htmlspecialchars($row['language'] ?? ''); ?>"
                         data-format="<?php echo htmlspecialchars($row['book_format'] ?? ''); ?>" 
                         onclick="showProductDetails(
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
                        <button class="wishlist-btn" onclick="event.stopPropagation(); toggleWishlist('<?php echo $row['product_id']; ?>', this);" title="Thêm vào yêu thích">
                            <i class="far fa-heart"></i>
                        </button>
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                        <?php if (!empty($row['author'])): ?>
                        <p class="book-author"><i class="fas fa-user-edit"></i> <?php echo htmlspecialchars($row['author']); ?></p>
                        <?php endif; ?>
                        <?php 
                        $price_info = getDiscountedPrice($conn, $row['product_id'], $row['price'], $row['category_id']);
                        ?>
                        <?php if ($price_info['has_discount']): ?>
                            <div class="price-container" style="display: flex; align-items: center; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                <span class="original-price" style="text-decoration: line-through; color: #999; font-size: 14px;"><?php echo number_format($price_info['original_price'], 0, ',', '.'); ?> VNĐ</span>
                                <span class="book-price" style="color: #dc3545; font-weight: bold; font-size: 18px;"><?php echo number_format($price_info['price'], 0, ',', '.'); ?> VNĐ</span>
                                <span class="discount-badge" style="background: #ff6b6b; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">-<?php echo $price_info['discount_percent']; ?>%</span>
                            </div>
                        <?php else: ?>
                            <p class="book-price"><?php echo number_format($row['price'], 0, ',', '.'); ?> VNĐ</p>
                        <?php endif; ?>
                        
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
                            <button class="buy-now" data-product-id="<?php echo $row['product_id']; ?>">
                                Mua ngay
                            </button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='no-products'>Không có sản phẩm nào.</p>";
            }
            ?>
        </div>
        
        <!-- Pagination (JS-based) -->
        <div class="pagination" id="pagination"></div>
        
        <div class="pagination-info" id="paginationInfo"></div>
    </section>
        </main>
    </div>

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
        function searchProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const products = document.querySelectorAll('.product');
            
            products.forEach(product => {
                const productName = product.getAttribute('data-name').toLowerCase();
                const author = product.getAttribute('data-author') ? product.getAttribute('data-author').toLowerCase() : '';
                
                if (productName.includes(searchTerm) || author.includes(searchTerm)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        // Thêm sự kiện cho input search khi nhấn Enter
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchProducts();
            }
        });

        // Filter products
        // Phân trang client-side
        let currentPage = 1;
        const itemsPerPage = 12;

        document.getElementById('categoryFilter').addEventListener('change', filterProducts);
        document.getElementById('priceFilter').addEventListener('change', filterProducts);
        document.getElementById('languageFilter').addEventListener('change', filterProducts);
        document.getElementById('formatFilter').addEventListener('change', filterProducts);

        function filterProducts() {
            const category = document.getElementById('categoryFilter').value;
            const priceRange = document.getElementById('priceFilter').value;
            const language = document.getElementById('languageFilter').value;
            const format = document.getElementById('formatFilter').value;
            const products = document.querySelectorAll('.product');
            let visibleProducts = [];

            products.forEach(product => {
                const productCategory = product.getAttribute('data-category');
                const productPrice = parseInt(product.getAttribute('data-price'));
                const productLanguage = product.getAttribute('data-language');
                const productFormat = product.getAttribute('data-format');
                
                let showByCategory = category === 'all' || productCategory === category;
                let showByPrice = true;
                let showByLanguage = language === 'all' || productLanguage === language;
                let showByFormat = format === 'all' || productFormat === format;

                switch(priceRange) {
                    case 'low':
                        showByPrice = productPrice < 100000;
                        break;
                    case 'medium':
                        showByPrice = productPrice >= 100000 && productPrice <= 300000;
                        break;
                    case 'high':
                        showByPrice = productPrice > 300000;
                        break;
                }

                if (showByCategory && showByPrice && showByLanguage && showByFormat) {
                    visibleProducts.push(product);
                }
            });
            
            currentPage = 1; // Reset về trang 1
            displayPage(visibleProducts);
        }

        function displayPage(visibleProducts) {
            const products = document.querySelectorAll('.product');
            
            // Ẩn tất cả sản phẩm
            products.forEach(product => product.style.display = 'none');
            
            // Tính toán sản phẩm hiển thị
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const pageProducts = visibleProducts.slice(start, end);
            
            // Hiển thị sản phẩm của trang hiện tại
            pageProducts.forEach(product => product.style.display = 'block');
            
            // Cập nhật thông tin
            updateProductCount(visibleProducts.length, pageProducts.length);
            updatePagination(visibleProducts.length);
        }

        function updateProductCount(totalVisible, currentVisible) {
            const countElement = document.getElementById('productCount');
            if (countElement) {
                const total = <?php echo $total_products; ?>;
                countElement.textContent = `Hiển thị ${currentVisible} / ${totalVisible} sản phẩm`;
            }
        }

        function updatePagination(totalVisible) {
            const totalPages = Math.ceil(totalVisible / itemsPerPage);
            const paginationDiv = document.getElementById('pagination');
            const paginationInfo = document.getElementById('paginationInfo');
            
            if (totalPages <= 1) {
                paginationDiv.innerHTML = '';
                paginationInfo.innerHTML = '';
                return;
            }
            
            let html = '';
            
            // Nút Trước
            if (currentPage > 1) {
                html += `<a href="javascript:void(0)" onclick="changePage(${currentPage - 1})" class="pagination-btn"><i class="fas fa-chevron-left"></i> Trước</a>`;
            }
            
            // Các số trang
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);
            
            if (startPage > 1) {
                html += `<a href="javascript:void(0)" onclick="changePage(1)" class="pagination-number">1</a>`;
                if (startPage > 2) {
                    html += `<span class="pagination-dots">...</span>`;
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                html += `<a href="javascript:void(0)" onclick="changePage(${i})" class="pagination-number ${i === currentPage ? 'active' : ''}">${i}</a>`;
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `<span class="pagination-dots">...</span>`;
                }
                html += `<a href="javascript:void(0)" onclick="changePage(${totalPages})" class="pagination-number">${totalPages}</a>`;
            }
            
            // Nút Tiếp
            if (currentPage < totalPages) {
                html += `<a href="javascript:void(0)" onclick="changePage(${currentPage + 1})" class="pagination-btn">Tiếp <i class="fas fa-chevron-right"></i></a>`;
            }
            
            paginationDiv.innerHTML = html;
            paginationInfo.innerHTML = `Trang ${currentPage} / ${totalPages} (${totalVisible} sản phẩm)`;
        }

        function changePage(page) {
            currentPage = page;
            const category = document.getElementById('categoryFilter').value;
            const priceRange = document.getElementById('priceFilter').value;
            const language = document.getElementById('languageFilter').value;
            const format = document.getElementById('formatFilter').value;
            const products = document.querySelectorAll('.product');
            let visibleProducts = [];

            products.forEach(product => {
                const productCategory = product.getAttribute('data-category');
                const productPrice = parseInt(product.getAttribute('data-price'));
                const productLanguage = product.getAttribute('data-language');
                const productFormat = product.getAttribute('data-format');
                
                let showByCategory = category === 'all' || productCategory === category;
                let showByPrice = true;
                let showByLanguage = language === 'all' || productLanguage === language;
                let showByFormat = format === 'all' || productFormat === format;

                switch(priceRange) {
                    case 'low':
                        showByPrice = productPrice < 100000;
                        break;
                    case 'medium':
                        showByPrice = productPrice >= 100000 && productPrice <= 300000;
                        break;
                    case 'high':
                        showByPrice = productPrice > 300000;
                        break;
                }

                if (showByCategory && showByPrice && showByLanguage && showByFormat) {
                    visibleProducts.push(product);
                }
            });
            
            displayPage(visibleProducts);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Khởi tạo phân trang ban đầu
        window.addEventListener('load', function() {
            // Kiểm tra nếu có category được chọn từ URL, tự động lọc
            const categoryFilter = document.getElementById('categoryFilter');
            if (categoryFilter.value !== 'all') {
                filterProducts();
            } else {
                const products = Array.from(document.querySelectorAll('.product'));
                displayPage(products);
            }
        });

        function clearAllFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('categoryFilter').value = 'all';
            document.getElementById('priceFilter').value = 'all';
            document.getElementById('languageFilter').value = 'all';
            document.getElementById('formatFilter').value = 'all';
            document.getElementById('sortFilter').value = 'default';
            
            currentPage = 1;
            const products = Array.from(document.querySelectorAll('.product'));
            displayPage(products);
        }

        function sortProducts() {
            const sortValue = document.getElementById('sortFilter').value;
            const productsContainer = document.querySelector('.products');
            const products = Array.from(document.querySelectorAll('.product'));
            
            products.sort((a, b) => {
                switch(sortValue) {
                    case 'name-asc':
                        return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                    case 'name-desc':
                        return b.getAttribute('data-name').localeCompare(a.getAttribute('data-name'));
                    case 'price-asc':
                        return parseInt(a.getAttribute('data-price')) - parseInt(b.getAttribute('data-price'));
                    case 'price-desc':
                        return parseInt(b.getAttribute('data-price')) - parseInt(a.getAttribute('data-price'));
                    default:
                        return 0;
                }
            });
            
            products.forEach(product => productsContainer.appendChild(product));
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

        function showProductDetails(name, description, price, image, id, category, stock, author, publisher, year, isbn, pages, language, format) {
            document.getElementById('modalTitle').textContent = name;
            document.getElementById('modalDescription').textContent = description;
            document.getElementById('modalPrice').textContent = price;
            document.getElementById('modalImage').src = image;
            document.getElementById('modalCategory').textContent = category;
            document.getElementById('modalStock').textContent = stock;
            document.getElementById('modalIsbn').textContent = isbn || 'Chưa cập nhật';
            
            // Hiển thị tác giả
            if (author) {
                document.getElementById('modalAuthor').textContent = author;
                document.getElementById('modalAuthorContainer').style.display = 'block';
            } else {
                document.getElementById('modalAuthorContainer').style.display = 'none';
            }
            
            // Hiển thị nhà xuất bản
            if (publisher) {
                document.getElementById('modalPublisher').textContent = publisher;
                document.getElementById('modalPublisherContainer').style.display = 'block';
            } else {
                document.getElementById('modalPublisherContainer').style.display = 'none';
            }
            
            // Hiển thị năm xuất bản
            if (year) {
                document.getElementById('modalYear').textContent = year;
                document.getElementById('modalYearContainer').style.display = 'block';
            } else {
                document.getElementById('modalYearContainer').style.display = 'none';
            }
            
            // Hiển thị số trang
            if (pages && pages > 0) {
                document.getElementById('modalPages').textContent = pages + ' trang';
                document.getElementById('modalPagesContainer').style.display = 'block';
            } else {
                document.getElementById('modalPagesContainer').style.display = 'none';
            }
            
            // Hiển thị ngôn ngữ
            if (language) {
                document.getElementById('modalLanguage').textContent = language;
                document.getElementById('modalLanguageContainer').style.display = 'block';
            } else {
                document.getElementById('modalLanguageContainer').style.display = 'none';
            }
            
            // Hiển thị hình thức
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
                                html += '<p style="color:#666;margin:0 0 10px 0;">' + review.content + '</p>';
                                
                                // Hiển thị ảnh đánh giá nếu có
                                if (review.images && review.images.length > 0) {
                                    html += '<div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">';
                                    review.images.forEach(image => {
                                        html += '<img src="' + image + '" alt="Ảnh đánh giá" style="width:80px;height:80px;object-fit:cover;border-radius:6px;cursor:pointer;" onclick="window.open(\'' + image + '\', \'_blank\')">';
                                    });
                                    html += '</div>';
                                }
                                
                                // Hiển thị phản hồi của admin nếu có
                                if (review.admin_reply) {
                                    html += '<div style="background:#f0f8ff;border-left:3px solid #007bff;padding:10px;margin-top:10px;border-radius:4px;">';
                                    html += '<div style="display:flex;align-items:center;gap:5px;margin-bottom:5px;">';
                                    html += '<i class="fas fa-user-shield" style="color:#007bff;font-size:14px;"></i>';
                                    html += '<strong style="color:#007bff;font-size:14px;">Phản hồi từ Shop</strong>';
                                    html += '<span style="color:#999;font-size:12px;margin-left:auto;">' + review.admin_reply_date + '</span>';
                                    html += '</div>';
                                    html += '<p style="color:#555;margin:0;font-size:14px;">' + review.admin_reply + '</p>';
                                    html += '</div>';
                                }
                                
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
        
        // Toggle wishlist
        function toggleWishlist(productId, button) {
            <?php if (!isset($_SESSION['user_id'])): ?>
                if (confirm('Bạn cần đăng nhập để thêm vào yêu thích. Đến trang đăng nhập?')) {
                    window.location.href = 'login.php';
                }
                return;
            <?php endif; ?>
            
            fetch('toggle_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    action: 'toggle'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = button.querySelector('i');
                    if (data.in_wishlist) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        button.style.color = '#dc3545';
                        button.title = 'Xóa khỏi yêu thích';
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        button.style.color = '';
                        button.title = 'Thêm vào yêu thích';
                    }
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
        }
        
        // Load wishlist status on page load
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['user_id'])): ?>
                loadWishlistStatus();
            <?php endif; ?>
        });
        
        function loadWishlistStatus() {
            fetch('get_wishlist_status.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.wishlist.forEach(productId => {
                            const buttons = document.querySelectorAll(`button[onclick*="${productId}"]`);
                            buttons.forEach(button => {
                                if (button.classList.contains('wishlist-btn')) {
                                    const icon = button.querySelector('i');
                                    icon.classList.remove('far');
                                    icon.classList.add('fas');
                                    button.style.color = '#dc3545';
                                    button.title = 'Xóa khỏi yêu thích';
                                }
                            });
                        });
                    }
                });
        }
    </script>
    
    <script>
        // Xử lý nút Mua ngay
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('buy-now')) {
                e.stopPropagation();
                e.preventDefault();
                const productId = e.target.getAttribute('data-product-id');
                window.location.href = 'buy_now_page.php?id=' + productId;
            }
        });
    </script>
      <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
