<?php
require_once 'config/db.php';
require_once 'header.php';

$success_message = '';
$error_message = '';

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validate
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Email không hợp lệ!';
    } else {
        // Lưu vào database
        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        
        if ($stmt->execute()) {
            $success_message = 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.';
            // Reset form
            $_POST = array();
        } else {
            $error_message = 'Có lỗi xảy ra. Vui lòng thử lại sau!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - TTHUONG Bookstore</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fontawesome/all.min.css">
</head>
<body>

    <!-- Breadcrumb -->
    <div class="breadcrumb-container">
        <div class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
            <span class="separator">/</span>
            <span class="current">Liên hệ</span>
        </div>
    </div>

    <!-- Contact Page -->
    <div class="contact-page">
        <div class="contact-header">
            <h1><i class="fas fa-envelope"></i> Liên hệ với chúng tôi</h1>
            <p>Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn</p>
        </div>

        <div class="contact-info-grid">
            <!-- Contact Info -->
            <div class="contact-info">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Địa chỉ</h3>
                        <p>127 Nguyễn Thiện Thành<br>Phường 7, TP. Trà Vinh<br>Tỉnh Trà Vinh</p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h3>Điện thoại</h3>
                        <p><a href="tel:0398123456">0398 123 456</a></p>
                        <p><small>Thứ 2 - Chủ nhật: 8:00 - 21:00</small></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h3>Email</h3>
                        <p><a href="mailto:contact@tthuongbookstore.com">contact@tthuongbookstore.com</a></p>
                        <p><a href="mailto:support@tthuongbookstore.com">support@tthuongbookstore.com</a></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h3>Giờ làm việc</h3>
                        <p>Thứ 2 - Thứ 6: 8:00 - 21:00</p>
                        <p>Thứ 7 - Chủ nhật: 8:00 - 22:00</p>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="social-links">
                    <h3>Kết nối với chúng tôi</h3>
                    <div class="social-icons">
                        <a href="#" class="social-icon facebook" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon instagram" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon youtube" title="Youtube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="social-icon tiktok" title="Tiktok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-section">
            <h2><i class="fas fa-map-marked-alt"></i> Tìm đường đến cửa hàng</h2>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3928.6447470890437!2d106.34142631533266!3d9.93467!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOcKwNTYnMDQuOSJOIDEwNsKwMjAnMzcuMSJF!5e0!3m2!1svi!2s!4v1234567890" 
                        width="100%" 
                        height="450" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <h2><i class="fas fa-question-circle"></i> Câu hỏi thường gặp</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>Làm thế nào để đặt hàng?</h3>
                    <p>Bạn có thể đặt hàng trực tiếp trên website bằng cách thêm sách vào giỏ hàng và tiến hành thanh toán, hoặc liên hệ hotline: 0398 123 456.</p>
                </div>

                <div class="faq-item">
                    <h3>Thời gian giao hàng?</h3>
                    <p>Thời gian giao hàng từ 2-5 ngày làm việc tùy theo khu vực. Nội thành TP. Trà Vinh: 1-2 ngày.</p>
                </div>

                <div class="faq-item">
                    <h3>Chính sách đổi trả?</h3>
                    <p>Chúng tôi hỗ trợ đổi trả trong vòng 7 ngày nếu sách có lỗi từ nhà sản xuất. Chi tiết xem tại trang Chính sách.</p>
                </div>

                <div class="faq-item">
                    <h3>Phương thức thanh toán?</h3>
                    <p>Hỗ trợ thanh toán COD (tiền mặt khi nhận hàng) và chuyển khoản ngân hàng.</p>
                </div>
            </div>
        </div>
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
        
        .contact-page {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .contact-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .contact-header h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 10px;
            display: inline-flex;
            align-items: center;
            gap: 15px;
        }
        
        .contact-header p {
            font-size: 18px;
            color: #666;
        }
        
        .contact-info-grid {
            max-width: 1200px;
            margin: 0 auto 60px;
        }
        
        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .info-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            gap: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        
        .info-icon {
            width: 50px;
            height: 50px;
            background: #333333;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #EBE9E5;
            font-size: 22px;
            flex-shrink: 0;
        }
        
        .info-content h3 {
            font-size: 18px;
            color: #333;
            margin: 0 0 8px 0;
        }
        
        .info-content p {
            color: #666;
            margin: 4px 0;
            line-height: 1.6;
        }
        
        .info-content a {
            color: #333333;
            text-decoration: none;
        }
        
        .info-content a:hover {
            text-decoration: underline;
        }
        
        .social-links {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            grid-column: 1 / -1;
        }
        
        .social-links h3 {
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .social-icons {
            display: flex;
            gap: 12px;
        }
        
        .social-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: transform 0.3s;
        }
        
        .social-icon:hover {
            transform: scale(1.1);
        }
        
        .social-icon.facebook {
            background: #1877f2;
        }
        
        .social-icon.instagram {
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        }
        
        .social-icon.youtube {
            background: #ff0000;
        }
        
        .social-icon.tiktok {
            background: #000;
        }
        
        /* Map Section */
        .map-section {
            margin-bottom: 60px;
        }
        
        .map-section h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .map-container {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        /* FAQ Section */
        .faq-section h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .faq-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        
        .faq-item {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .faq-item h3 {
            font-size: 18px;
            color: #667eea;
            margin-bottom: 12px;
        }
        
        .faq-item p {
            color: #666;
            line-height: 1.6;
        }
        
        @media (max-width: 992px) {
            .faq-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>
