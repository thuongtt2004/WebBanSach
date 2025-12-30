<?php
require_once 'config/connect.php';

$promotion_id = $_GET['promotion_id'] ?? 0;

$query = $conn->prepare("SELECT category_id FROM promotion_categories WHERE promotion_id = ?");
$query->bind_param("i", $promotion_id);
$query->execute();
$result = $query->get_result();

$category_ids = [];
while ($row = $result->fetch_assoc()) {
    $category_ids[] = $row['category_id'];
}

header('Content-Type: application/json');
echo json_encode($category_ids);

$query->close();
$conn->close();
?>
