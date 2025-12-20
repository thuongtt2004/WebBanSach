<?php
require_once 'config/connect.php';
require_once 'session_init.php';
/** @var mysqli $conn */

if (!isset($_SESSION['user_id'])) {
    header('Location: login_page.php');
    exit();
}

// Tự động hủy đơn hàng quá hạn 24h
require_once 'auto_cancel_expired_orders.php';

$user_id = $_SESSION['user_id'];

// Lấy danh sách đơn hàng của user
$order_sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$orders = $order_stmt->get_result();

// Số ngày cho phép trả hàng
$return_days_limit = 1;

require_once 'header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theo dõi đơn hàng - TTHUONG Store</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/theodoidonhang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="order-tracking">
        <h2>Theo dõi đơn hàng</h2>

        <?php if ($orders->num_rows > 0): ?>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="order-card" id="order-<?php echo $order['order_id']; ?>">
                    <div class="order-header">
                        <div>
                            <h3>Đơn hàng #<?php echo $order['order_id']; ?></h3>
                            <p>Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                            <span class="order-status status-<?php echo strtolower($order['order_status']); ?>">
                                <?php echo $order['order_status']; ?>
                            </span>
                            <?php if ($order['payment_method'] === 'bank_transfer' && $order['order_status'] === 'Chờ thanh toán'): ?>
                                <span style="background:#dc3545;color:white;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                                    <i class="fas fa-exclamation-circle"></i> Chưa thanh toán
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="order-details">
                        <p><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                        <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                        <p><strong>Hình thức thanh toán:</strong> 
                            <?php if ($order['payment_method'] === 'bank_transfer'): ?>
                                <span style="color:#dc3545;font-weight:600;"><i class="fas fa-university"></i> Chuyển khoản</span>
                            <?php else: ?>
                                <span style="color:#28a745;font-weight:600;"><i class="fas fa-money-bill-wave"></i> COD</span>
                            <?php endif; ?>
                        </p>
                        
                        <?php if ($order['payment_method'] === 'bank_transfer' && $order['order_status'] === 'Chờ thanh toán'): 
                            $created_time = strtotime($order['order_date']);
                            $hours_passed = floor((time() - $created_time) / 3600);
                            $hours_left = 24 - $hours_passed;
                        ?>
                            <div style="background:#fff3cd;border-left:4px solid #ffc107;padding:15px;margin:15px 0;border-radius:8px;">
                                <p style="margin:0;color:#856404;font-weight:600;">
                                    <i class="fas fa-clock"></i> 
                                    <?php if ($hours_left > 0): ?>
                                        Còn <strong><?php echo $hours_left; ?> giờ</strong> để hoàn tất thanh toán
                                    <?php else: ?>
                                        Đơn hàng sắp hết hạn thanh toán!
                                    <?php endif; ?>
                                </p>
                                <p style="margin:5px 0 0 0;color:#856404;font-size:13px;">
                                    Vui lòng chuyển khoản theo thông tin đã gửi sau khi đặt hàng
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($order['notes']): ?>
                            <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['notes']); ?></p>
                        <?php endif; ?>

                        <div class="product-list">
                            <h4>Sản phẩm đã đặt:</h4>
                            <?php
                            $detail_sql = "SELECT od.*, p.product_name, p.image_url 
                                         FROM order_details od 
                                         JOIN products p ON od.product_id = p.product_id 
                                         WHERE od.order_id = ?";
                            $detail_stmt = $conn->prepare($detail_sql);
                            if ($detail_stmt) {
                                $detail_stmt->bind_param("i", $order['order_id']);
                                $detail_stmt->execute();
                                $details = $detail_stmt->get_result();
                                
                                if ($details && $details->num_rows > 0) {
                                    while ($detail = $details->fetch_assoc()):
                            ?>
                                <div class="product-item">
                                    <img src="<?php echo htmlspecialchars($detail['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($detail['product_name']); ?>">
                                    <div>
                                        <h5><?php echo htmlspecialchars($detail['product_name']); ?></h5>
                                        <p>Số lượng: <?php echo $detail['quantity']; ?></p>
                                        <p>Giá: <?php echo number_format($detail['price'], 0, ',', '.'); ?> VNĐ</p>
                                    </div>
                                </div>
                            <?php 
                                    endwhile;
                                } else {
                                    echo '<p style="color:#999;padding:20px;text-align:center;">Không có sản phẩm trong đơn hàng này</p>';
                                }
                            } else {
                                echo '<p style="color:red;">Lỗi truy vấn chi tiết đơn hàng</p>';
                            }
                            ?>
                        </div>

                        <div class="total-amount">
                            Tổng tiền: <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ
                        </div>
                    </div>

                    <?php if ($order['order_status'] == 'Đã giao'): ?>
                        <!-- Trạng thái Đã giao - Hiện cả 2 nút: xác nhận và trả hàng -->
                        <div class="order-actions" style="background:#f8f9fa;padding:20px;margin-top:15px;border-radius:8px;">
                            <h4 style="margin-bottom:15px;color:#333;"><i class="fas fa-tasks"></i> Xác nhận đơn hàng</h4>
                            <div style="background:#d1ecf1;border-left:4px solid #17a2b8;padding:15px;border-radius:8px;margin-bottom:15px;">
                                <p style="margin:0;color:#0c5460;font-weight:600;">
                                    <i class="fas fa-info-circle"></i> Đơn hàng đã được giao đến bạn
                                </p>
                                <p style="margin:8px 0 0 0;color:#0c5460;font-size:14px;">
                                    Vui lòng xác nhận đã nhận hàng hoặc yêu cầu trả hàng nếu có vấn đề
                                </p>
                            </div>
                            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                                <button onclick="confirmOrderCompletion(<?php echo $order['order_id']; ?>)" 
                                        class="btn-confirm-completion" 
                                        style="flex:1;min-width:200px;padding:15px 20px;background:#28a745;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:16px;transition:all 0.3s;">
                                    <i class="fas fa-check-circle"></i> Xác nhận đã nhận hàng
                                </button>
                                <button onclick="openReturnModal(<?php echo $order['order_id']; ?>)" 
                                        class="btn-request-return" 
                                        style="flex:1;min-width:200px;padding:15px 20px;background:#dc3545;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:16px;transition:all 0.3s;">
                                    <i class="fas fa-undo"></i> Yêu cầu trả hàng
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($order['order_status'] == 'Hoàn thành'): 
                        $completed_date = (isset($order['completed_date']) && $order['completed_date']) ? strtotime($order['completed_date']) : time();
                        $days_passed = floor((time() - $completed_date) / 86400);
                        $days_left = $return_days_limit - $days_passed;
                        $customer_confirmed = $order['customer_confirmed'] ?? 0;
                        $return_request = $order['return_request'] ?? 0;
                        $return_status = $order['return_status'] ?? '';
                    ?>
                        <div class="order-actions" style="background:#f8f9fa;padding:20px;margin-top:15px;border-radius:8px;">
                            <h4 style="margin-bottom:15px;color:#333;"><i class="fas fa-tasks"></i> Thao tác với đơn hàng</h4>
                            
                            <?php if ($return_request == 1): ?>
                                <!-- Đã yêu cầu trả hàng -->
                                <div style="background:#fff3cd;border-left:4px solid #ffc107;padding:15px;border-radius:8px;">
                                    <p style="margin:0;color:#856404;font-weight:600;">
                                        <i class="fas fa-undo"></i> Đã gửi yêu cầu trả hàng/hoàn tiền
                                    </p>
                                    <p style="margin:8px 0 0 0;color:#856404;font-size:14px;">
                                        <strong>Trạng thái:</strong> 
                                        <span style="background:#fff;padding:4px 12px;border-radius:12px;display:inline-block;margin-top:5px;">
                                            <?php echo $return_status; ?>
                                        </span>
                                    </p>
                                    <?php if ($order['return_reason']): ?>
                                        <p style="margin:8px 0 0 0;color:#856404;font-size:14px;">
                                            <strong>Lý do:</strong> <?php echo htmlspecialchars($order['return_reason']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($customer_confirmed == 1): ?>
                                <!-- Đã xác nhận hài lòng -->
                                <div style="background:#d4edda;border-left:4px solid #28a745;padding:15px;border-radius:8px;">
                                    <p style="margin:0;color:#155724;font-weight:600;">
                                        <i class="fas fa-check-circle"></i> Bạn đã xác nhận hài lòng với đơn hàng này
                                    </p>
                                    <p style="margin:8px 0 0 0;color:#155724;font-size:14px;">
                                        Cảm ơn bạn đã tin tưởng và mua sắm tại TTHUONG Store!
                                    </p>
                                </div>
                            <?php elseif ($days_left > 0): ?>
                                <!-- Trong thời gian cho phép trả hàng -->
                                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                                    <button onclick="confirmDelivery(<?php echo $order['order_id']; ?>)" 
                                            class="btn-confirm-delivery" 
                                            style="flex:1;min-width:200px;padding:12px 20px;background:#28a745;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:600;transition:all 0.3s;">
                                        <i class="fas fa-check-circle"></i> Đã nhận hàng và hài lòng
                                    </button>
                                    <button onclick="openReturnModal(<?php echo $order['order_id']; ?>)" 
                                            class="btn-request-return" 
                                            style="flex:1;min-width:200px;padding:12px 20px;background:#dc3545;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:600;transition:all 0.3s;">
                                        <i class="fas fa-undo"></i> Yêu cầu trả hàng/hoàn tiền
                                    </button>
                                </div>
                                <p style="margin:10px 0 0 0;color:#666;font-size:13px;">
                                    <i class="fas fa-info-circle"></i> 
                                    Còn <strong><?php echo $days_left; ?> ngày</strong> để yêu cầu trả hàng/hoàn tiền
                                </p>
                            <?php else: ?>
                                <!-- Hết thời gian trả hàng -->
                                <div style="background:#f8d7da;border-left:4px solid #dc3545;padding:15px;border-radius:8px;">
                                    <p style="margin:0;color:#721c24;font-weight:600;">
                                        <i class="fas fa-exclamation-triangle"></i> Đã hết thời gian trả hàng/hoàn tiền
                                    </p>
                                    <p style="margin:8px 0 0 0;color:#721c24;font-size:14px;">
                                        Thời gian cho phép trả hàng là <?php echo $return_days_limit; ?> ngày kể từ khi nhận hàng
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>


                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-orders">
                <i class="fas fa-box-open fa-3x"></i>
                <h3>Bạn chưa có đơn hàng nào</h3>
                <p>Hãy đặt hàng để xem thông tin đơn hàng tại đây</p>
                <a href="products.php" class="btn-order">Mua sắm ngay</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Yêu Cầu Trả Hàng -->
    <div id="returnModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;overflow-y:auto;">
        <div style="background:white !important;border-radius:12px;padding:30px !important;max-width:500px;width:90%;box-shadow:0 10px 40px rgba(0,0,0,0.3);margin:50px auto;max-height:90vh;overflow-y:auto;">
            <h3 style="margin-bottom:20px !important;color:#333 !important;display:block !important;visibility:visible !important;"><i class="fas fa-undo"></i> Yêu cầu trả hàng/hoàn tiền</h3>
            <form id="returnForm" style="display:block !important;visibility:visible !important;">
                <input type="hidden" id="return_order_id" name="order_id">
                <div style="margin-bottom:20px !important;display:block !important;visibility:visible !important;">
                    <label style="display:block !important;margin-bottom:8px !important;font-weight:600;color:#333;visibility:visible !important;">
                        Lý do trả hàng <span style="color:red;">*</span>
                    </label>
                    <textarea name="return_reason" 
                              style="width:100% !important;padding:12px !important;border:2px solid #ddd;border-radius:8px;min-height:120px !important;font-family:inherit;box-sizing:border-box;display:block !important;visibility:visible !important;"
                              placeholder="Vui lòng mô tả rõ lý do bạn muốn trả hàng (sản phẩm lỗi, không đúng mô tả, v.v.)"
                              required></textarea>
                </div>
                <div style="background:#fff3cd !important;border-left:4px solid #ffc107;padding:12px !important;margin-bottom:20px !important;border-radius:8px;display:block !important;visibility:visible !important;">
                    <p style="margin:0 !important;color:#856404 !important;font-size:14px !important;display:block !important;visibility:visible !important;">
                        <i class="fas fa-info-circle"></i> 
                        Chúng tôi sẽ xem xét yêu cầu trong vòng 24-48h và liên hệ lại với bạn
                    </p>
                </div>
                <div style="display:flex !important;gap:10px;justify-content:flex-end;visibility:visible !important;">
                    <button type="button" onclick="closeReturnModal()" 
                            style="padding:12px 24px !important;background:#6c757d !important;color:white !important;border:none;border-radius:8px;cursor:pointer;font-weight:600;display:inline-block !important;visibility:visible !important;">
                        Hủy
                    </button>
                    <button type="submit" 
                            style="padding:12px 24px !important;background:#dc3545 !important;color:white !important;border:none;border-radius:8px;cursor:pointer;font-weight:600;display:inline-block !important;visibility:visible !important;">
                        <i class="fas fa-paper-plane"></i> Gửi yêu cầu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Xác nhận hoàn thành đơn hàng (từ Đã giao -> Hoàn thành)
        function confirmOrderCompletion(orderId) {
            if (confirm('Xác nhận bạn đã nhận hàng và hài lòng với sản phẩm?\n\nSau khi xác nhận, đơn hàng sẽ chuyển sang trạng thái "Hoàn thành".')) {
                fetch('confirm_order_completion.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'order_id=' + orderId
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra. Vui lòng thử lại!');
                });
            }
        }

        // Xác nhận đã nhận hàng (legacy - cho đơn hoàn thành)
        function confirmDelivery(orderId) {
            if (confirm('Xác nhận bạn đã nhận hàng và hài lòng với sản phẩm?')) {
                fetch('process_order_action.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'order_id=' + orderId + '&action=confirm_delivery'
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    alert('Có lỗi xảy ra. Vui lòng thử lại!');
                });
            }
        }

        // Mở modal yêu cầu trả hàng
        function openReturnModal(orderId) {
            document.getElementById('return_order_id').value = orderId;
            document.getElementById('returnModal').style.display = 'block';
        }

        // Đóng modal
        function closeReturnModal() {
            document.getElementById('returnModal').style.display = 'none';
            document.getElementById('returnForm').reset();
        }

        // Xử lý submit form trả hàng
        document.getElementById('returnForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'request_return');
            
            fetch('process_order_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    closeReturnModal();
                    location.reload();
                }
            })
            .catch(error => {
                alert('Có lỗi xảy ra. Vui lòng thử lại!');
            });
        });

        // Đóng modal khi click bên ngoài
        document.getElementById('returnModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReturnModal();
            }
        });
        
        // Style hover cho buttons
        document.addEventListener('DOMContentLoaded', function() {
            const confirmCompletionBtns = document.querySelectorAll('.btn-confirm-completion');
            confirmCompletionBtns.forEach(btn => {
                btn.addEventListener('mouseenter', () => btn.style.background = '#218838');
                btn.addEventListener('mouseleave', () => btn.style.background = '#28a745');
            });
            
            const confirmBtns = document.querySelectorAll('.btn-confirm-delivery');
            confirmBtns.forEach(btn => {
                btn.addEventListener('mouseenter', () => btn.style.background = '#218838');
                btn.addEventListener('mouseleave', () => btn.style.background = '#28a745');
            });
            
            const returnBtns = document.querySelectorAll('.btn-request-return');
            returnBtns.forEach(btn => {
                btn.addEventListener('mouseenter', () => btn.style.background = '#c82333');
                btn.addEventListener('mouseleave', () => btn.style.background = '#dc3545');
            });
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>