<?php
session_start();
require_once '../config/connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$product_id = $_GET['product_id'] ?? null;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit();
}

// Lấy chi tiết các đánh giá của sản phẩm
$sql = "SELECT 
            r.review_id,
            r.rating,
            r.content,
            r.review_date,
            r.images,
            r.admin_reply,
            r.admin_reply_date,
            u.username
        FROM reviews r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.product_id = ?
        ORDER BY r.review_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    // Parse images JSON
    $images = [];
    if (!empty($row['images'])) {
        $decoded = json_decode($row['images'], true);
        if (is_array($decoded)) {
            $images = $decoded;
        }
    }
    
    $reviews[] = [
        'review_id' => $row['review_id'],
        'username' => $row['username'],
        'rating' => (int)$row['rating'],
        'content' => $row['content'],
        'review_date' => date('d/m/Y H:i', strtotime($row['review_date'])),
        'images' => $images,
        'admin_reply' => $row['admin_reply'],
        'admin_reply_date' => $row['admin_reply_date'] ? date('d/m/Y H:i', strtotime($row['admin_reply_date'])) : null
    ];
}

echo json_encode([
    'success' => true,
    'reviews' => $reviews
]);
