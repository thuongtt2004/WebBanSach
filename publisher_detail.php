<?php
require_once 'config/connect.php';

// Lấy tên nhà xuất bản từ URL
$publisher = isset($_GET['publisher']) ? $_GET['publisher'] : '';

if (empty($publisher)) {
    header('Location: publishers.php');
    exit();
}

// Lấy danh sách sách của nhà xuất bản
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.publisher = ?
        ORDER BY p.publish_year DESC, p.product_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $publisher);
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
    <title><?php echo htmlspecialchars($publisher); ?> - TTHUONG Bookstore</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/sanpham.css">
    <link rel="stylesheet" href="css/publisher_detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php require_once 'header.php'; ?>

    <section class="publisher-detail-section">
        <div class="publisher-header">
            <div class="publisher-icon-large">
                <i class="fas fa-building"></i>
            </div>
            <div class="publisher-info">
                <h1><i class="fas fa-book-open"></i> <?php echo htmlspecialchars($publisher); ?></h1>
                <p class="publisher-stats">
                    <span><i class="fas fa-books"></i> <?php echo $book_count; ?> đầu sách</span>
                </p>
                <a href="publishers.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách NXB
                </a>
            </div>
        </div>

        <div class="books-section">
            <h2>📚 Sách xuất bản bởi <?php echo htmlspecialchars($publisher); ?></h2>
            <div class="products">
                <?php
                if ($book_count > 0) {
                    while($row = $result->fetch_assoc()) {
                        ?>
                        <div class="product" 
                             onclick="window.location.href='products.php'">
                            <button class="wishlist-btn" onclick="event.stopPropagation(); toggleWishlist('<?php echo $row['product_id']; ?>', this);" title="Thêm vào yêu thích">
                                <i class="far fa-heart"></i>
                            </button>
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                            <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                            <?php if (!empty($row['author'])): ?>
                            <p class="book-author"><i class="fas fa-user-edit"></i> <?php echo htmlspecialchars($row['author']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($row['publish_year'])): ?>
                            <p class="book-year"><i class="fas fa-calendar"></i> Năm XB: <?php echo $row['publish_year']; ?></p>
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
                                    Thêm vào giỏ
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
                    echo "<p>Không có sách nào của nhà xuất bản này.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <script>
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
