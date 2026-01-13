<?php
require_once 'config/connect.php';
require_once 'header.php';

// Lấy danh sách tác giả có sách (distinct)
$sql = "SELECT DISTINCT author, COUNT(*) as book_count 
        FROM products 
        WHERE author IS NOT NULL AND author != '' 
        GROUP BY author 
        ORDER BY author ASC";
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
    <title>Tác giả - TTHUONG Bookstore</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/authors.css">
    <link rel="stylesheet" href="css/fontawesome/all.min.css">
</head>
<body>

    <section class="authors-section">
        <div class="authors-header">
            <h1><i class="fas fa-user-edit"></i> Danh sách tác giả</h1>
            <p>Khám phá các tác giả và tác phẩm của họ</p>
        </div>

        <!-- Search box -->
        <div class="author-search">
            <input type="text" id="authorSearch" placeholder="Tìm kiếm tác giả...">
            <i class="fas fa-search"></i>
        </div>

        <div class="authors-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $author_encoded = urlencode($row['author']);
                    ?>
                    <div class="author-card" data-author="<?php echo strtolower($row['author']); ?>">
                        <div class="author-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="author-info">
                            <h3><?php echo htmlspecialchars($row['author']); ?></h3>
                            <p class="book-count">
                                <i class="fas fa-book"></i> 
                                <?php echo $row['book_count']; ?> cuốn sách
                            </p>
                            <a href="author_detail.php?author=<?php echo $author_encoded; ?>" class="view-books-btn">
                                <i class="fas fa-arrow-right"></i> Xem sách
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='no-result'>Chưa có tác giả nào.</p>";
            }
            ?>
        </div>
    </section>

    <script>
        // Tìm kiếm tác giả
        document.getElementById('authorSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const authorCards = document.querySelectorAll('.author-card');
            
            authorCards.forEach(card => {
                const authorName = card.getAttribute('data-author');
                if (authorName.includes(searchTerm)) {
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
