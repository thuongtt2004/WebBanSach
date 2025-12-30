<?php
require_once 'config/connect.php';

/** @var mysqli $conn */

$promotion_id = $_GET['promotion_id'] ?? 0;

$query = $conn->prepare("SELECT product_id FROM promotion_products WHERE promotion_id = ?");
if ($query) {
    $query->bind_param("i", $promotion_id);
    $query->execute();
    $result = $query->get_result();

    $product_ids = [];
    while ($row = $result->fetch_assoc()) {
        $product_ids[] = $row['product_id'];
    }

    $query->close();
}

header('Content-Type: application/json');
echo json_encode($product_ids ?? []);

$conn->close();
?>
