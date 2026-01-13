<?php
require_once 'config/db.php';
require_once 'header.php';

// Lấy tham số lọc
$category = isset($_GET['category']) && is_numeric($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build SQL query
$sql = "SELECT bp.*, bc.category_name as category 
        FROM blog_posts bp 
        LEFT JOIN blog_categories bc ON bp.category_id = bc.category_id 
        WHERE bp.status = 'published'";
$params = [];
$types = '';

if ($category > 0) {
    $sql .= " AND bp.category_id = ?";
    $params[] = $category;
    $types .= 'i';
}

if (!empty($search)) {
    $sql .= " AND (bp.title LIKE ? OR bp.excerpt LIKE ? OR bp.content LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

$sql .= " ORDER BY bp.published_at DESC, bp.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Lấy danh mục
$categories_sql = "SELECT * FROM blog_categories ORDER BY category_name";
$categories_result = $conn->query($categories_sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Tin tức sách - TTHUONG Bookstore</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fontawesome/all.min.css">
</head>
<body>

    <!-- Breadcrumb -->
    <div class="breadcrumb-container">
        <div class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
            <span class="separator">/</span>
            <span class="current">Blog</span>
        </div>
    </div>

    <!-- Blog Page -->
    <div class="blog-page-container">
        <!-- Sidebar -->
        <aside class="blog-sidebar">
            <div class="sidebar-widget">
                <h3><i class="fas fa-search"></i> Tìm kiếm</h3>
                <form method="GET" action="" class="search-form">
                    <input type="text" name="search" placeholder="Tìm bài viết..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="sidebar-widget">
                <h3><i class="fas fa-list"></i> Danh mục</h3>
                <ul class="category-list">
                    <li>
                        <a href="blog.php" class="<?php echo empty($category) ? 'active' : ''; ?>">
                            Tất cả bài viết
                        </a>
                    </li>
                    <?php while ($cat = $categories_result->fetch_assoc()): ?>
                        <li>
                            <a href="?category=<?php echo urlencode($cat['category_name']); ?>"
                               class="<?php echo $category === $cat['category_name'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="blog-main">
            <div class="blog-header">
                <h1><i class="fas fa-newspaper"></i> Blog & Tin tức</h1>
                <?php if (!empty($category) || !empty($search)): ?>
                    <div class="filter-info">
                        <?php if (!empty($category)): ?>
                            <span class="filter-tag">
                                Danh mục: <?php echo htmlspecialchars($category); ?>
                                <a href="blog.php"><i class="fas fa-times"></i></a>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($search)): ?>
                            <span class="filter-tag">
                                Tìm kiếm: "<?php echo htmlspecialchars($search); ?>"
                                <a href="blog.php"><i class="fas fa-times"></i></a>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="blog-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($post = $result->fetch_assoc()): ?>
                        <article class="blog-card">
                            <?php if ($post['featured_image']): ?>
                                <div class="blog-card-image">
                                    <?php 
                                    // Xử lý đường dẫn hình ảnh - thêm prefix uploads/blog/
                                    $image_path = $post['featured_image'];
                                    // Nếu không phải URL đầy đủ và không bắt đầu bằng uploads/
                                    if (!preg_match('/^(https?:\/\/|uploads\/)/i', $image_path)) {
                                        $image_path = 'uploads/blog/' . $image_path;
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                         alt="<?php echo htmlspecialchars($post['title']); ?>"
                                         onerror="this.src='images/no-image.jpg'">
                                    <span class="blog-category-badge">
                                        <?php echo htmlspecialchars($post['category'] ?? 'Chưa phân loại'); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="blog-card-content">
                                <div class="blog-meta">
                                    <span><i class="fas fa-calendar"></i> 
                                        <?php echo date('d/m/Y', strtotime($post['published_at'] ?? $post['created_at'])); ?>
                                    </span>
                                    <span><i class="fas fa-eye"></i> <?php echo number_format($post['views']); ?> lượt xem</span>
                                </div>
                                
                                <h2 class="blog-title">
                                    <a href="blog_detail.php?slug=<?php echo $post['slug']; ?>">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </h2>
                                
                                <p class="blog-excerpt">
                                    <?php echo htmlspecialchars($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 150) . '...'); ?>
                                </p>
                                
                                <a href="blog_detail.php?slug=<?php echo $post['slug']; ?>" class="read-more">
                                    Đọc thêm <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-posts">
                        <i class="fas fa-inbox"></i>
                        <p>Không tìm thấy bài viết nào</p>
                        <?php if (!empty($category) || !empty($search)): ?>
                            <a href="blog.php" class="btn btn-primary">Xem tất cả bài viết</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <?php include 'footer.php'; ?>

    <style>
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
            color: #333333;
            text-decoration: none;
        }
        
        .breadcrumb .separator {
            color: #999;
        }
        
        .breadcrumb .current {
            color: #333;
            font-weight: 500;
        }
        
        .blog-page-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
        }
        
        /* Sidebar */
        .blog-sidebar {
            position: sticky;
            top: 20px;
            height: fit-content;
        }
        
        .sidebar-widget {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .sidebar-widget h3 {
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #333;
        }
        
        .search-form {
            display: flex;
            gap: 8px;
        }
        
        .search-form input {
            flex: 1;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .search-form button {
            padding: 10px 15px;
            background: #333333;
            color: #EBE9E5;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .category-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .category-list li {
            margin-bottom: 8px;
        }
        
        .category-list a {
            display: block;
            padding: 10px 12px;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .category-list a:hover,
        .category-list a.active {
            background: #333333;
            color: #EBE9E5;
        }
        
        /* Main Content */
        .blog-main {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .blog-header {
            margin-bottom: 30px;
        }
        
        .blog-header h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .filter-info {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .filter-tag {
            background: #EBE9E5;
            color: #333333;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-tag a {
            color: #333333;
            text-decoration: none;
        }
        
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .blog-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        
        .blog-card-image {
            position: relative;
            height: 220px;
            overflow: hidden;
        }
        
        .blog-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        
        .blog-card:hover .blog-card-image img {
            transform: scale(1.05);
        }
        
        .blog-category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(102, 126, 234, 0.9);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .blog-card-content {
            padding: 20px;
        }
        
        .blog-meta {
            display: flex;
            gap: 15px;
            color: #666;
            font-size: 13px;
            margin-bottom: 12px;
        }
        
        .blog-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .blog-title {
            font-size: 20px;
            margin: 0 0 12px 0;
        }
        
        .blog-title a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .blog-title a:hover {
            color: #667eea;
        }
        
        .blog-excerpt {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .read-more {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #333333;
            text-decoration: none;
            font-weight: 600;
            transition: gap 0.3s;
        }
        
        .read-more:hover {
            gap: 12px;
        }
        
        .no-posts {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .no-posts i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .no-posts p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 992px) {
            .blog-page-container {
                grid-template-columns: 1fr;
            }
            
            .blog-sidebar {
                position: static;
            }
            
            .blog-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }
    </style>
</body>
</html>
