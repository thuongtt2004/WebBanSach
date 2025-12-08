<?php
require_once 'config/connect.php';

// Lấy danh sách sách từ database
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
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
    <?php require_once 'header.php'; ?>

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
                    <option value="all">Tất cả danh mục</option>
                    <?php
                    $categories_query = "SELECT * FROM categories ORDER BY category_name";
                    $categories_result = $conn->query($categories_query);
                    while($cat = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
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
                    <span class="product-count" id="productCount">Hiển thị <?php echo $result->num_rows; ?> sản phẩm</span>
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
                            <button onclick="addToCart('<?php echo $row['product_id']; ?>', 
                                                     '<?php echo addslashes($row['product_name']); ?>', 
                                                     <?php echo $row['price']; ?>)" 
                                    class="add-to-cart">
                                Thêm vào giỏ hàng
                            </button>
                            <button onclick="window.location.href='buy_now_page.php?id=<?php echo $row['product_id']; ?>'" 
                                    class="buy-now">
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
            let visibleCount = 0;

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
                    product.style.display = 'block';
                    visibleCount++;
                } else {
                    product.style.display = 'none';
                }
            });
            
            updateProductCount(visibleCount);
        }

        function updateProductCount(count) {
            const countElement = document.getElementById('productCount');
            if (countElement) {
                countElement.textContent = `Hiển thị ${count} sản phẩm`;
            }
        }

        function clearAllFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('categoryFilter').value = 'all';
            document.getElementById('priceFilter').value = 'all';
            document.getElementById('languageFilter').value = 'all';
            document.getElementById('formatFilter').value = 'all';
            document.getElementById('sortFilter').value = 'default';
            
            const products = document.querySelectorAll('.product');
            products.forEach(product => product.style.display = 'block');
            updateProductCount(products.length);
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
            
            document.getElementById('productModal').style.display = 'block';
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
      <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?> 