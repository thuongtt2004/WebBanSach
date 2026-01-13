<?php
require_once 'config/connect.php';
require_once 'header.php';

// Lấy danh sách nhà xuất bản có sách (distinct)
$sql = "SELECT DISTINCT publisher, COUNT(*) as book_count 
        FROM products 
        WHERE publisher IS NOT NULL AND publisher != '' 
        GROUP BY publisher 
        ORDER BY publisher ASC";
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
    <title>Nhà xuất bản - TTHUONG Bookstore</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/publishers.css">
    <link rel="stylesheet" href="css/fontawesome/all.min.css">
</head>
<body>

    <section class="publishers-section">
        <div class="publishers-header">
            <h1><i class="fas fa-building"></i> Nhà xuất bản</h1>
            <p>Khám phá sách từ các nhà xuất bản uy tín</p>
        </div>

        <!-- Search box -->
        <div class="publisher-search">
            <input type="text" id="publisherSearch" placeholder="Tìm kiếm nhà xuất bản...">
            <i class="fas fa-search"></i>
        </div>

        <div class="publishers-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $publisher_encoded = urlencode($row['publisher']);
                    ?>
                    <div class="publisher-card" data-publisher="<?php echo strtolower($row['publisher']); ?>">
                        <div class="publisher-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="publisher-info">
                            <h3><?php echo htmlspecialchars($row['publisher']); ?></h3>
                            <p class="book-count">
                                <i class="fas fa-books"></i> 
                                <?php echo $row['book_count']; ?> đầu sách
                            </p>
                            <a href="publisher_detail.php?publisher=<?php echo $publisher_encoded; ?>" class="view-books-btn">
                                <i class="fas fa-arrow-right"></i> Xem sách
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='no-result'>Chưa có nhà xuất bản nào.</p>";
            }
            ?>
        </div>
    </section>

    <script>
        // Tìm kiếm nhà xuất bản
        document.getElementById('publisherSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const publisherCards = document.querySelectorAll('.publisher-card');
            
            publisherCards.forEach(card => {
                const publisherName = card.getAttribute('data-publisher');
                if (publisherName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
