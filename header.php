<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tự động chạy script xác nhận hài lòng và hủy đơn quá hạn (chạy mỗi 1h một lần)
$last_auto_run_file = __DIR__ . '/logs/last_auto_run.txt';
$should_run = true;

if (file_exists($last_auto_run_file)) {
    $last_run = file_get_contents($last_auto_run_file);
    if (time() - (int)$last_run < 3600) { // 1 giờ = 3600 giây
        $should_run = false;
    }
}

if ($should_run) {
    require_once __DIR__ . '/config/connect.php';
    
    // Cập nhật completed_date cho đơn cũ
    $conn->query("UPDATE orders SET completed_date = created_at WHERE order_status = 'Hoàn thành' AND completed_date IS NULL");
    
    // Tự động xác nhận đơn hàng sau 7 ngày
    $conn->query("UPDATE orders SET customer_confirmed = 1 WHERE order_status = 'Hoàn thành' AND customer_confirmed = 0 AND ((completed_date IS NOT NULL AND DATEDIFF(NOW(), completed_date) >= 7) OR (completed_date IS NULL AND DATEDIFF(NOW(), created_at) >= 7))");
    
    // Tự động hủy đơn chờ thanh toán quá 24h
    $conn->query("UPDATE orders SET order_status = 'Đã hủy', notes = CONCAT(IFNULL(notes, ''), '\nTự động hủy: Quá thời gian thanh toán (24h)') WHERE order_status = 'Chờ thanh toán' AND payment_method = 'bank_transfer' AND TIMESTAMPDIFF(HOUR, created_at, NOW()) >= 24");
    
    // Lưu thời gian chạy
    if (!is_dir(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0777, true);
    }
    file_put_contents($last_auto_run_file, time());
}
?>
<header class="user-header">
    <div class="header-top">
        <div class="logo-section">
            <img src="./uploads/logo.jpeg" alt="Logo" class="site-logo">
            <h1>TTHUONG Bookstore</h1>
        </div>
        
        <div class="header-actions">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="chat.php" class="chat-icon-btn" title="Chat với admin">
                    <i class="fas fa-comments" style="font-size: 1.3rem;"></i>
                    <?php
                    // Đếm tin nhắn chưa đọc (kiểm tra bảng tồn tại)
                    require_once 'config/connect.php';
                    $user_id = $_SESSION['user_id'];
                    $table_check = $conn->query("SHOW TABLES LIKE 'messages'");
                    if ($table_check && $table_check->num_rows > 0) {
                        $unread_query = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND sender_type = 'admin' AND is_read = 0";
                        $unread_stmt = $conn->prepare($unread_query);
                        $unread_stmt->bind_param("i", $user_id);
                        $unread_stmt->execute();
                        $unread_result = $unread_stmt->get_result();
                        if ($unread_result) {
                            $unread = $unread_result->fetch_assoc()['count'];
                            if ($unread > 0): ?>
                                <span class="chat-badge"><?php echo $unread; ?></span>
                            <?php endif;
                        }
                    }
                    ?>
                </a>
                
                <a href="profile.php" class="profile-icon-btn" title="Trang cá nhân">
                    <i class="fas fa-user-circle" style="font-size: 1.3rem;"></i>
                </a>
                
                <div class="user-welcome">
                    <span>Xin chào, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                </div>
                
                <a href="logout_page.php" class="btn-logout-user">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="login_page.php" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập
                    </a>
                    <a href="register_page.php" class="btn-register">
                        <i class="fas fa-user-plus"></i> Đăng ký
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <nav class="main-nav">
        <ul>
            <li><a href="home.php"><i class="fas fa-home"></i> <span>Trang chủ</span></a></li>
            <li><a href="products.php"><i class="fas fa-book"></i> <span>Sách</span></a></li>
            <li><a href="authors.php"><i class="fas fa-user-edit"></i> <span>Tác giả</span></a></li>
            <li><a href="publishers.php"><i class="fas fa-building"></i> <span>NXB</span></a></li>
            <li><a href="blog.php"><i class="fas fa-newspaper"></i> <span>Blog</span></a></li>
            <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> <span>Giỏ hàng</span></a></li>
            <li><a href="track_order.php"><i class="fas fa-truck"></i> <span>Theo dõi</span></a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> <span>Đánh giá</span></a></li>
            <li><a href="contact.php"><i class="fas fa-envelope"></i> <span>Liên hệ</span></a></li>
        </ul>
    </nav>
</header>

<!-- Hiển thị thông báo -->
<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success" style="position:fixed;top:80px;right:20px;z-index:10000;padding:15px 20px;background:#28a745;color:white;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.2);animation:slideIn 0.3s ease;">
    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
</div>
<script>setTimeout(() => document.querySelector('.alert-success')?.remove(), 4000);</script>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-error" style="position:fixed;top:80px;right:20px;z-index:10000;padding:15px 20px;background:#dc3545;color:white;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.2);animation:slideIn 0.3s ease;">
    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
</div>
<script>setTimeout(() => document.querySelector('.alert-error')?.remove(), 4000);</script>
<?php endif; ?>

<link rel="stylesheet" href="css/header.css">
<link rel="stylesheet" href="css/mobile-optimization.css">
<link rel="stylesheet" href="css/mobile-375px.css">

<!-- Thêm Font Awesome cho icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">