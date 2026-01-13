<?php
session_start();
require_once '../config/connect.php';
require_once '../includes/email_helper.php';

/** @var mysqli $conn */

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login_page.php');
    exit();
}

// Xử lý cập nhật trạng thái đơn hàng
if(isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = trim($_POST['status']);
    
    // Lấy trạng thái cũ
    $old_status_sql = "SELECT order_status FROM orders WHERE order_id = ?";
    $old_status_stmt = $conn->prepare($old_status_sql);
    $old_status_stmt->bind_param("i", $order_id);
    $old_status_stmt->execute();
    $old_status_result = $old_status_stmt->get_result();
    $order_data = $old_status_result->fetch_assoc();
    $old_status = $order_data['order_status'];
    
    // Kiểm tra nếu đơn hàng đã hoàn thành, đã hủy, hoặc đã hoàn tiền thì không cho phép thay đổi
    if (in_array($old_status, ['Hoàn thành', 'Đã hủy', 'Đã hoàn tiền'])) {
        $error = 'Không thể thay đổi trạng thái đơn hàng đã khóa (Hoàn thành/Đã hủy/Đã hoàn tiền)!';
    } 
    // Ngăn admin set trạng thái "Hoàn thành" trực tiếp
    elseif ($new_status === 'Hoàn thành') {
        $error = 'Admin không thể đặt trạng thái "Hoàn thành" trực tiếp. Chỉ khách hàng mới có thể xác nhận hoàn thành đơn hàng!';
    } else {
        $message = 'Cập nhật trạng thái thành công!';
        
        // Lấy chi tiết đơn hàng
        $details_sql = "SELECT product_id, quantity FROM order_details WHERE order_id = ?";
        $details_stmt = $conn->prepare($details_sql);
        $details_stmt->bind_param("i", $order_id);
        $details_stmt->execute();
        $details_result = $details_stmt->get_result();
    
        // Chuyển sang trạng thái "Đã hủy" - Hoàn lại nếu đã trừ trước đó
        if ($new_status === 'Đã hủy' && $old_status === 'Hoàn thành') {
            // Nếu đơn đã hoàn thành trước đó, hoàn lại tồn kho
            $restore_stock_sql = "UPDATE products 
                                  SET stock_quantity = stock_quantity + ?, 
                                      sold_quantity = sold_quantity - ? 
                                  WHERE product_id = ?";
            $restore_stock_stmt = $conn->prepare($restore_stock_sql);
            
            $details_stmt->execute();
            $details_result = $details_stmt->get_result();
            
            while ($detail = $details_result->fetch_assoc()) {
                $restore_stock_stmt->bind_param("iis", $detail['quantity'], $detail['quantity'], $detail['product_id']);
                $restore_stock_stmt->execute();
            }
            $message .= ' Đã hoàn lại tồn kho.';
        }
        // Hủy đơn chưa hoàn thành - Không cần hoàn lại
        elseif ($new_status === 'Đã hủy') {
            $message .= ' Đơn hàng chưa giao nên không cần hoàn lại tồn kho.';
        }
        // Hoàn tiền - Hoàn lại tồn kho nếu đơn đã hoàn thành trước đó
        elseif ($new_status === 'Đã hoàn tiền') {
            // Kiểm tra xem đơn hàng có từng được hoàn thành không (đã trừ inventory)
            $check_completed_sql = "SELECT completed_date FROM orders WHERE order_id = ?";
            $check_stmt = $conn->prepare($check_completed_sql);
            $check_stmt->bind_param("i", $order_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $order_info = $check_result->fetch_assoc();
            
            if ($order_info && $order_info['completed_date']) {
                // Đơn đã từng hoàn thành, cần hoàn lại tồn kho
                $restore_stock_sql = "UPDATE products 
                                      SET stock_quantity = stock_quantity + ?, 
                                          sold_quantity = sold_quantity - ? 
                                      WHERE product_id = ?";
                $restore_stock_stmt = $conn->prepare($restore_stock_sql);
                
                $details_stmt->execute();
                $details_result = $details_stmt->get_result();
                
                while ($detail = $details_result->fetch_assoc()) {
                    $restore_stock_stmt->bind_param("iis", $detail['quantity'], $detail['quantity'], $detail['product_id']);
                    $restore_stock_stmt->execute();
                }
                $message .= ' Đã hoàn lại tồn kho.';
            } else {
                $message .= ' Đơn hàng chưa hoàn thành nên không cần hoàn lại tồn kho.';
            }
        }
        
        // Cập nhật trạng thái đơn hàng
        // Nếu set thành "Đã hủy" hoặc "Đã hoàn tiền", tự động xóa yêu cầu trả hàng
        if ($new_status === 'Đã hủy' || $new_status === 'Đã hoàn tiền') {
            $stmt = $conn->prepare("UPDATE orders SET order_status = ?, return_request = 0 WHERE order_id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        }
        $stmt->bind_param("si", $new_status, $order_id);
        
        if($stmt->execute()) {
            // Gửi email thông báo nếu trạng thái là "Đã giao"
            if ($new_status === 'Đã giao') {
                // Lấy thông tin đơn hàng và khách hàng
                $order_info_sql = "SELECT email, full_name, total_amount, created_at FROM orders WHERE order_id = ?";
                $order_info_stmt = $conn->prepare($order_info_sql);
                $order_info_stmt->bind_param("i", $order_id);
                $order_info_stmt->execute();
                $order_info_result = $order_info_stmt->get_result();
                
                if ($order_info = $order_info_result->fetch_assoc()) {
                    // Gửi email thông báo
                    $email_sent = send_order_delivered_email(
                        $order_info['email'],
                        $order_info['full_name'],
                        $order_id,
                        $order_info['total_amount'],
                        $order_info['created_at']
                    );
                    
                    if ($email_sent) {
                        $message .= ' Email thông báo đã được gửi đến khách hàng.';
                    } else {
                        $message .= ' Tuy nhiên không thể gửi email thông báo đến khách hàng.';
                    }
                }
            }
            
            echo "<script>alert('$message'); window.location.href='admin_orders.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi cập nhật: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}

// Xử lý xóa đơn hàng
if(isset($_POST['delete_order'])) {
    $order_id = intval($_POST['order_id']);
    
    // Lấy trạng thái đơn hàng
    $status_sql = "SELECT order_status FROM orders WHERE order_id = ?";
    $status_stmt = $conn->prepare($status_sql);
    $status_stmt->bind_param("i", $order_id);
    $status_stmt->execute();
    $status_result = $status_stmt->get_result();
    $order_status = $status_result->fetch_assoc()['order_status'];
    
    // Nếu đơn đã hoàn thành, hoàn lại tồn kho
    if ($order_status === 'Hoàn thành') {
        $details_sql = "SELECT product_id, quantity FROM order_details WHERE order_id = ?";
        $details_stmt = $conn->prepare($details_sql);
        $details_stmt->bind_param("i", $order_id);
        $details_stmt->execute();
        $details_result = $details_stmt->get_result();
        
        $restore_stock_sql = "UPDATE products 
                              SET stock_quantity = stock_quantity + ?, 
                                  sold_quantity = sold_quantity - ? 
                              WHERE product_id = ?";
        $restore_stock_stmt = $conn->prepare($restore_stock_sql);
        
        while ($detail = $details_result->fetch_assoc()) {
            $restore_stock_stmt->bind_param("iis", $detail['quantity'], $detail['quantity'], $detail['product_id']);
            $restore_stock_stmt->execute();
        }
    }
    
    // Xóa chi tiết đơn hàng
    $delete_details = $conn->prepare("DELETE FROM order_details WHERE order_id = ?");
    $delete_details->bind_param("i", $order_id);
    $delete_details->execute();
    
    // Xóa đơn hàng
    $delete_order = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $delete_order->bind_param("i", $order_id);
    
    if($delete_order->execute()) {
        $message = 'Xóa đơn hàng thành công!';
        if ($order_status === 'Hoàn thành') {
            $message .= ' Đã hoàn lại tồn kho.';
        }
        echo "<script>alert('$message'); window.location.href='admin_orders.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa đơn hàng: " . $conn->error . "');</script>";
    }
    $delete_order->close();
}

// Lấy danh sách đơn hàng
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">    <link rel="stylesheet" href="../css/admin-mobile.css">    <link rel="stylesheet" href="../css/admin_orders.css">
    <link rel="stylesheet" href="../css/fontawesome/all.min.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <?php if(isset($message)): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <main>       
    <div class="admin-orders">
        <h1>Quản Lý Đơn Hàng</h1>

        <table class="order-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tạm tính</th>
                    <th>Giảm giá</th>
                    <th>Tổng tiền</th>
                    <th>Hình thức TT</th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $order['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                        <td>
                            <?php 
                            $discount = isset($order['discount_amount']) ? floatval($order['discount_amount']) : 0;
                            $subtotal = $order['total_amount'] + $discount;
                            echo number_format($subtotal, 0, ',', '.'); 
                            ?> VNĐ
                        </td>
                        <td>
                            <?php if ($discount > 0): ?>
                                <span style="color:#28a745;font-weight:600;">-<?php echo number_format($discount, 0, ',', '.'); ?> VNĐ</span>
                            <?php else: ?>
                                <span style="color:#999;">0 VNĐ</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</strong></td>
                        <td>
                            <?php if ($order['payment_method'] === 'bank_transfer'): ?>
                                <span style="background:#dc3545;color:white;padding:4px 8px;border-radius:4px;font-size:12px;font-weight:600;">
                                    <i class="fas fa-university"></i> Chuyển khoản
                                </span>
                            <?php else: ?>
                                <span style="background:#28a745;color:white;padding:4px 8px;border-radius:4px;font-size:12px;font-weight:600;">
                                    <i class="fas fa-money-bill-wave"></i> COD
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <?php 
                            $is_locked = in_array($order['order_status'], ['Hoàn thành', 'Đã hủy', 'Đã hoàn tiền']);
                            ?>
                            <form method="POST" action="" style="display: flex; align-items: center; gap: 5px;">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <?php if ($is_locked): ?>
                                    <i class="fas fa-lock" style="color:#dc3545;margin-right:5px;" title="Đơn hàng đã khóa"></i>
                                <?php endif; ?>
                                <select name="status" class="status-select" <?php echo $is_locked ? 'disabled' : ''; ?> style="<?php echo $is_locked ? 'opacity:0.6;cursor:not-allowed;' : ''; ?>">
                                    <option value="Chờ thanh toán" <?php if($order['order_status'] == 'Chờ thanh toán') echo 'selected'; ?>>Chờ thanh toán</option>
                                    <option value="Chờ xác nhận" <?php if($order['order_status'] == 'Chờ xác nhận') echo 'selected'; ?>>Chờ xác nhận</option>
                                    <option value="Đã xác nhận" <?php if($order['order_status'] == 'Đã xác nhận') echo 'selected'; ?>>Đã xác nhận</option>
                                    <option value="Đang giao" <?php if($order['order_status'] == 'Đang giao') echo 'selected'; ?>>Đang giao</option>
                                    <option value="Đã giao" <?php if($order['order_status'] == 'Đã giao') echo 'selected'; ?>>Đã giao</option>
                                    <?php if($order['order_status'] == 'Yêu cầu trả hàng'): ?>
                                        <option value="Yêu cầu trả hàng" selected>Yêu cầu trả hàng</option>
                                    <?php endif; ?>
                                    <?php if($order['order_status'] == 'Đang trả hàng' || $order['order_status'] == 'Yêu cầu trả hàng'): ?>
                                        <option value="Đang trả hàng" <?php if($order['order_status'] == 'Đang trả hàng') echo 'selected'; ?>>Đang trả hàng</option>
                                    <?php endif; ?>
                                    <?php if($order['order_status'] == 'Đang trả hàng'): ?>
                                        <option value="Đã hoàn tiền" <?php if($order['order_status'] == 'Đã hoàn tiền') echo 'selected'; ?>>Đã hoàn tiền</option>
                                    <?php endif; ?>
                                    <?php if($order['order_status'] == 'Đã hoàn tiền'): ?>
                                        <option value="Đã hoàn tiền" selected>Đã hoàn tiền</option>
                                    <?php endif; ?>
                                    <?php if($order['order_status'] == 'Hoàn thành'): ?>
                                        <option value="Hoàn thành" selected>Hoàn thành</option>
                                    <?php endif; ?>
                                    <option value="Đã hủy" <?php if($order['order_status'] == 'Đã hủy') echo 'selected'; ?>>Đã hủy</option>
                                </select>
                                <button type="submit" class="btn-update-status" <?php echo $is_locked ? 'disabled' : ''; ?> style="<?php echo $is_locked ? 'opacity:0.6;cursor:not-allowed;' : ''; ?>">
                                    <i class="fas <?php echo $is_locked ? 'fa-lock' : 'fa-save'; ?>"></i> <?php echo $is_locked ? 'Đã khóa' : 'Lưu'; ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <span class="view-details" onclick="toggleDetails(<?php echo $order['order_id']; ?>)">
                                <i class="fas fa-eye"></i> Chi tiết
                            </span>
                            <form method="POST" action="" style="display:inline-block;margin-left:10px;" onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này? Hành động này không thể hoàn tác!');">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <input type="hidden" name="delete_order" value="1">
                                <button type="submit" class="btn-delete-order" style="background:#dc3545;color:white;border:none;padding:6px 12px;border-radius:4px;cursor:pointer;font-size:12px;">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div id="details-<?php echo $order['order_id']; ?>" class="order-details">
                                <?php
                                $detail_sql = "SELECT od.*, p.product_name 
                                             FROM order_details od 
                                             JOIN products p ON od.product_id = p.product_id 
                                             WHERE od.order_id = ?";
                                $detail_stmt = $conn->prepare($detail_sql);
                                $detail_stmt->bind_param("i", $order['order_id']);
                                $detail_stmt->execute();
                                $details = $detail_stmt->get_result();
                                ?>
                                <h4>Chi tiết đơn hàng:</h4>
                                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                                <?php if ($order['notes']): ?>
                                    <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['notes']); ?></p>
                                <?php endif; ?>
                                <?php if ($order['order_status'] === 'Yêu cầu trả hàng' && $order['return_reason']): ?>
                                    <div style="background:#fff3cd;border-left:4px solid #ffc107;padding:15px;margin:10px 0;border-radius:8px;">
                                        <p style="margin:0;color:#856404;font-weight:600;">
                                            <i class="fas fa-undo"></i> Lý do trả hàng:
                                        </p>
                                        <p style="margin:8px 0 0 0;color:#856404;">
                                            <?php echo htmlspecialchars($order['return_reason']); ?>
                                        </p>
                                        <?php if ($order['return_request_date']): ?>
                                            <p style="margin:8px 0 0 0;color:#856404;font-size:13px;">
                                                <i class="fas fa-clock"></i> Ngày yêu cầu: <?php echo date('d/m/Y H:i', strtotime($order['return_request_date'])); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <p><strong>Hình thức thanh toán:</strong> 
                                    <?php echo $order['payment_method'] === 'bank_transfer' ? 'Chuyển khoản' : 'COD'; ?>
                                </p>
                                <?php if ($order['payment_method'] === 'bank_transfer' && !empty($order['payment_proof'])): ?>
                                    <p><strong>Chứng từ thanh toán:</strong></p>
                                    <img src="<?php echo htmlspecialchars($order['payment_proof']); ?>" 
                                         style="max-width: 300px; border-radius: 8px; margin-top: 10px; cursor: pointer;"
                                         onclick="window.open('<?php echo htmlspecialchars($order['payment_proof']); ?>', '_blank')">
                                <?php endif; ?>
                                <table style="width: 100%; margin-top: 10px;">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Giá</th>
                                    </tr>
                                    <?php while ($detail = $details->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($detail['product_name']); ?></td>
                                            <td><?php echo $detail['quantity']; ?></td>
                                            <td><?php echo number_format($detail['price'], 0, ',', '.'); ?> VNĐ</td>
                                        </tr>
                                    <?php endwhile; ?>
                                </table>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
    function toggleDetails(orderId) {
        const detailsDiv = document.getElementById(`details-${orderId}`);
        if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
            detailsDiv.style.display = 'block';
        } else {
            detailsDiv.style.display = 'none';
        }
    }

    document.querySelectorAll('select[name="new_status"]').forEach(select => {
        select.addEventListener('change', function() {
            if(confirm('Bạn có chắc muốn cập nhật trạng thái?')) {
                this.closest('form').submit();
            }
        });
    });
    </script>
    </main>

    <?php include 'admin_footer.php'; ?>
</body>
</html>

<?php $conn->close(); ?>
