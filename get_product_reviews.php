<?php
require_once 'config/connect.php';

header('Content-Type: application/json');

$product_id = isset($_GET['product_id']) ? trim($_GET['product_id']) : '';

if (empty($product_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit();
}

try {
    // Lấy rating trung bình và tổng số reviews
    $avg_sql = "SELECT 
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating
                FROM reviews 
                WHERE product_id = ?";
    $avg_stmt = $conn->prepare($avg_sql);
    $avg_stmt->bind_param("s", $product_id);
    $avg_stmt->execute();
    $avg_result = $avg_stmt->get_result();
    $avg_data = $avg_result->fetch_assoc();
    
    // Lấy danh sách reviews
    $reviews_sql = "SELECT 
                        r.rating,
                        r.content,
                        r.images,
                        r.review_date,
                        r.admin_reply,
                        r.admin_reply_date,
                        u.full_name as user_name
                    FROM reviews r
                    JOIN users u ON r.user_id = u.user_id
                    WHERE r.product_id = ?
                    ORDER BY r.review_date DESC
                    LIMIT 10";
    $reviews_stmt = $conn->prepare($reviews_sql);
    $reviews_stmt->bind_param("s", $product_id);
    $reviews_stmt->execute();
    $reviews_result = $reviews_stmt->get_result();
    
    $reviews = [];
    while ($row = $reviews_result->fetch_assoc()) {
        $images = [];
        if (!empty($row['images'])) {
            $decoded_images = json_decode($row['images'], true);
            if (is_array($decoded_images)) {
                $images = $decoded_images;
            }
        }
        
        $reviews[] = [
            'rating' => intval($row['rating']),
            'content' => htmlspecialchars($row['content']),
            'images' => $images,
            'user_name' => htmlspecialchars($row['user_name']),
            'created_at' => date('d/m/Y H:i', strtotime($row['review_date'])),
            'admin_reply' => $row['admin_reply'] ? htmlspecialchars($row['admin_reply']) : null,
            'admin_reply_date' => $row['admin_reply_date'] ? date('d/m/Y H:i', strtotime($row['admin_reply_date'])) : null
        ];
    }
    
    echo json_encode([
        'success' => true,
        'average_rating' => floatval($avg_data['average_rating']),
        'total_reviews' => intval($avg_data['total_reviews']),
        'reviews' => $reviews
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
