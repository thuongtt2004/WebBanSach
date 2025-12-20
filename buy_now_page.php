<?php
require_once 'config/connect.php';
require_once 'session_init.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để mua hàng';
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: login_page.php');
    exit();
}

// Kiểm tra product_id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'Sản phẩm không hợp lệ';
    header('Location: products.php');
    exit();
}

$product_id = trim($_GET['id']);
$user_id = intval($_SESSION['user_id']);

// Lấy thông tin sản phẩm
$product_query = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    $_SESSION['error'] = 'Sản phẩm không tồn tại';
    header('Location: products.php');
    exit();
}

// Kiểm tra tồn kho
if ($product['stock_quantity'] < 1) {
    $_SESSION['error'] = 'Sản phẩm đã hết hàng';
    header('Location: products.php?id=' . $product_id);
    exit();
}

// Lấy thông tin người dùng
$user_sql = "SELECT * FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

$total = $product['price'];

require_once 'header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mua ngay - TTHUONG Store</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dathang.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/payment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <h1 class="page-title"><i class="fas fa-shopping-bag"></i> Mua ngay</h1>

    <div class="cart-container">
        <div class="cart-items">
            <div class="cart-item">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                
                <div class="item-details">
                    <h3 class="item-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p class="item-price">Giá: <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
                    <div class="quantity-controls">
                        <span class="quantity-display">Số lượng: 1</span>
                    </div>
                </div>

                <div class="item-actions">
                    <p class="item-total">Tổng: <?php echo number_format($total, 0, ',', '.'); ?> VNĐ</p>
                </div>
            </div>
        </div>

        <div class="cart-summary">
            <h3><i class="fas fa-receipt"></i> Chi tiết đơn hàng</h3>
            
            <div class="price-breakdown">
                <div class="price-row total-row">
                    <span><strong>Tổng cộng:</strong></span>
                    <span id="totalAmount" class="total-amount"><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</span>
                </div>
            </div>
            
            <button class="btn-order-main" onclick="openOrderModal()">
                <i class="fas fa-shopping-bag"></i> Xác nhận đặt hàng
            </button>
        </div>
    </div>

    <!-- Modal xác nhận thông tin đặt hàng -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-check"></i> Xác nhận thông tin đặt hàng</h2>
                <span class="close-modal" onclick="closeOrderModal()">&times;</span>
            </div>
            <form id="orderForm" onsubmit="return handleOrderSubmit(event)">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Họ và tên:</label>
                        <input type="text" id="modal_full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email:</label>
                        <input type="email" id="modal_email" name="email" 
                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Số điện thoại:</label>
                        <input type="tel" id="modal_phone" name="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                               pattern="[0-9]{10,11}" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng:</label>
                        <textarea id="modal_address" name="address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-sticky-note"></i> Ghi chú (không bắt buộc):</label>
                        <textarea id="modal_notes" name="notes" rows="2" placeholder="Ghi chú thêm về đơn hàng..."></textarea>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-credit-card"></i> Phương thức thanh toán:</label>
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <span class="payment-label">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <div>
                                        <strong>Thanh toán khi nhận hàng (COD)</strong>
                                        <small>Thanh toán bằng tiền mặt khi nhận hàng</small>
                                    </div>
                                </span>
                            </label>
                            
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="bank_transfer">
                                <span class="payment-label">
                                    <i class="fas fa-university"></i>
                                    <div>
                                        <strong>Chuyển khoản ngân hàng</strong>
                                        <small>Chuyển khoản trước khi giao hàng</small>
                                    </div>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Bank Transfer Info (hidden by default) -->
                    <div class="bank-transfer-info" id="bankTransferInfo" style="display: none;">
                        <div class="bank-card">
                            <h4><i class="fas fa-university"></i> Thông tin chuyển khoản</h4>
                            <div class="bank-details">
                                <p><strong>Ngân hàng:</strong> MB Bank (Ngân hàng Quân Đội)</p>
                                <p><strong>Số tài khoản:</strong> <span class="account-number">0220623499999</span></p>
                                <p><strong>Chủ tài khoản:</strong> TRAN THANH THUONG</p>
                                <p><strong>Chi nhánh:</strong> TP. Hồ Chí Minh</p>
                            </div>
                        </div>
                    </div>

                    <div class="order-summary-modal">
                        <h3><i class="fas fa-receipt"></i> Tổng đơn hàng:</h3>
                        <div class="price-details">
                            <div class="price-line total-line">
                                <span><strong>Tổng cộng:</strong></span>
                                <span class="modal-total" id="modalTotal"><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeOrderModal()">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn-confirm">
                        <i class="fas fa-check-circle"></i> Xác nhận đặt hàng
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mở modal đặt hàng
        function openOrderModal() {
            const modal = document.getElementById('orderModal');
            const modalBody = modal.querySelector('.modal-body');
            const formGroups = modal.querySelectorAll('.form-group');
            
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            document.body.style.overflow = 'hidden';
            
            if (modalBody) {
                modalBody.style.display = 'block';
                modalBody.style.visibility = 'visible';
                modalBody.style.opacity = '1';
                modalBody.style.height = 'auto';
            }
            
            formGroups.forEach(group => {
                group.style.display = 'block';
                group.style.visibility = 'visible';
                group.style.opacity = '1';
            });
        }

        // Đóng modal
        function closeOrderModal() {
            document.getElementById('orderModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target === modal) {
                closeOrderModal();
            }
        }

        // Hiển thị/ẩn thông tin chuyển khoản
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const bankInfo = document.getElementById('bankTransferInfo');
                if (this.value === 'bank_transfer') {
                    bankInfo.style.display = 'block';
                } else {
                    bankInfo.style.display = 'none';
                }
            });
        });

        // Xử lý submit form đặt hàng
        function handleOrderSubmit(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);

            // Hiển thị loading
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            fetch('process_buy_now.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeOrderModal();
                    
                    if (data.payment_method === 'bank_transfer') {
                        setTimeout(() => {
                            openBankApp(data.order_id);
                        }, 300);
                        
                        setTimeout(() => {
                            alert('Đặt hàng thành công! Mã đơn hàng: #' + data.order_id + '\nVui lòng chuyển khoản theo thông tin hiển thị.');
                        }, 500);
                    } else {
                        alert('Đặt hàng thành công! Mã đơn hàng: #' + data.order_id + '\nCảm ơn bạn đã mua hàng!');
                        window.location.href = 'home.php';
                    }
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi đặt hàng');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi đặt hàng');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }

        // Hàm mở app ngân hàng với QR code
        function openBankApp(orderId) {
            const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            const bankUrl = `https://img.vietqr.io/image/MB-0220623499999-compact2.png?amount=${<?php echo $total; ?>}&addInfo=TTHUONG ${orderId}`;
            
            if (isMobile) {
                window.open(bankUrl, '_blank');
            } else {
                const qrModal = document.createElement('div');
                qrModal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);display:flex;align-items:center;justify-content:center;z-index:10000';
                qrModal.innerHTML = `
                    <div style="background:white;padding:30px;border-radius:15px;text-align:center;max-width:400px;">
                        <h3 style="margin:0 0 20px 0;color:#333;">Quét mã QR để thanh toán</h3>
                        <img src="${bankUrl}" style="width:100%;max-width:300px;border:2px solid #ddd;border-radius:10px;">
                        <p style="margin:15px 0;color:#666;">Mã đơn hàng: #${orderId}</p>
                        <p style="color:#dc3545;font-weight:bold;font-size:18px;">Số tiền: ${new Intl.NumberFormat('vi-VN').format(<?php echo $total; ?>)} VNĐ</p>
                        <button onclick="this.parentElement.parentElement.remove()" 
                                style="margin-top:20px;padding:10px 30px;background:#dc3545;color:white;border:none;border-radius:8px;cursor:pointer;font-size:16px;">
                            Đóng
                        </button>
                    </div>
                `;
                document.body.appendChild(qrModal);
            }
        }
    </script>

    <?php require_once 'footer.php'; ?>
</body>
</html>
