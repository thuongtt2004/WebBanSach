<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

echo "=== KIỂM TRA ẢNH CHỨNG TỪ ĐỚN #422 ===\n\n";

$result = $conn->query("SELECT order_id, payment_proof, payment_method FROM orders WHERE order_id = 422");
$order = $result->fetch_assoc();

echo "Đơn #422:\n";
echo "Payment method: {$order['payment_method']}\n";
echo "Payment proof (DB): {$order['payment_proof']}\n\n";

if (!empty($order['payment_proof'])) {
    $file_path = __DIR__ . '/' . $order['payment_proof'];
    echo "Đường dẫn đầy đủ: $file_path\n";
    
    if (file_exists($file_path)) {
        echo "✓ File TỒN TẠI\n";
        echo "  Size: " . filesize($file_path) . " bytes\n";
    } else {
        echo "✗ FILE KHÔNG TỒN TẠI!\n";
        echo "  → Cần xóa đường dẫn trong database\n\n";
        
        // Xóa đường dẫn ảnh không tồn tại
        $update = "UPDATE orders SET payment_proof = NULL WHERE order_id = 422";
        if ($conn->query($update)) {
            echo "✓ Đã xóa đường dẫn ảnh lỗi trong database\n";
        }
    }
} else {
    echo "Đơn này chưa có ảnh chứng từ\n";
}

// Kiểm tra thư mục uploads
echo "\n=== KIỂM TRA THỦ MỤC UPLOADS ===\n";
$upload_dir = __DIR__ . '/uploads/payment_proofs/';
if (is_dir($upload_dir)) {
    echo "✓ Thư mục tồn tại: $upload_dir\n";
    $files = scandir($upload_dir);
    echo "Các file trong thư mục:\n";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "  - $file\n";
        }
    }
} else {
    echo "✗ Thư mục không tồn tại!\n";
}
