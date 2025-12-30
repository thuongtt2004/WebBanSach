<?php
session_start();
require_once 'config/connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách product_id trong wishlist của user
$sql = "SELECT product_id FROM wishlist WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$wishlist = [];
while ($row = $result->fetch_assoc()) {
    $wishlist[] = $row['product_id'];
}

echo json_encode([
    'success' => true,
    'wishlist' => $wishlist
]);

$conn->close();
