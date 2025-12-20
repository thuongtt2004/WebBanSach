<?php
require_once 'config/db.php';
require_once 'header.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: blog.php');
    exit();
}

// Lấy bài viết
$sql = "SELECT bp.*, bc.category_name, bc.slug as category_slug 
        FROM blog_posts bp 
        LEFT JOIN blog_categories bc ON bp.category_id = bc.category_id 
        WHERE bp.slug = ? AND bp.status = 'published'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    header('Location: blog.php');
    exit();
}

// Tăng lượt xem
$update_views = "UPDATE blog_posts SET views = views + 1 WHERE post_id = ?";
$stmt_views = $conn->prepare($update_views);
$stmt_views->bind_param("i", $post['post_id']);
$stmt_views->execute();

// Lấy bài viết liên quan
$related_sql = "SELECT * FROM blog_posts 
                WHERE category_id = ? AND post_id != ? AND status = 'published' 
                ORDER BY published_at DESC LIMIT 3";
$stmt_related = $conn->prepare($related_sql);
$stmt_related->bind_param("ii", $post['category_id'], $post['post_id']);
$stmt_related->execute();
$related_posts = $stmt_related->get_result();

// Lấy comments
$comments_sql = "SELECT bc.*, u.username, u.full_name 
                 FROM blog_comments bc 
                 LEFT JOIN users u ON bc.user_id = u.user_id 
                 WHERE bc.post_id = ? AND bc.status = 'approved' AND bc.parent_id IS NULL
                 ORDER BY bc.created_at DESC";
$stmt_comments = $conn->prepare($comments_sql);
$stmt_comments->bind_param("i", $post['post_id']);
$stmt_comments->execute();
$comments = $stmt_comments->get_result();

// Lấy replies cho mỗi comment
$replies_sql = "SELECT bc.*, u.username, u.full_name 
                FROM blog_comments bc 
                LEFT JOIN users u ON bc.user_id = u.user_id 
                WHERE bc.parent_id = ? AND bc.status = 'approved'
                ORDER BY bc.created_at ASC";

// Đếm tổng số comments
$count_sql = "SELECT COUNT(*) as total FROM blog_comments WHERE post_id = ? AND status = 'approved'";
$stmt_count = $conn->prepare($count_sql);
$stmt_count->bind_param("i", $post['post_id']);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$total_comments = $count_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - TTHUONG Bookstore</title>
    <meta name="description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <!-- Breadcrumb -->
    <div class="breadcrumb-container">
        <div class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
            <span class="separator">/</span>
            <a href="blog.php">Blog</a>
            <span class="separator">/</span>
            <span class="current"><?php echo htmlspecialchars($post['title']); ?></span>
        </div>
    </div>

    <!-- Blog Detail -->
    <div class="blog-detail-container">
        <article class="blog-detail-main">
            <!-- Header -->
            <header class="article-header">
                <div class="article-category">
                    <a href="blog.php?category=<?php echo $post['category_id']; ?>">
                        <?php echo htmlspecialchars($post['category_name']); ?>
                    </a>
                </div>
                
                <h1 class="article-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                
                <div class="article-meta">
                    <span><i class="fas fa-calendar"></i> 
                        <?php echo date('d/m/Y H:i', strtotime($post['published_at'] ?? $post['created_at'])); ?>
                    </span>
                    <span><i class="fas fa-eye"></i> <?php echo number_format($post['views']); ?> lượt xem</span>
                </div>
            </header>

            <!-- Featured Image -->
            <?php if ($post['featured_image']): ?>
                <div class="article-featured-image">
                    <?php 
                    // Xử lý đường dẫn hình ảnh
                    $image_path = $post['featured_image'];
                    if (!preg_match('/^(https?:\/\/|uploads\/)/i', $image_path)) {
                        $image_path = 'uploads/blog/' . $image_path;
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($image_path); ?>" 
                         alt="<?php echo htmlspecialchars($post['title']); ?>"
                         onerror="this.style.display='none'">
                </div>
            <?php endif; ?>

            <!-- Content -->
            <div class="article-content">
                <?php echo $post['content']; ?>
            </div>

            <!-- Share -->
            <div class="article-share">
                <h3>Chia sẻ bài viết</h3>
                <div class="share-buttons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                       target="_blank" class="share-btn facebook">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" 
                       target="_blank" class="share-btn twitter">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <button onclick="copyLink()" class="share-btn copy">
                        <i class="fas fa-link"></i> Copy link
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <div class="article-navigation">
                <a href="blog.php" class="nav-back">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>

            <!-- Comments Section -->
            <div class="comments-section">
                <h2 class="comments-title">
                    <i class="fas fa-comments"></i> 
                    Bình luận (<?php echo $total_comments; ?>)
                </h2>

                <!-- Comment Form -->
                <div class="comment-form-container">
                    <h3>Để lại bình luận của bạn</h3>
                    
                    <?php if (isset($_GET['comment_error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($_GET['comment_error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['comment_success'])): ?>
                        <div class="alert alert-success">
                            Bình luận của bạn đã được gửi thành công!
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="submit_blog_comment.php" class="comment-form">
                        <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                        <input type="hidden" name="slug" value="<?php echo $post['slug']; ?>">
                        <input type="hidden" name="parent_id" id="parentCommentId" value="">
                        
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="guestName">Tên của bạn <span class="required">*</span></label>
                                    <input type="text" id="guestName" name="guest_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="guestEmail">Email <span class="required">*</span></label>
                                    <input type="email" id="guestEmail" name="guest_email" required>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="commentContent">Nội dung <span class="required">*</span></label>
                            <textarea id="commentContent" name="comment_content" rows="5" required 
                                      placeholder="Nhập bình luận của bạn..." maxlength="1000"></textarea>
                            <div class="char-count">
                                <span id="charCount">0</span>/1000 ký tự
                            </div>
                        </div>
                        
                        <div id="replyingTo" class="replying-to" style="display: none;">
                            Đang trả lời: <span id="replyingToName"></span>
                            <button type="button" onclick="cancelReply()" class="cancel-reply" title="Hủy trả lời">
                                <i class="fas fa-times"></i> Hủy
                            </button>
                        </div>
                        
                        <button type="submit" class="btn-submit-comment">
                            <i class="fas fa-paper-plane"></i> Gửi bình luận
                        </button>
                    </form>
                </div>

                <!-- Comments List -->
                <div class="comments-list">
                    <?php if ($comments->num_rows > 0): ?>
                        <?php while ($comment = $comments->fetch_assoc()): ?>
                            <div class="comment-item" id="comment-<?php echo $comment['comment_id']; ?>">
                                <div class="comment-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="comment-body">
                                    <div class="comment-header">
                                        <span class="comment-author">
                                            <?php 
                                            if ($comment['user_id']) {
                                                echo htmlspecialchars($comment['full_name'] ?: $comment['username']);
                                            } else {
                                                echo htmlspecialchars($comment['author_name']);
                                            }
                                            ?>
                                        </span>
                                        <span class="comment-date">
                                            <i class="fas fa-clock"></i>
                                            <?php 
                                            $time_ago = time() - strtotime($comment['created_at']);
                                            if ($time_ago < 60) echo 'Vừa xong';
                                            elseif ($time_ago < 3600) echo floor($time_ago/60) . ' phút trước';
                                            elseif ($time_ago < 86400) echo floor($time_ago/3600) . ' giờ trước';
                                            else echo date('d/m/Y H:i', strtotime($comment['created_at']));
                                            ?>
                                        </span>
                                    </div>
                                    <div class="comment-content">
                                        <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                    </div>
                                    <div class="comment-actions">
                                        <button class="btn-reply" onclick="replyToComment(<?php echo $comment['comment_id']; ?>, '<?php echo htmlspecialchars($comment['user_id'] ? ($comment['full_name'] ?: $comment['username']) : $comment['author_name']); ?>')">
                                            <i class="fas fa-reply"></i> Trả lời
                                        </button>
                                    </div>

                                    <!-- Replies -->
                                    <?php
                                    $stmt_replies = $conn->prepare($replies_sql);
                                    $stmt_replies->bind_param("i", $comment['comment_id']);
                                    $stmt_replies->execute();
                                    $replies = $stmt_replies->get_result();
                                    
                                    if ($replies->num_rows > 0):
                                    ?>
                                        <div class="comment-replies">
                                            <?php while ($reply = $replies->fetch_assoc()): ?>
                                                <div class="comment-item reply" id="comment-<?php echo $reply['comment_id']; ?>">
                                                    <div class="comment-avatar">
                                                        <i class="fas fa-user-circle"></i>
                                                    </div>
                                                    <div class="comment-body">
                                                        <div class="comment-header">
                                                            <span class="comment-author">
                                                                <?php 
                                                                if ($reply['user_id']) {
                                                                    echo htmlspecialchars($reply['full_name'] ?: $reply['username']);
                                                                } else {
                                                                    echo htmlspecialchars($reply['author_name']);
                                                                }
                                                                ?>
                                                            </span>
                                                            <span class="comment-date">
                                                                <i class="fas fa-clock"></i>
                                                                <?php 
                                                                $time_ago = time() - strtotime($reply['created_at']);
                                                                if ($time_ago < 60) echo 'Vừa xong';
                                                                elseif ($time_ago < 3600) echo floor($time_ago/60) . ' phút trước';
                                                                elseif ($time_ago < 86400) echo floor($time_ago/3600) . ' giờ trước';
                                                                else echo date('d/m/Y H:i', strtotime($reply['created_at']));
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="comment-content">
                                                            <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-comments">
                            <i class="fas fa-comment-slash"></i>
                            <p>Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </article>

        <!-- Sidebar -->
        <aside class="blog-detail-sidebar">
            <?php if ($related_posts->num_rows > 0): ?>
                <div class="sidebar-widget">
                    <h3>Bài viết liên quan</h3>
                    <div class="related-posts">
                        <?php while ($related = $related_posts->fetch_assoc()): ?>
                            <a href="blog_detail.php?slug=<?php echo $related['slug']; ?>" class="related-post-item">
                                <?php if ($related['featured_image']): ?>
                                    <?php 
                                    $related_image = $related['featured_image'];
                                    if (!preg_match('/^(https?:\/\/|uploads\/)/i', $related_image)) {
                                        $related_image = 'uploads/blog/' . $related_image;
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($related_image); ?>" 
                                         alt="<?php echo htmlspecialchars($related['title']); ?>"
                                         onerror="this.style.display='none'">
                                <?php endif; ?>
                                <div class="related-post-info">
                                    <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                                    <span class="related-post-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('d/m/Y', strtotime($related['published_at'] ?? $related['created_at'])); ?>
                                    </span>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>
        </aside>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function copyLink() {
            navigator.clipboard.writeText(window.location.href);
            alert('Đã copy link bài viết!');
        }

        // Character count for comment
        const commentContent = document.getElementById('commentContent');
        const charCount = document.getElementById('charCount');
        
        if (commentContent) {
            commentContent.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }

        // Reply to comment
        function replyToComment(commentId, authorName) {
            document.getElementById('parentCommentId').value = commentId;
            document.getElementById('replyingTo').style.display = 'block';
            document.getElementById('replyingToName').textContent = authorName;
            
            // Scroll to form
            document.querySelector('.comment-form-container').scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Focus on textarea
            setTimeout(function() {
                document.getElementById('commentContent').focus();
            }, 500);
        }

        function cancelReply() {
            document.getElementById('parentCommentId').value = '';
            document.getElementById('replyingTo').style.display = 'none';
            document.getElementById('replyingToName').textContent = '';
        }

        // Auto scroll to comment section if there's a message
        <?php if (isset($_GET['comment_error']) || isset($_GET['comment_success'])): ?>
            window.addEventListener('load', function() {
                document.querySelector('.comment-form-container').scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        <?php endif; ?>
    </script>

    <style>
        * {
            box-sizing: border-box;
        }

        .breadcrumb-container {
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px 0;
        }
        
        .breadcrumb {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }
        
        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            color: #0056b3;
        }
        
        .breadcrumb .separator {
            color: #999;
        }

        .breadcrumb .current {
            color: #333;
            font-weight: 500;
        }
        
        .blog-detail-container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 40px;
            align-items: start;
        }
        
        .blog-detail-main {
            background: white;
            padding: 50px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .article-header {
            margin-bottom: 35px;
            padding-bottom: 25px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .article-category a {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .article-category a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .article-title {
            font-size: 42px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 20px 0;
            line-height: 1.3;
        }
        
        .article-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            color: #666;
            font-size: 15px;
        }
        
        .article-meta span {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .article-meta i {
            color: #667eea;
        }
        
        .article-featured-image {
            margin: 30px auto 40px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            max-width: 100%;
            max-height: 450px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f9fa;
        }
        
        .article-featured-image img {
            width: 100%;
            height: 100%;
            max-height: 450px;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }

        .article-featured-image:hover img {
            transform: scale(1.05);
        }
        
        .article-content {
            font-size: 18px;
            line-height: 1.9;
            color: #333;
            margin-bottom: 40px;
        }

        .article-content h2 {
            font-size: 32px;
            color: #1a1a1a;
            margin: 35px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
        }

        .article-content h3 {
            font-size: 26px;
            color: #333;
            margin: 30px 0 15px 0;
        }

        .article-content p {
            margin-bottom: 20px;
        }

        .article-content ul, .article-content ol {
            margin: 20px 0;
            padding-left: 30px;
        }

        .article-content li {
            margin-bottom: 12px;
            line-height: 1.8;
        }

        .article-content strong {
            color: #1a1a1a;
            font-weight: 600;
        }

        .article-content em {
            color: #555;
        }
        
        .article-share {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .article-share h3 {
            margin: 0 0 20px 0;
            font-size: 20px;
            color: #333;
        }
        
        .share-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .share-btn {
            flex: 1;
            min-width: 120px;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .share-btn.facebook {
            background: #1877f2;
            color: white;
        }
        
        .share-btn.twitter {
            background: #1da1f2;
            color: white;
        }
        
        .share-btn.copy {
            background: #6c757d;
            color: white;
        }
        
        .share-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        }
        
        .article-navigation {
            text-align: center;
        }
        
        .nav-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 30px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border: 2px solid #667eea;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .nav-back:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        /* Sidebar */
        .blog-detail-sidebar {
            position: sticky;
            top: 20px;
        }
        
        .sidebar-widget {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        
        .sidebar-widget h3 {
            margin: 0 0 25px 0;
            font-size: 22px;
            color: #1a1a1a;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }
        
        .related-posts {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .related-post-item {
            display: flex;
            gap: 15px;
            text-decoration: none;
            color: inherit;
            padding: 15px;
            border-radius: 10px;
            transition: all 0.3s;
            border: 1px solid #f0f0f0;
        }
        
        .related-post-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .related-post-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }
        
        .related-post-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .related-post-info h4 {
            margin: 0 0 10px 0;
            font-size: 15px;
            line-height: 1.4;
            color: #333;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .related-post-date {
            font-size: 13px;
            color: #999;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .blog-detail-container {
                grid-template-columns: 1fr;
            }
            
            .blog-detail-main {
                padding: 35px;
            }

            .article-title {
                font-size: 32px;
            }

            .article-content {
                font-size: 16px;
            }

            .article-content h2 {
                font-size: 26px;
            }

            .article-content h3 {
                font-size: 22px;
            }
            
            .blog-detail-sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .blog-detail-main {
                padding: 25px;
            }

            .article-title {
                font-size: 26px;
            }

            .article-meta {
                gap: 15px;
                font-size: 14px;
            }
            
            .share-buttons {
                flex-direction: column;
            }

            .share-btn {
                min-width: auto;
                width: 100%;
            }
        }

        /* Comments Section */
        .comments-section {
            margin-top: 60px;
            padding-top: 40px;
            border-top: 3px solid #f0f0f0;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .comments-title {
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .comments-title i {
            color: #667eea;
        }

        /* Comment Form */
        .comment-form-container {
            background: #f8f9fa;
            padding: 35px;
            border-radius: 16px;
            margin-bottom: 40px;
        }

        .comment-form-container h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 25px;
        }

        .comment-form {
            display: block;
            width: 100%;
        }

        .comment-form .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .comment-form .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .comment-form label {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        .comment-form .required {
            color: #dc3545;
        }

        .comment-form input,
        .comment-form textarea {
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
            background: white !important;
            width: 100%;
            display: block !important;
            position: relative;
            z-index: 1;
            color: #333 !important;
            line-height: 1.6;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .comment-form input::placeholder,
        .comment-form textarea::placeholder {
            color: #999;
        }

        .comment-form input:focus,
        .comment-form textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .comment-form textarea {
            resize: vertical;
            min-height: 100px;
        }

        .replying-to {
            background: #e3f2fd;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            color: #1976d2;
        }

        .cancel-reply {
            background: none;
            border: none;
            color: #d32f2f;
            cursor: pointer;
            padding: 5px;
            font-size: 16px;
            transition: color 0.3s;
        }

        .cancel-reply:hover {
            color: #b71c1c;
        }

        .char-count {
            text-align: right;
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        .btn-submit-comment {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 35px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-submit-comment:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-submit-comment:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Comments List */
        .comments-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .comment-item {
            display: flex;
            gap: 15px;
            padding: 25px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .comment-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .comment-item.reply {
            margin-left: 60px;
            background: #f8f9fa;
        }

        .comment-avatar {
            flex-shrink: 0;
        }

        .comment-avatar i {
            font-size: 48px;
            color: #667eea;
        }

        .comment-body {
            flex: 1;
        }

        .comment-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .comment-author {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .comment-date {
            font-size: 13px;
            color: #999;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .comment-content {
            font-size: 15px;
            line-height: 1.7;
            color: #333;
            margin-bottom: 15px;
        }

        .comment-actions {
            display: flex;
            gap: 15px;
        }

        .btn-reply {
            background: none;
            border: none;
            color: #667eea;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.3s;
        }

        .btn-reply:hover {
            color: #764ba2;
        }

        .comment-replies {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .no-comments {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .no-comments i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .no-comments p {
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .comment-form-container {
                padding: 25px;
            }

            .comment-form .form-row {
                grid-template-columns: 1fr;
            }

            .comment-item.reply {
                margin-left: 30px;
            }

            .comment-avatar i {
                font-size: 36px;
            }

            .btn-submit-comment {
                width: 100%;
                justify-content: center;
            }

            .comments-title {
                font-size: 22px;
            }
        }
    </style>

</body>
</html>
