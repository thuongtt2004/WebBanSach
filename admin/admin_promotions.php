<?php
session_start();
require_once '../config/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_page.php');
    exit();
}

// Xử lý xóa khuyến mãi
if (isset($_POST['delete_promotion'])) {
    $promotion_id = $_POST['promotion_id'];
    $delete_query = $conn->prepare("DELETE FROM promotions WHERE promotion_id = ?");
    $delete_query->bind_param("i", $promotion_id);
    
    if ($delete_query->execute()) {
        echo "<script>alert('Xóa khuyến mãi thành công!'); window.location.href='admin_promotions.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa khuyến mãi!');</script>";
    }
    $delete_query->close();
}

// Xử lý thêm/sửa khuyến mãi
if (isset($_POST['save_promotion'])) {
    $promotion_id = $_POST['promotion_id'] ?? null;
    $promotion_code = trim($_POST['promotion_code']);
    $promotion_name = trim($_POST['promotion_name']);
    $promotion_type = $_POST['promotion_type'];
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    $min_order_amount = floatval($_POST['min_order_amount'] ?? 0);
    $max_discount = !empty($_POST['max_discount']) ? floatval($_POST['max_discount']) : null;
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : null;
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'];
    
    if ($promotion_id) {
        // Cập nhật
        $stmt = $conn->prepare("UPDATE promotions SET promotion_code=?, promotion_name=?, promotion_type=?, discount_type=?, discount_value=?, min_order_amount=?, max_discount=?, start_date=?, end_date=?, usage_limit=?, description=?, status=? WHERE promotion_id=?");
        $stmt->bind_param("ssssddssssssi", $promotion_code, $promotion_name, $promotion_type, $discount_type, $discount_value, $min_order_amount, $max_discount, $start_date, $end_date, $usage_limit, $description, $status, $promotion_id);
    } else {
        // Thêm mới
        $stmt = $conn->prepare("INSERT INTO promotions (promotion_code, promotion_name, promotion_type, discount_type, discount_value, min_order_amount, max_discount, start_date, end_date, usage_limit, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssddssssss", $promotion_code, $promotion_name, $promotion_type, $discount_type, $discount_value, $min_order_amount, $max_discount, $start_date, $end_date, $usage_limit, $description, $status);
    }
    
    if ($stmt->execute()) {
        $promo_id = $promotion_id ?: $conn->insert_id;
        
        // Xử lý sản phẩm áp dụng (nếu là khuyến mãi theo sản phẩm)
        if ($promotion_type == 'product' && !empty($_POST['product_ids'])) {
            // Xóa liên kết cũ
            $conn->query("DELETE FROM promotion_products WHERE promotion_id = $promo_id");
            
            // Thêm liên kết mới
            $insert_product = $conn->prepare("INSERT INTO promotion_products (promotion_id, product_id) VALUES (?, ?)");
            foreach ($_POST['product_ids'] as $product_id) {
                $insert_product->bind_param("is", $promo_id, $product_id);
                $insert_product->execute();
            }
            $insert_product->close();
        }
        
        // Xử lý danh mục áp dụng (nếu là khuyến mãi theo danh mục)
        if ($promotion_type == 'category' && !empty($_POST['category_ids'])) {
            // Xóa liên kết cũ
            $conn->query("DELETE FROM promotion_categories WHERE promotion_id = $promo_id");
            
            // Thêm liên kết mới
            $insert_category = $conn->prepare("INSERT INTO promotion_categories (promotion_id, category_id) VALUES (?, ?)");
            foreach ($_POST['category_ids'] as $category_id) {
                $insert_category->bind_param("ii", $promo_id, $category_id);
                $insert_category->execute();
            }
            $insert_category->close();
        }
        
        echo "<script>alert('Lưu khuyến mãi thành công!'); window.location.href='admin_promotions.php';</script>";
    } else {
        echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

// Lấy danh sách khuyến mãi
$filter = $_GET['filter'] ?? 'all';
$where = "WHERE 1=1";
if ($filter == 'active') {
    $where .= " AND status='active' AND NOW() BETWEEN start_date AND end_date";
} elseif ($filter == 'expired') {
    $where .= " AND (status='expired' OR end_date < NOW())";
} elseif ($filter != 'all') {
    $where .= " AND promotion_type='$filter'";
}

$promotions_query = "SELECT * FROM promotions $where ORDER BY created_at DESC";
$promotions_result = $conn->query($promotions_query);

// Lấy danh sách sản phẩm cho dropdown
$products_query = "SELECT product_id, product_name FROM products ORDER BY product_name";
$products_result = $conn->query($products_query);

// Lấy danh sách danh mục (giả sử có bảng categories)
$categories_result = null;
if ($conn->query("SHOW TABLES LIKE 'categories'")->num_rows > 0) {
    $categories_query = "SELECT category_id, category_name FROM categories ORDER BY category_name";
    $categories_result = $conn->query($categories_query);
}

include 'admin_header.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/admin-mobile.css">
    <link rel="stylesheet" href="../css/mobile-375px.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
.promotions-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    gap: 15px;
    flex-wrap: wrap;
}

.page-header h1 {
    color: #333;
    font-size: 28px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.page-header h1 i {
    font-size: 26px;
}

.btn-add {
    background: linear-gradient(135deg, #2dce89 0%, #24a46d 100%);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(45, 206, 137, 0.3);
    transition: all 0.3s;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(45, 206, 137, 0.4);
}

.btn-add i {
    font-size: 18px;
}

.filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid #ddd;
    padding-bottom: 10px;
}

.filter-tab {
    padding: 10px 20px;
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 5px 5px 0 0;
    cursor: pointer;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
}

.filter-tab:hover {
    background-color: #e9ecef;
}

.filter-tab.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.table-wrapper {
    width: 100%;
    background: white;
    border-radius: 12px;
    border: 2px solid #e9ecef;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.promotions-table {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.promotions-table::-webkit-scrollbar {
    height: 8px;
}

.promotions-table::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.promotions-table::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.promotions-table::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.promotions-table table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.promotions-table th {
    background-color: #343a40;
    color: white;
    padding: 15px;
    text-align: left;
    font-weight: 600;
}

.promotions-table td {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
}

.promotions-table tr:hover {
    background-color: #f8f9fa;
}

.badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-product { background-color: #007bff; color: white; }
.badge-category { background-color: #6f42c1; color: white; }
.badge-flash_sale { background-color: #fd7e14; color: white; }
.badge-coupon { background-color: #20c997; color: white; }
.badge-minimum_order { background-color: #17a2b8; color: white; }

.badge-active { background-color: #28a745; color: white; }
.badge-inactive { background-color: #6c757d; color: white; }
.badge-expired { background-color: #dc3545; color: white; }

.badge-percentage { background-color: #ffc107; color: #333; }
.badge-fixed_amount { background-color: #28a745; color: white; }

.action-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.btn-edit, .btn-delete {
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.btn-edit {
    background: linear-gradient(135deg, #11cdef 0%, #0da5c0 100%);
    color: white;
    box-shadow: 0 3px 10px rgba(17, 205, 239, 0.3);
}

.btn-edit:hover {
    background: linear-gradient(135deg, #0da5c0 0%, #11cdef 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(17, 205, 239, 0.4);
}

.btn-delete {
    background: linear-gradient(135deg, #f5365c 0%, #ec0c38 100%);
    color: white;
    box-shadow: 0 3px 10px rgba(245, 54, 92, 0.3);
}

.btn-delete:hover {
    background: linear-gradient(135deg, #ec0c38 0%, #f5365c 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(245, 54, 92, 0.4);
}

.btn-edit i, .btn-delete i {
    font-size: 14px;
}

.no-promotions {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.no-promotions i {
    font-size: 64px;
    color: #dee2e6;
    margin-bottom: 20px;
}

.no-promotions p {
    font-size: 18px;
    margin: 0;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
    overflow-y: auto;
}

.modal-content {
    background-color: white;
    margin: 50px auto;
    padding: 30px;
    width: 90%;
    max-width: 800px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 15px;
}

.modal-header h2 {
    margin: 0;
    color: #333;
}

.close {
    font-size: 32px;
    font-weight: bold;
    color: #aaa;
    cursor: pointer;
    line-height: 1;
}

.close:hover {
    color: #000;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
}

.btn-submit {
    background-color: #007bff;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.btn-submit:hover {
    background-color: #0056b3;
}

.btn-cancel {
    background-color: #6c757d;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.btn-cancel:hover {
    background-color: #545b62;
}

.no-promotions {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-size: 18px;
}

.no-promotions i {
    font-size: 64px;
    margin-bottom: 20px;
    display: block;
    color: #dee2e6;
}

#productSelect, #categorySelect {
    height: 200px;
}

.conditional-field {
    display: none;
}

/* Responsive Mobile Styles */
@media (max-width: 992px) {
    .promotions-container {
        padding: 15px;
    }
    
    .filter-tabs {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    
    .filter-tabs::-webkit-scrollbar {
        display: none;
    }
    
    .filter-tab {
        white-space: nowrap;
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .page-header h1 {
        font-size: 20px;
        justify-content: center;
    }
    
    .page-header h1 i {
        font-size: 22px;
    }
    
    .btn-add {
        width: 100%;
        justify-content: center;
        padding: 14px 20px;
    }
    
    .filter-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 8px;
    }
    
    .filter-tab {
        font-size: 13px;
        padding: 8px 15px;
    }
    
    .table-wrapper {
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .promotions-table {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .promotions-table table {
        min-width: 900px;
        font-size: 13px;
    }
    
    .promotions-table th,
    .promotions-table td {
        padding: 10px 8px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 5px;
    }
    
    .btn-edit, .btn-delete {
        width: 100%;
        justify-content: center;
        padding: 8px 12px;
    }
    
    /* Ẩn text, chỉ hiện icon trên mobile */
    .btn-text {
        display: none;
    }
    
    .btn-edit i, .btn-delete i {
        margin: 0;
        font-size: 16px;
    }
    
    .modal-content {
        width: 95%;
        margin: 20px auto;
        padding: 20px 15px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

@media (max-width: 480px) {
    .promotions-container {
        padding: 10px;
    }
    
    .page-header h1 {
        font-size: 18px;
    }
    
    .page-header h1 i {
        font-size: 18px;
    }
    
    .btn-add {
        font-size: 14px;
        padding: 12px 16px;
    }
    
    .btn-add .btn-text {
        display: none;
    }
    
    .btn-add i {
        font-size: 16px;
        margin: 0;
    }
    
    .badge {
        font-size: 10px;
        padding: 4px 8px;
    }
}
</style>

<div class="promotions-container">
    <div class="page-header">
        <h1><i class="fas fa-tags"></i> Quản lý khuyến mãi</h1>
        <button class="btn-add" onclick="openAddModal()">
            <i class="fas fa-plus"></i> <span class="btn-text">Thêm khuyến mãi</span>
        </button>
    </div>

    <div class="filter-tabs">
        <a href="?filter=all" class="filter-tab <?= $filter == 'all' ? 'active' : '' ?>">
            <i class="fas fa-list"></i> Tất cả
        </a>
        <a href="?filter=active" class="filter-tab <?= $filter == 'active' ? 'active' : '' ?>">
            <i class="fas fa-check-circle"></i> Đang hoạt động
        </a>
        <a href="?filter=expired" class="filter-tab <?= $filter == 'expired' ? 'active' : '' ?>">
            <i class="fas fa-times-circle"></i> Đã hết hạn
        </a>
        <a href="?filter=product" class="filter-tab <?= $filter == 'product' ? 'active' : '' ?>">
            <i class="fas fa-box"></i> Theo sản phẩm
        </a>
        <a href="?filter=category" class="filter-tab <?= $filter == 'category' ? 'active' : '' ?>">
            <i class="fas fa-folder"></i> Theo danh mục
        </a>
        <a href="?filter=flash_sale" class="filter-tab <?= $filter == 'flash_sale' ? 'active' : '' ?>">
            <i class="fas fa-bolt"></i> Flash Sale
        </a>
        <a href="?filter=coupon" class="filter-tab <?= $filter == 'coupon' ? 'active' : '' ?>">
            <i class="fas fa-ticket-alt"></i> Mã giảm giá
        </a>
    </div>

    <div class="table-wrapper">
        <div class="promotions-table">
            <?php if ($promotions_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Mã KM</th>
                        <th>Tên khuyến mãi</th>
                        <th>Loại</th>
                        <th>Giảm giá</th>
                        <th>Thời gian</th>
                        <th>Đã dùng/Giới hạn</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
            </thead>
            <tbody>
                <?php while ($promo = $promotions_result->fetch_assoc()): 
                    $now = date('Y-m-d H:i:s');
                    $actual_status = $promo['status'];
                    if ($promo['end_date'] < $now) {
                        $actual_status = 'expired';
                    }
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($promo['promotion_code']) ?></strong></td>
                    <td><?= htmlspecialchars($promo['promotion_name']) ?></td>
                    <td><span class="badge badge-<?= $promo['promotion_type'] ?>"><?= $promo['promotion_type'] ?></span></td>
                    <td>
                        <span class="badge badge-<?= $promo['discount_type'] ?>">
                            <?= $promo['discount_type'] == 'percentage' ? $promo['discount_value'] . '%' : number_format($promo['discount_value']) . 'đ' ?>
                        </span>
                        <?php if ($promo['min_order_amount'] > 0): ?>
                            <br><small style="color: #6c757d;">Đơn tối thiểu: <?= number_format($promo['min_order_amount']) ?>đ</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <small>
                            <?= date('d/m/Y H:i', strtotime($promo['start_date'])) ?><br>
                            → <?= date('d/m/Y H:i', strtotime($promo['end_date'])) ?>
                        </small>
                    </td>
                    <td><?= $promo['used_count'] ?> / <?= $promo['usage_limit'] ?: '∞' ?></td>
                    <td><span class="badge badge-<?= $actual_status ?>"><?= $actual_status ?></span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-edit" onclick='editPromotion(<?= json_encode($promo) ?>)'>
                                <i class="fas fa-edit"></i> <span class="btn-text">Sửa</span>
                            </button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Xác nhận xóa khuyến mãi này?')">
                                <input type="hidden" name="promotion_id" value="<?= $promo['promotion_id'] ?>">
                                <button type="submit" name="delete_promotion" class="btn-delete">
                                    <i class="fas fa-trash"></i> <span class="btn-text">Xóa</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-promotions">
            <i class="fas fa-tags"></i>
            <p>Chưa có khuyến mãi nào. Nhấn "Thêm khuyến mãi" để bắt đầu!</p>
        </div>
        <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa Khuyến Mãi -->
<div id="promotionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle"><i class="fas fa-tag"></i> Thêm khuyến mãi</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        
        <form method="POST" id="promotionForm">
            <input type="hidden" name="promotion_id" id="promotion_id">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Mã khuyến mãi <span style="color:red;">*</span></label>
                    <input type="text" name="promotion_code" id="promotion_code" required placeholder="VD: SUMMER2025">
                </div>
                
                <div class="form-group">
                    <label>Loại khuyến mãi <span style="color:red;">*</span></label>
                    <select name="promotion_type" id="promotion_type" required onchange="toggleConditionalFields()">
                        <option value="">-- Chọn loại --</option>
                        <option value="product">Sản phẩm cụ thể</option>
                        <option value="category">Danh mục</option>
                        <option value="flash_sale">Flash Sale (toàn cửa hàng)</option>
                        <option value="coupon">Mã giảm giá</option>
                        <option value="minimum_order">Giảm theo đơn hàng</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Tên khuyến mãi <span style="color:red;">*</span></label>
                <input type="text" name="promotion_name" id="promotion_name" required placeholder="VD: Giảm giá mùa hè">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Kiểu giảm giá <span style="color:red;">*</span></label>
                    <select name="discount_type" id="discount_type" required>
                        <option value="percentage">Phần trăm (%)</option>
                        <option value="fixed_amount">Số tiền cố định (đ)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Giá trị giảm <span style="color:red;">*</span></label>
                    <input type="number" name="discount_value" id="discount_value" required min="0" step="0.01">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Đơn hàng tối thiểu (đ)</label>
                    <input type="number" name="min_order_amount" id="min_order_amount" min="0" step="1000" value="0">
                </div>
                
                <div class="form-group">
                    <label>Giảm tối đa (đ)</label>
                    <input type="number" name="max_discount" id="max_discount" min="0" step="1000" placeholder="Để trống nếu không giới hạn">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Ngày bắt đầu <span style="color:red;">*</span></label>
                    <input type="datetime-local" name="start_date" id="start_date" required>
                </div>
                
                <div class="form-group">
                    <label>Ngày kết thúc <span style="color:red;">*</span></label>
                    <input type="datetime-local" name="end_date" id="end_date" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Giới hạn số lần dùng</label>
                    <input type="number" name="usage_limit" id="usage_limit" min="1" placeholder="Để trống nếu không giới hạn">
                </div>
                
                <div class="form-group">
                    <label>Trạng thái <span style="color:red;">*</span></label>
                    <select name="status" id="status" required>
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Tạm dừng</option>
                    </select>
                </div>
            </div>
            
            <!-- Chọn sản phẩm (hiện khi chọn loại "product") -->
            <div class="form-group conditional-field" id="productField">
                <label>Chọn sản phẩm áp dụng <span style="color:red;">*</span></label>
                <select name="product_ids[]" id="productSelect" multiple>
                    <?php 
                    $products_result->data_seek(0);
                    while ($product = $products_result->fetch_assoc()): 
                    ?>
                        <option value="<?= $product['product_id'] ?>"><?= htmlspecialchars($product['product_name']) ?></option>
                    <?php endwhile; ?>
                </select>
                <small style="color: #6c757d;">Giữ Ctrl để chọn nhiều sản phẩm</small>
            </div>
            
            <!-- Chọn danh mục (hiện khi chọn loại "category") -->
            <?php if ($categories_result): ?>
            <div class="form-group conditional-field" id="categoryField">
                <label>Chọn danh mục áp dụng <span style="color:red;">*</span></label>
                <select name="category_ids[]" id="categorySelect" multiple>
                    <?php 
                    $categories_result->data_seek(0);
                    while ($category = $categories_result->fetch_assoc()): 
                    ?>
                        <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                    <?php endwhile; ?>
                </select>
                <small style="color: #6c757d;">Giữ Ctrl để chọn nhiều danh mục</small>
            </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" id="description" placeholder="Mô tả chi tiết về khuyến mãi..."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button type="submit" name="save_promotion" class="btn-submit">
                    <i class="fas fa-save"></i> Lưu khuyến mãi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('promotionModal').style.display = 'block';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> Thêm khuyến mãi';
    document.getElementById('promotionForm').reset();
    document.getElementById('promotion_id').value = '';
}

function closeModal() {
    document.getElementById('promotionModal').style.display = 'none';
}

function editPromotion(promo) {
    document.getElementById('promotionModal').style.display = 'block';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Sửa khuyến mãi';
    
    document.getElementById('promotion_id').value = promo.promotion_id;
    document.getElementById('promotion_code').value = promo.promotion_code;
    document.getElementById('promotion_name').value = promo.promotion_name;
    document.getElementById('promotion_type').value = promo.promotion_type;
    document.getElementById('discount_type').value = promo.discount_type;
    document.getElementById('discount_value').value = promo.discount_value;
    document.getElementById('min_order_amount').value = promo.min_order_amount;
    document.getElementById('max_discount').value = promo.max_discount || '';
    document.getElementById('start_date').value = promo.start_date.replace(' ', 'T');
    document.getElementById('end_date').value = promo.end_date.replace(' ', 'T');
    document.getElementById('usage_limit').value = promo.usage_limit || '';
    document.getElementById('description').value = promo.description || '';
    document.getElementById('status').value = promo.status;
    
    toggleConditionalFields();
    
    // Load selected products/categories
    if (promo.promotion_type === 'product') {
        fetch('get_promotion_products.php?promotion_id=' + promo.promotion_id)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('productSelect');
                for (let option of select.options) {
                    option.selected = data.includes(option.value);
                }
            });
    } else if (promo.promotion_type === 'category') {
        fetch('get_promotion_categories.php?promotion_id=' + promo.promotion_id)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('categorySelect');
                for (let option of select.options) {
                    option.selected = data.includes(parseInt(option.value));
                }
            });
    }
}

function toggleConditionalFields() {
    const type = document.getElementById('promotion_type').value;
    
    document.getElementById('productField').style.display = type === 'product' ? 'block' : 'none';
    document.getElementById('categoryField').style.display = type === 'category' ? 'block' : 'none';
}

// Đóng modal khi click bên ngoài
window.onclick = function(event) {
    const modal = document.getElementById('promotionModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>
</head>
<body>

<?php
include 'admin_footer.php';
$conn->close();
?>
</body>
</html>
