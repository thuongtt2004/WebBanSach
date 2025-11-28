<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_page.php');
    exit();
}

require_once('../config/connect.php');

// Xử lý xóa nhà xuất bản
if (isset($_GET['delete'])) {
    $publisher_id = $_GET['delete'];
    $delete_query = "DELETE FROM publishers WHERE publisher_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $publisher_id);
    if ($stmt->execute()) {
        $message = "Xóa nhà xuất bản thành công!";
    } else {
        $message = "Lỗi: " . $stmt->error;
    }
}

// Xử lý thêm/sửa nhà xuất bản
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $publisher_name = $_POST['publisher_name'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    $status = $_POST['status'];
    
    // Xử lý upload logo
    $logo = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = "../uploads/publishers/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo = 'publisher_' . time() . '.' . $file_extension;
        move_uploaded_file($_FILES['logo']['tmp_name'], $target_dir . $logo);
    }
    
    if (isset($_POST['publisher_id']) && !empty($_POST['publisher_id'])) {
        // Cập nhật
        $publisher_id = $_POST['publisher_id'];
        if (!empty($logo)) {
            $update_query = "UPDATE publishers SET publisher_name=?, description=?, address=?, phone=?, email=?, website=?, logo=?, status=? WHERE publisher_id=?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssssssssi", $publisher_name, $description, $address, $phone, $email, $website, $logo, $status, $publisher_id);
        } else {
            $update_query = "UPDATE publishers SET publisher_name=?, description=?, address=?, phone=?, email=?, website=?, status=? WHERE publisher_id=?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssssssi", $publisher_name, $description, $address, $phone, $email, $website, $status, $publisher_id);
        }
        $stmt->execute();
        $message = "Cập nhật nhà xuất bản thành công!";
    } else {
        // Thêm mới
        $insert_query = "INSERT INTO publishers (publisher_name, description, address, phone, email, website, logo, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssssssss", $publisher_name, $description, $address, $phone, $email, $website, $logo, $status);
        $stmt->execute();
        $message = "Thêm nhà xuất bản thành công!";
    }
}

// Lấy danh sách nhà xuất bản
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT * FROM publishers WHERE 1=1";
if ($search) {
    $query .= " AND (publisher_name LIKE '%$search%' OR address LIKE '%$search%')";
}
if ($status_filter) {
    $query .= " AND status = '$status_filter'";
}
$query .= " ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Nhà xuất bản</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .publishers-container {
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
        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
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
            max-width: 600px;
            margin: 50px auto;
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
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="publishers-container">
        <div class="page-header">
            <h1><i class="fas fa-building"></i> Quản lý Nhà xuất bản</h1>
            <button class="btn btn-primary" onclick="openModal()">
                <i class="fas fa-plus"></i> Thêm nhà xuất bản
            </button>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên, địa chỉ..." value="<?php echo $search; ?>">
            <select id="statusFilter">
                <option value="">Tất cả trạng thái</option>
                <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>Ngừng hoạt động</option>
            </select>
            <button class="btn btn-primary" onclick="searchPublishers()">
                <i class="fas fa-search"></i> Tìm kiếm
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Tên nhà xuất bản</th>
                        <th>Địa chỉ</th>
                        <th>Liên hệ</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($publisher = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $publisher['publisher_id']; ?></td>
                        <td>
                            <?php if ($publisher['logo']): ?>
                                <img src="../uploads/publishers/<?php echo $publisher['logo']; ?>" class="logo-img">
                            <?php else: ?>
                                <i class="fas fa-building fa-2x"></i>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($publisher['publisher_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($publisher['address']); ?></td>
                        <td>
                            <?php if ($publisher['phone']): ?>
                                <div><i class="fas fa-phone"></i> <?php echo $publisher['phone']; ?></div>
                            <?php endif; ?>
                            <?php if ($publisher['email']): ?>
                                <div><i class="fas fa-envelope"></i> <?php echo $publisher['email']; ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $publisher['status']; ?>">
                                <?php echo $publisher['status'] == 'active' ? 'Hoạt động' : 'Ngừng hoạt động'; ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-success" onclick='editPublisher(<?php echo json_encode($publisher); ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger" onclick="deletePublisher(<?php echo $publisher['publisher_id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="publisherModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Thêm nhà xuất bản</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="publisher_id" id="publisher_id">
                
                <div class="form-group">
                    <label>Tên nhà xuất bản *</label>
                    <input type="text" name="publisher_name" id="publisher_name" required>
                </div>
                
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description" id="description"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Địa chỉ</label>
                    <textarea name="address" id="address"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" id="phone">
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="email">
                </div>
                
                <div class="form-group">
                    <label>Website</label>
                    <input type="text" name="website" id="website">
                </div>
                
                <div class="form-group">
                    <label>Logo</label>
                    <input type="file" name="logo" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" id="status">
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Ngừng hoạt động</option>
                    </select>
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

    <script>
        function openModal() {
            document.getElementById('modalTitle').innerText = 'Thêm nhà xuất bản';
            document.getElementById('publisher_id').value = '';
            document.querySelector('form').reset();
            document.getElementById('publisherModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('publisherModal').style.display = 'none';
        }

        function editPublisher(publisher) {
            document.getElementById('modalTitle').innerText = 'Sửa nhà xuất bản';
            document.getElementById('publisher_id').value = publisher.publisher_id;
            document.getElementById('publisher_name').value = publisher.publisher_name;
            document.getElementById('description').value = publisher.description || '';
            document.getElementById('address').value = publisher.address || '';
            document.getElementById('phone').value = publisher.phone || '';
            document.getElementById('email').value = publisher.email || '';
            document.getElementById('website').value = publisher.website || '';
            document.getElementById('status').value = publisher.status;
            document.getElementById('publisherModal').style.display = 'block';
        }

        function deletePublisher(id) {
            if (confirm('Bạn có chắc chắn muốn xóa nhà xuất bản này?')) {
                window.location.href = '?delete=' + id;
            }
        }

        function searchPublishers() {
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            window.location.href = '?search=' + search + '&status=' + status;
        }

        window.onclick = function(event) {
            const modal = document.getElementById('publisherModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
