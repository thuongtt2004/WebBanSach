<?php
echo "=== KIỂM TRA FILE ADMIN BLOG ===\n\n";

// Kiểm tra file tồn tại
$files = [
    'admin_blog_posts.php' => __DIR__ . '/admin/admin_blog_posts.php',
    'admin_blog_form.php' => __DIR__ . '/admin/admin_blog_form.php'
];

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        echo "✓ $name TỒN TẠI\n";
        echo "  Đường dẫn: $path\n";
        echo "  Kích thước: " . filesize($path) . " bytes\n";
    } else {
        echo "✗ $name KHÔNG TỒN TẠI\n";
        echo "  Đường dẫn tìm kiếm: $path\n";
    }
    echo "\n";
}

// Kiểm tra database có bảng blog không
require_once 'config/connect.php';
/** @var mysqli $conn */

echo "=== KIỂM TRA DATABASE ===\n\n";

$tables = ['blog_posts', 'blog_categories', 'authors'];
foreach ($tables as $table) {
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check && $check->num_rows > 0) {
        echo "✓ Bảng $table TỒN TẠI\n";
        $count = $conn->query("SELECT COUNT(*) as count FROM $table");
        if ($count) {
            $c = $count->fetch_assoc();
            echo "  Số bản ghi: {$c['count']}\n";
        }
    } else {
        echo "✗ Bảng $table KHÔNG TỒN TẠI\n";
    }
}

echo "\n=== KIỂM TRA ERRORS PHP ===\n";
echo "Bật error reporting để debug...\n";
echo "Truy cập: http://localhost/BanSach/src/admin/admin_blog_form.php\n";
echo "Và xem có lỗi gì trong console/network tab\n";
