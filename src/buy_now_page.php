<?php
require_once 'config/connect.php';
require_once 'session_init.php';

/** @var mysqli $conn */

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
    <link rel="stylesheet" href="css/fontawesome/all.min.css">
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
                    <p class="item-price">Giá: <span id="unitPrice"><?php echo number_format($product['price'], 0, ',', '.'); ?></span> VNĐ</p>
                    <div class="quantity-controls">
                        <button type="button" class="qty-btn" onclick="decreaseQuantity()">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="quantityInput" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" onchange="updateQuantity()">
                        <button type="button" class="qty-btn" onclick="increaseQuantity()">
                            <i class="fas fa-plus"></i>
                        </button>
                        <span class="stock-info">Còn <?php echo $product['stock_quantity']; ?> sản phẩm</span>
                    </div>
                </div>

                <div class="item-actions">
                    <p class="item-total">Tổng: <span id="itemTotal"><?php echo number_format($total, 0, ',', '.'); ?></span> VNĐ</p>
                </div>
            </div>
        </div>

        <div class="cart-summary">
            <h3><i class="fas fa-receipt"></i> Chi tiết đơn hàng</h3>
            
            <div class="order-items">
                <div class="order-item">
                    <span class="item-name"><?php echo htmlspecialchars($product['product_name']); ?></span>
                    <span class="item-price"><span id="summaryUnitPrice"><?php echo number_format($product['price'], 0, ',', '.'); ?></span> VNĐ</span>
                </div>
                <div class="order-item">
                    <span class="item-quantity">Số lượng: <span id="summaryQuantity">1</span></span>
                </div>
            </div>
            
            <div class="coupon-section">
                <h4><i class="fas fa-ticket-alt"></i> Mã giảm giá</h4>
                <div class="coupon-input-group">
                    <input type="text" id="couponCode" placeholder="NHẬP MÃ GIẢM GIÁ" class="coupon-input">
                    <button type="button" onclick="applyCouponManual()" class="apply-coupon-btn">
                        <i class="fas fa-check"></i> Áp dụng
                    </button>
                </div>
                <div id="couponMessage" class="coupon-message"></div>
                <div id="appliedPromotion" class="applied-promotion" style="display: none;">
                    <div class="promo-info">
                        <i class="fas fa-check-circle"></i>
                        <span id="promotionText"></span>
                    </div>
                    <button type="button" onclick="removePromotionBuyNow()" class="remove-promo-btn" title="Xóa khuyến mãi">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="price-breakdown">
                <div class="price-row subtotal-row">
                    <span>Tạm tính:</span>
                    <span id="subtotalAmount"><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</span>
                </div>
                <div class="price-row discount-row" id="discountRow" style="display: none;">
                    <span><i class="fas fa-tag"></i> Giảm giá:</span>
                    <span id="discountAmount">-0 VNĐ</span>
                </div>
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
                <input type="hidden" name="quantity" id="hiddenQuantity" value="1">
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
        const unitPrice = <?php echo $product['price']; ?>;
        const maxStock = <?php echo $product['stock_quantity']; ?>;
        
        // Hàm tăng số lượng
        function increaseQuantity() {
            const input = document.getElementById('quantityInput');
            let currentQty = parseInt(input.value);
            if (currentQty < maxStock) {
                input.value = currentQty + 1;
                updateQuantity();
            } else {
                alert('Số lượng tối đa là ' + maxStock);
            }
        }
        
        // Hàm giảm số lượng
        function decreaseQuantity() {
            const input = document.getElementById('quantityInput');
            let currentQty = parseInt(input.value);
            if (currentQty > 1) {
                input.value = currentQty - 1;
                updateQuantity();
            }
        }
        
        // Hàm cập nhật tổng tiền khi thay đổi số lượng
        function updateQuantity() {
            const input = document.getElementById('quantityInput');
            let qty = parseInt(input.value);
            
            // Validate
            if (isNaN(qty) || qty < 1) {
                qty = 1;
                input.value = 1;
            }
            if (qty > maxStock) {
                qty = maxStock;
                input.value = maxStock;
                alert('Số lượng tối đa là ' + maxStock);
            }
            
            // Cập nhật giá
            const newTotal = unitPrice * qty;
            originalPrice = newTotal;
            
            document.getElementById('itemTotal').textContent = formatNumber(newTotal);
            document.getElementById('subtotalAmount').textContent = formatNumber(newTotal) + ' VNĐ';
            document.getElementById('summaryQuantity').textContent = qty;
            document.getElementById('hiddenQuantity').value = qty;
            
            // Reset khuyến mãi và tính lại
            removePromotionBuyNow();
            applyPromotionBuyNow();
        }
        
        // Mở modal đặt hàng
        function openOrderModal() {
            console.log('openOrderModal called');
            const modal = document.getElementById('orderModal');
            console.log('Modal element:', modal);
            
            if (!modal) {
                console.error('Modal not found!');
                return;
            }
            
            const modalBody = modal.querySelector('.modal-body');
            const formGroups = modal.querySelectorAll('.form-group');
            
            console.log('Setting modal display to flex...');
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
            
            console.log('Modal opened successfully');
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
            
            // Thêm thông tin giảm giá nếu có
            if (appliedPromotionId) {
                formData.append('promotion_id', appliedPromotionId);
                const totalText = document.getElementById('totalAmount').textContent;
                const finalAmount = parseFloat(totalText.replace(/[^\d]/g, ''));
                formData.append('discount_amount', originalPrice - finalAmount);
                formData.append('final_amount', finalAmount);
            } else {
                formData.append('final_amount', originalPrice);
            }

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
            const amount = document.getElementById('totalAmount').textContent.replace(/[^\d]/g, '');
            const bankUrl = `https://img.vietqr.io/image/MB-0220623499999-compact2.png?amount=${amount}&addInfo=TTHUONG ${orderId}`;
            const bankAppUrl = `https://dl.vietqr.io/pay?app=mbbank&bank=MB&acc=0220623499999&amount=${amount}&des=TTHUONG ${orderId}`;
            
            if (isMobile) {
                const qrModal = document.createElement('div');
                qrModal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);display:flex;align-items:center;justify-content:center;z-index:10000;padding:20px;';
                qrModal.innerHTML = `
                    <div style="background:white;padding:25px;border-radius:15px;text-align:center;max-width:400px;width:100%;">
                        <h3 style="margin:0 0 15px 0;color:#333;font-size:20px;">Quét mã QR để thanh toán</h3>
                        <img src="${bankUrl}" style="width:100%;max-width:280px;border:2px solid #ddd;border-radius:10px;margin-bottom:15px;">
                        <p style="margin:10px 0;color:#666;font-size:14px;">
                            <strong>Ngân hàng:</strong> MB Bank<br>
                            <strong>STK:</strong> 0220623499999<br>
                            <strong>Chủ TK:</strong> TRAN THANH THUONG<br>
                            <strong>Nội dung:</strong> TTHUONG ${orderId}<br>
                            <span style="color:#28a745;"><i class="fas fa-check-circle"></i> Mã đơn hàng: #${orderId}</span>
                        </p>
                        <p style="color:#dc3545;font-weight:bold;font-size:18px;margin:10px 0;">Số tiền: ${new Intl.NumberFormat('vi-VN').format(amount)} VNĐ</p>
                        
                        <div style="margin:20px 0;padding:15px;background:#f8f9fa;border-radius:8px;border:2px dashed #28a745;">
                            <p style="color:#333;margin-bottom:10px;font-weight:600;"><i class="fas fa-upload"></i> Upload ảnh chứng từ chuyển khoản</p>
                            <input type="file" id="paymentProofFile_${orderId}" accept="image/*" style="display:none;" onchange="uploadPaymentProof(${orderId}, this.files[0])">
                            <button onclick="document.getElementById('paymentProofFile_${orderId}').click()" style="width:100%;background:#28a745;color:white;padding:10px;border:none;border-radius:6px;cursor:pointer;font-weight:600;margin-bottom:8px;">
                                <i class="fas fa-camera"></i> Chọn ảnh chứng từ
                            </button>
                            <small style="color:#666;display:block;text-align:center;">Chụp màn hình giao dịch thành công</small>
                        </div>
                        
                        <div style="display:flex;gap:10px;margin-top:20px;">
                            <button onclick="openBankingApp('${bankAppUrl}')" style="flex:1;background:#333;color:#ebe9e5;padding:12px;border:none;border-radius:8px;cursor:pointer;font-weight:600;">
                                <i class="fas fa-mobile-alt"></i> Mở App Banking
                            </button>
                            <button onclick="this.parentElement.parentElement.parentElement.remove(); window.location.href='home.php';" style="flex:1;background:#dc3545;color:white;padding:12px;border:none;border-radius:8px;cursor:pointer;font-weight:600;">
                                Hoàn thành
                            </button>
                        </div>
                        <p style="color:#999;font-size:12px;margin-top:10px;text-align:center;">
                            <i class="fas fa-info-circle"></i> Nút "Mở App Banking" chỉ hoạt động trên điện thoại có cài app ngân hàng
                        </p>
                    </div>
                `;
                document.body.appendChild(qrModal);
            } else {
                const qrModal = document.createElement('div');
                qrModal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);display:flex;align-items:center;justify-content:center;z-index:10000';
                qrModal.innerHTML = `
                    <div style="background:white;padding:30px;border-radius:15px;text-align:center;max-width:400px;">
                        <h3 style="margin:0 0 20px 0;color:#333;">Quét mã QR để thanh toán</h3>
                        <img src="${bankUrl}" style="width:100%;max-width:300px;border:2px solid #ddd;border-radius:10px;">
                        <p style="margin:15px 0;color:#666;">Mã đơn hàng: #${orderId}</p>
                        <p style="color:#dc3545;font-weight:bold;font-size:18px;">Số tiền: ${new Intl.NumberFormat('vi-VN').format(amount)} VNĐ</p>
                        
                        <div style="margin:20px 0;padding:15px;background:#f8f9fa;border-radius:8px;border:2px dashed #28a745;">
                            <p style="color:#333;margin-bottom:10px;font-weight:600;"><i class="fas fa-upload"></i> Upload ảnh chứng từ chuyển khoản</p>
                            <input type="file" id="paymentProofFile_${orderId}" accept="image/*" style="display:none;" onchange="uploadPaymentProof(${orderId}, this.files[0])">
                            <button onclick="document.getElementById('paymentProofFile_${orderId}').click()" style="width:100%;background:#28a745;color:white;padding:10px;border:none;border-radius:6px;cursor:pointer;font-weight:600;margin-bottom:8px;">
                                <i class="fas fa-camera"></i> Chọn ảnh chứng từ
                            </button>
                            <small style="color:#666;display:block;text-align:center;">Chụp màn hình giao dịch thành công</small>
                        </div>
                        
                        <button onclick="this.parentElement.parentElement.remove(); window.location.href='home.php';" 
                                style="margin-top:20px;padding:10px 30px;background:#dc3545;color:white;border:none;border-radius:8px;cursor:pointer;font-size:16px;">
                            Hoàn thành
                        </button>
                    </div>
                `;
                document.body.appendChild(qrModal);
            }
        }
        
        // Function để mở app banking
        function openBankingApp(appUrl) {
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (isMobile) {
                window.location.href = appUrl;
                setTimeout(() => {
                    alert('Không tìm thấy ứng dụng ngân hàng. Vui lòng quét mã QR bằng app ngân hàng của bạn.');
                }, 2000);
            } else {
                alert('Tính năng này chỉ hoạt động trên điện thoại có cài đặt ứng dụng ngân hàng.\n\nVui lòng:\n1. Quét mã QR bằng app ngân hàng trên điện thoại\n2. Hoặc chuyển khoản thủ công theo thông tin hiển thị');
            }
        }
        
        // Upload payment proof
        function uploadPaymentProof(orderId, file) {
            if (!file) {
                alert('Vui lòng chọn file ảnh');
                return;
            }
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Vui lòng chọn file ảnh (jpg, png, gif)');
                return;
            }
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File ảnh quá lớn (tối đa 5MB)');
                return;
            }
            
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('payment_proof', file);
            
            // Show loading
            const uploadBtn = document.querySelector(`button[onclick*="paymentProofFile_${orderId}"]`);
            const originalText = uploadBtn.innerHTML;
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang upload...';
            
            fetch('upload_payment_proof.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    uploadBtn.innerHTML = '<i class="fas fa-check"></i> Đã upload thành công!';
                    uploadBtn.style.background = '#28a745';
                    setTimeout(() => {
                        alert(data.message);
                    }, 300);
                } else {
                    alert('Lỗi: ' + data.message);
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi upload. Vui lòng thử lại!');
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = originalText;
            });
        }
        
        // Áp dụng mã giảm giá cho buy now
        let appliedPromotionId = null;
        let originalPrice = <?php echo $total; ?>;
        
        // Tự động áp dụng khuyến mãi tốt nhất khi load trang
        document.addEventListener('DOMContentLoaded', function() {
            applyPromotionBuyNow();
        });
        
        async function applyPromotionBuyNow(couponCode = '') {
            const quantity = parseInt(document.getElementById('quantityInput').value);
            const formData = new FormData();
            const cartItems = [{
                product_id: '<?php echo $product_id; ?>',
                quantity: quantity,
                price: <?php echo $product['price']; ?>
            }];
            
            formData.append('cart_items', JSON.stringify(cartItems));
            if (couponCode) {
                formData.append('coupon_code', couponCode);
            }
            
            try {
                const response = await fetch('apply_promotion.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                console.log('Promotion Response:', data);
                
                if (data.success && data.applied_promotions.length > 0) {
                    // Giới hạn chỉ 1 mã
                    const promo = data.applied_promotions[0];
                    appliedPromotionId = promo.promotion_id;
                    
                    const discount = Math.round(data.discount);
                    const newTotal = Math.round(data.total);
                    
                    document.getElementById('discountRow').style.display = 'flex';
                    document.getElementById('discountAmount').textContent = '-' + formatNumber(discount) + ' VNĐ';
                    document.getElementById('totalAmount').textContent = formatNumber(newTotal) + ' VNĐ';
                    document.getElementById('modalTotal').textContent = formatNumber(newTotal) + ' VNĐ';
                    
                    document.getElementById('appliedPromotion').style.display = 'flex';
                    document.getElementById('promotionText').textContent = promo.promotion_name;
                    document.getElementById('couponCode').value = '';
                    
                    if (couponCode) {
                        showMessage(data.message, 'success');
                    }
                } else {
                    if (couponCode) {
                        showMessage(data.message || 'Mã giảm giá không hợp lệ', 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                if (couponCode) {
                    showMessage('Có lỗi xảy ra', 'error');
                }
            }
        }
        
        function applyCouponManual() {
            const code = document.getElementById('couponCode').value.trim();
            if (!code) {
                showMessage('Vui lòng nhập mã giảm giá', 'error');
                return;
            }
            applyPromotionBuyNow(code);
        }
        
        function removePromotionBuyNow() {
            appliedPromotionId = null;
            document.getElementById('discountRow').style.display = 'none';
            document.getElementById('totalAmount').textContent = formatNumber(originalPrice) + ' VNĐ';
            document.getElementById('modalTotal').textContent = formatNumber(originalPrice) + ' VNĐ';
            document.getElementById('appliedPromotion').style.display = 'none';
            showMessage('Đã xóa mã giảm giá', 'success');
        }
        
        function showMessage(message, type) {
            const msgDiv = document.getElementById('couponMessage');
            msgDiv.textContent = message;
            msgDiv.className = 'coupon-message ' + (type === 'success' ? 'success' : 'error');
            msgDiv.style.display = 'block';
            setTimeout(() => { msgDiv.style.display = 'none'; }, 3000);
        }
        
        function formatNumber(num) {
            return new Intl.NumberFormat('vi-VN').format(num);
        }
    </script>

    <?php require_once 'footer.php'; ?>
</body>
</html>
