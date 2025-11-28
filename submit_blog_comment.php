<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: blog.php');
    exit;
}

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';
$comment_content = isset($_POST['comment_content']) ? trim($_POST['comment_content']) : '';
$parent_comment_id = isset($_POST['parent_comment_id']) && !empty($_POST['parent_comment_id']) ? intval($_POST['parent_comment_id']) : null;

// Validation
if ($post_id <= 0) {
    header("Location: blog_detail.php?slug=$slug&comment_error=" . urlencode('Bài viết không hợp lệ'));
    exit;
}

if (empty($comment_content)) {
    header("Location: blog_detail.php?slug=$slug&comment_error=" . urlencode('Vui lòng nhập nội dung bình luận'));
    exit;
}

if (mb_strlen($comment_content) < 2) {
    header("Location: blog_detail.php?slug=$slug&comment_error=" . urlencode('Bình luận phải có ít nhất 2 ký tự'));
    exit;
}

if (mb_strlen($comment_content) > 1000) {
    header("Location: blog_detail.php?slug=$slug&comment_error=" . urlencode('Bình luận không được quá 1000 ký tự'));
    exit;
}

// Check if user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$guest_name = null;
$guest_email = null;

if (!$user_id) {
    // Guest comment - require name and email
    $guest_name = isset($_POST['guest_name']) ? trim($_POST['guest_name']) : '';
    $guest_email = isset($_POST['guest_email']) ? trim($_POST['guest_email']) : '';
    
    if (empty($guest_name)) {
        header("Location: blog_detail.php?slug=$slug&comment_error=" . urlencode('Vui lòng nhập tên của bạn'));
        exit;
    }
    
    if (empty($guest_email) || !filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
        header("Location: blog_detail.php?slug=$slug&comment_error=" . urlencode('Vui lòng nhập email hợp lệ'));
        exit;
    }
}

// Insert comment
$sql = "INSERT INTO blog_comments (post_id, user_id, guest_name, guest_email, comment_content, parent_comment_id, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 'approved', NOW())";

$stmt = $conn->prepare($sql);

// Handle NULL values properly
if ($parent_comment_id === null) {
    $parent_comment_id_bind = null;
} else {
    $parent_comment_id_bind = $parent_comment_id;
}

if ($user_id === null) {
    $user_id_bind = null;
} else {
    $user_id_bind = $user_id;
}

$stmt->bind_param("iisssi", $post_id, $user_id_bind, $guest_name, $guest_email, $comment_content, $parent_comment_id_bind);

if ($stmt->execute()) {
    header("Location: blog_detail.php?slug=$slug&comment_success=1#comments");
    exit;
} else {
    header("Location: blog_detail.php?slug=$slug&comment_error=" . urlencode('Có lỗi xảy ra. Vui lòng thử lại'));
    exit;
}

$stmt->close();
$conn->close();
?>
