<?php
if (!isset($conn)) {
    require_once __DIR__ . '/../config/connect.php';
}
/** @var mysqli $conn */

// Tự động chạy script (mỗi 1h một lần)
$last_auto_run_file = __DIR__ . '/../logs/last_auto_run.txt';
$should_run = true;

if (file_exists($last_auto_run_file)) {
    $last_run = file_get_contents($last_auto_run_file);
    if (time() - (int)$last_run < 3600) {
        $should_run = false;
    }
}

if ($should_run) {
    // Cập nhật completed_date cho đơn cũ
    $conn->query("UPDATE orders SET completed_date = created_at WHERE order_status = 'Hoàn thành' AND completed_date IS NULL");
    
    // Tự động xác nhận đơn hàng sau 7 ngày
    $conn->query("UPDATE orders SET customer_confirmed = 1 WHERE order_status = 'Hoàn thành' AND customer_confirmed = 0 AND ((completed_date IS NOT NULL AND DATEDIFF(NOW(), completed_date) >= 7) OR (completed_date IS NULL AND DATEDIFF(NOW(), created_at) >= 7))");
    
    // Tự động hủy đơn chờ thanh toán quá 24h
    $conn->query("UPDATE orders SET order_status = 'Đã hủy', notes = CONCAT(IFNULL(notes, ''), '\nTự động hủy: Quá thời gian thanh toán (24h)') WHERE order_status = 'Chờ thanh toán' AND payment_method = 'bank_transfer' AND TIMESTAMPDIFF(HOUR, created_at, NOW()) >= 24");
    
    if (!is_dir(__DIR__ . '/../logs')) {
        mkdir(__DIR__ . '/../logs', 0777, true);
    }
    file_put_contents($last_auto_run_file, time());
}
?>
<header class="admin-header">
    <div class="admin-header-top">
        <div class="logo-section">
            <?php 
            // Tự động phát hiện đường dẫn logo
            $logo_path = (basename(dirname($_SERVER['PHP_SELF'])) == 'admin') ? '../uploads/logo.jpeg' : 'uploads/logo.jpeg';
            ?>
            <img src="<?php echo $logo_path; ?>" alt="Logo" class="admin-logo">
            <h1>TTHUONG Bookstore - Admin</h1>
        </div>
        
        <div class="admin-header-actions">
            <div class="admin-welcome">
                <i class="fas fa-user-shield"></i>
                <span>Quản trị viên</span>
            </div>
            
            <?php
            $logout_path = (basename(dirname($_SERVER['PHP_SELF'])) == 'admin') ? '../logout.php' : 'logout.php';
            ?>
            <a href="<?php echo $logout_path; ?>" class="btn-admin-logout">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>
    </div>
    
    <nav class="admin-nav">
        <ul class="admin-menu">
            <?php
            // Tự động phát hiện đường dẫn menu
            $is_in_admin = (basename(dirname($_SERVER['PHP_SELF'])) == 'admin');
            $menu_prefix = $is_in_admin ? '' : 'admin/';
            $current_page = basename($_SERVER['PHP_SELF']);
            ?>
            <li>
                <a href="<?php echo $menu_prefix; ?>dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_products.php" class="<?php echo $current_page == 'admin_products.php' || $current_page == 'add_product.php' || $current_page == 'add_product_full.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> Quản lý sách
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_publishers.php" class="<?php echo $current_page == 'admin_publishers.php' ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i> Nhà xuất bản
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_authors.php" class="<?php echo $current_page == 'admin_authors.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-edit"></i> Tác giả
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_categories.php" class="<?php echo $current_page == 'admin_categories.php' ? 'active' : ''; ?>">
                    <i class="fas fa-folder"></i> Danh mục
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_orders.php" class="<?php echo $current_page == 'admin_orders.php' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Đơn hàng
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_returns.php" class="<?php echo $current_page == 'admin_returns.php' ? 'active' : ''; ?>">
                    <i class="fas fa-undo"></i> Trả hàng
                    <?php
                    // Đếm yêu cầu trả hàng chờ duyệt
                    $return_pending_query = "SELECT COUNT(*) as count FROM orders WHERE return_request = 1 AND (return_status IS NULL OR return_status = 'Chờ duyệt')";
                    $return_pending_result = $conn->query($return_pending_query);
                    if ($return_pending_result && ($row = $return_pending_result->fetch_assoc())) {
                        $return_count = $row['count'];
                        if ($return_count > 0): ?>
                            <span class="admin-badge"><?php echo $return_count; ?></span>
                        <?php endif;
                    }
                    ?>
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_payment_confirmation.php" class="<?php echo $current_page == 'admin_payment_confirmation.php' ? 'active' : ''; ?>">
                    <i class="fas fa-check-circle"></i> Xác nhận thanh toán
                    <?php
                    // Đếm đơn chờ thanh toán
                    $pending_payment_query = "SELECT COUNT(*) as count FROM orders WHERE order_status = 'Chờ thanh toán' AND payment_method = 'bank_transfer'";
                    $pending_payment_result = $conn->query($pending_payment_query);
                    if ($pending_payment_result && ($row = $pending_payment_result->fetch_assoc())) {
                        $pending_count = $row['count'];
                        if ($pending_count > 0): ?>
                            <span class="admin-badge"><?php echo $pending_count; ?></span>
                        <?php endif;
                    }
                    ?>
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_users.php" class="<?php echo $current_page == 'admin_users.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Khách hàng
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_reviews.php" class="<?php echo $current_page == 'admin_reviews.php' ? 'active' : ''; ?>">
                    <i class="fas fa-star"></i> Đánh giá
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_chat.php" class="<?php echo $current_page == 'admin_chat.php' ? 'active' : ''; ?>">
                    <i class="fas fa-comments"></i> Chat
                    <?php
                    // Đếm tin nhắn chưa đọc
                    $table_check = $conn->query("SHOW TABLES LIKE 'messages'");
                    if ($table_check && $table_check->num_rows > 0) {
                        $unread_query = "SELECT COUNT(*) as count FROM messages WHERE sender_type = 'user' AND is_read = 0";
                        $unread_result = $conn->query($unread_query);
                        if ($unread_result) {
                            $unread_count = $unread_result->fetch_assoc()['count'];
                            if ($unread_count > 0): ?>
                                <span class="admin-badge"><?php echo $unread_count; ?></span>
                            <?php endif;
                        }
                    }
                    ?>
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_promotions.php" class="<?php echo $current_page == 'admin_promotions.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i> Khuyến mãi
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_blog_posts.php" class="<?php echo $current_page == 'admin_blog_posts.php' ? 'active' : ''; ?>">
                    <i class="fas fa-newspaper"></i> Bài viết Blog
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_revenue.php" class="<?php echo $current_page == 'admin_revenue.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Doanh thu
                </a>
            </li>
            <li>
                <a href="<?php echo $menu_prefix; ?>admin_log.php" class="<?php echo $current_page == 'admin_log.php' ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i> Lịch sử
                </a>
            </li>
        </ul>
    </nav>
</header>
