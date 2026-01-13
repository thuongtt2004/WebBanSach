<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_page.php');
    exit();
}

require_once('../config/connect.php');

/** @var mysqli $conn */

// Xử lý xóa tác giả
if (isset($_GET['delete'])) {
    $author_id = $_GET['delete'];
    $delete_query = "DELETE FROM authors WHERE author_id = ?";
    $stmt = $conn->prepare($delete_query);
    if ($stmt) {
        $stmt->bind_param("i", $author_id);
        if ($stmt->execute()) {
            $message = "Xóa tác giả thành công!";
        } else {
            $message = "Lỗi: " . $stmt->error;
        }
    }
}

// Xử lý thêm/sửa tác giả
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $author_name = $_POST['author_name'];
    $pen_name = $_POST['pen_name'];
    $biography = $_POST['biography'];
    $birth_date = $_POST['birth_date'];
    $nationality = $_POST['nationality'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    $awards = $_POST['awards'];
    $status = $_POST['status'];
    
    // Xử lý upload ảnh
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "../uploads/authors/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = 'author_' . time() . '.' . $file_extension;
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_dir . $photo);
    }
    
    if (isset($_POST['author_id']) && !empty($_POST['author_id'])) {
        // Cập nhật
        $author_id = $_POST['author_id'];
        if (!empty($photo)) {
            $update_query = "UPDATE authors SET author_name=?, pen_name=?, biography=?, birth_date=?, nationality=?, email=?, website=?, awards=?, photo=?, status=? WHERE author_id=?";
            $stmt = $conn->prepare($update_query);
            if ($stmt) {
                $stmt->bind_param("ssssssssssi", $author_name, $pen_name, $biography, $birth_date, $nationality, $email, $website, $awards, $photo, $status, $author_id);
                $stmt->execute();
            }
        } else {
            $update_query = "UPDATE authors SET author_name=?, pen_name=?, biography=?, birth_date=?, nationality=?, email=?, website=?, awards=?, status=? WHERE author_id=?";
            $stmt = $conn->prepare($update_query);
            if ($stmt) {
                $stmt->bind_param("sssssssssi", $author_name, $pen_name, $biography, $birth_date, $nationality, $email, $website, $awards, $status, $author_id);
                $stmt->execute();
            }
        }
        $message = "Cập nhật tác giả thành công!";
    } else {
        // Thêm mới
        $insert_query = "INSERT INTO authors (author_name, pen_name, biography, birth_date, nationality, email, website, awards, photo, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        if ($stmt) {
            $stmt->bind_param("ssssssssss", $author_name, $pen_name, $biography, $birth_date, $nationality, $email, $website, $awards, $photo, $status);
            $stmt->execute();
            $message = "Thêm tác giả thành công!";
        }
    }
}

// Lấy danh sách tác giả
$search = isset($_GET['search']) ? $_GET['search'] : '';
$nationality_filter = isset($_GET['nationality']) ? $_GET['nationality'] : '';

$query = "SELECT * FROM authors WHERE 1=1";
if ($search) {
    $query .= " AND (author_name LIKE '%$search%' OR pen_name LIKE '%$search%')";
}
if ($nationality_filter) {
    $query .= " AND nationality = '$nationality_filter'";
}
$query .= " ORDER BY created_at DESC";
$result = $conn->query($query);

// Lấy danh sách quốc tịch
$nationalities = $conn->query("SELECT DISTINCT nationality FROM authors WHERE nationality IS NOT NULL ORDER BY nationality");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tác giả</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/fontawesome/all.min.css">
    <style>
        .authors-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-bar input, .search-bar select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .author-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .modal-content {
            background: white;
            width: 90%;
            max-width: 700px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 10px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .view-modal .modal-content {
            max-width: 600px;
        }
        .author-detail {
            line-height: 1.8;
        }
        .author-detail h3 {
            margin-top: 20px;
            color: #333;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="authors-container">
        <div class="page-header">
            <h1><i class="fas fa-user-edit"></i> Quản lý Tác giả</h1>
            <button class="btn btn-primary" onclick="openModal()">
                <i class="fas fa-plus"></i> Thêm tác giả
            </button>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên, bút danh..." value="<?php echo $search; ?>">
            <select id="nationalityFilter">
                <option value="">Tất cả quốc tịch</option>
                <?php if ($nationalities): while ($nat = $nationalities->fetch_assoc()): ?>
                    <option value="<?php echo $nat['nationality']; ?>" <?php echo $nationality_filter == $nat['nationality'] ? 'selected' : ''; ?>>
                        <?php echo $nat['nationality']; ?>
                    </option>
                <?php endwhile; endif; ?>
            </select>
            <button class="btn btn-primary" onclick="searchAuthors()">
                <i class="fas fa-search"></i> Tìm kiếm
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên tác giả</th>
                        <th>Bút danh</th>
                        <th>Quốc tịch</th>
                        <th>Năm sinh</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result): while ($author = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $author['author_id']; ?></td>
                        <td>
                            <?php if ($author['photo']): ?>
                                <img src="../uploads/authors/<?php echo $author['photo']; ?>" class="author-photo">
                            <?php else: ?>
                                <i class="fas fa-user-circle fa-3x"></i>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($author['author_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($author['pen_name'] ?: '-'); ?></td>
                        <td><?php echo htmlspecialchars($author['nationality']); ?></td>
                        <td><?php echo $author['birth_date'] ? date('Y', strtotime($author['birth_date'])) : '-'; ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $author['status']; ?>">
                                <?php echo $author['status'] == 'active' ? 'Hoạt động' : 'Ngừng hoạt động'; ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-info" onclick='viewAuthor(<?php echo json_encode($author); ?>)'>
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-success" onclick='editAuthor(<?php echo json_encode($author); ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger" onclick="deleteAuthor(<?php echo $author['author_id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="authorModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Thêm tác giả</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="author_id" id="author_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tên tác giả *</label>
                        <input type="text" name="author_name" id="author_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Bút danh</label>
                        <input type="text" name="pen_name" id="pen_name">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Tiểu sử</label>
                    <textarea name="biography" id="biography"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="birth_date" id="birth_date">
                    </div>
                    
                    <div class="form-group">
                        <label>Quốc tịch</label>
                        <input type="text" name="nationality" id="nationality">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="email">
                    </div>
                    
                    <div class="form-group">
                        <label>Website</label>
                        <input type="text" name="website" id="website">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Giải thưởng</label>
                    <textarea name="awards" id="awards" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Ảnh đại diện</label>
                        <input type="file" name="photo" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="status" id="status">
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Ngừng hoạt động</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                    <button type="button" class="btn btn-danger" onclick="closeModal()">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal View Detail -->
    <div id="viewModal" class="modal view-modal">
        <div class="modal-content">
            <h2><i class="fas fa-user"></i> Thông tin tác giả</h2>
            <div id="authorDetail" class="author-detail"></div>
            <button class="btn btn-primary" onclick="closeViewModal()" style="margin-top: 20px;">
                <i class="fas fa-times"></i> Đóng
            </button>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalTitle').innerText = 'Thêm tác giả';
            document.getElementById('author_id').value = '';
            document.querySelector('form').reset();
            document.getElementById('authorModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('authorModal').style.display = 'none';
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        function viewAuthor(author) {
            let html = `
                <div style="text-align: center; margin-bottom: 20px;">
                    ${author.photo ? `<img src="../uploads/authors/${author.photo}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">` : '<i class="fas fa-user-circle" style="font-size: 100px;"></i>'}
                </div>
                <h3><i class="fas fa-user"></i> ${author.author_name}</h3>
                ${author.pen_name ? `<p><strong>Bút danh:</strong> ${author.pen_name}</p>` : ''}
                ${author.birth_date ? `<p><strong>Ngày sinh:</strong> ${new Date(author.birth_date).toLocaleDateString('vi-VN')}</p>` : ''}
                ${author.nationality ? `<p><strong>Quốc tịch:</strong> ${author.nationality}</p>` : ''}
                ${author.email ? `<p><strong>Email:</strong> ${author.email}</p>` : ''}
                ${author.website ? `<p><strong>Website:</strong> <a href="${author.website}" target="_blank">${author.website}</a></p>` : ''}
                ${author.biography ? `<h3><i class="fas fa-book"></i> Tiểu sử</h3><p>${author.biography}</p>` : ''}
                ${author.awards ? `<h3><i class="fas fa-trophy"></i> Giải thưởng</h3><p>${author.awards}</p>` : ''}
            `;
            document.getElementById('authorDetail').innerHTML = html;
            document.getElementById('viewModal').style.display = 'block';
        }

        function editAuthor(author) {
            document.getElementById('modalTitle').innerText = 'Sửa tác giả';
            document.getElementById('author_id').value = author.author_id;
            document.getElementById('author_name').value = author.author_name;
            document.getElementById('pen_name').value = author.pen_name || '';
            document.getElementById('biography').value = author.biography || '';
            document.getElementById('birth_date').value = author.birth_date || '';
            document.getElementById('nationality').value = author.nationality || '';
            document.getElementById('email').value = author.email || '';
            document.getElementById('website').value = author.website || '';
            document.getElementById('awards').value = author.awards || '';
            document.getElementById('status').value = author.status;
            document.getElementById('authorModal').style.display = 'block';
        }

        function deleteAuthor(id) {
            if (confirm('Bạn có chắc chắn muốn xóa tác giả này?')) {
                window.location.href = '?delete=' + id;
            }
        }

        function searchAuthors() {
            const search = document.getElementById('searchInput').value;
            const nationality = document.getElementById('nationalityFilter').value;
            window.location.href = '?search=' + search + '&nationality=' + nationality;
        }

        window.onclick = function(event) {
            const modal = document.getElementById('authorModal');
            const viewModal = document.getElementById('viewModal');
            if (event.target == modal) {
                closeModal();
            }
            if (event.target == viewModal) {
                closeViewModal();
            }
        }
    </script>
</body>
</html>
