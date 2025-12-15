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

// Lấy danh sách đánh giá
$sql = "SELECT r.*, u.username, p.product_name 
        FROM reviews r 
        JOIN users u ON r.user_id = u.user_id 
        JOIN products p ON r.product_id = p.product_id 
        ORDER BY r.review_date DESC";
$result = $conn->query($sql);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .review-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .review-table th, .review-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .stars {
            color: #ffd700;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <main>
    <div class="container" style="padding: 20px;">
        <h1><i class="fas fa-star"></i> Quản Lý Đánh Giá</h1>
        
        <table class="review-table">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Sản phẩm</th>
                    <th>Đánh giá</th>
                    <th>Nội dung</th>
                    <th>Ngày đánh giá</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($review = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($review['username']); ?></td>
                        <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                        <td>
                            <div class="stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <span class="<?php echo $i <= $review['rating'] ? 'filled' : ''; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($review['content']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($review['review_date'])); ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?');">
                                <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                <button type="submit" name="delete_review" class="delete-btn">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    </main>

    <?php include 'admin_footer.php'; ?>
</body>
</html>