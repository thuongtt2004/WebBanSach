<?php
/**
 * Email Helper - Hàm hỗ trợ gửi email cho hệ thống
 */

// Tải PHPMailer thủ công (không cần Composer)
// Tải từ: https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.zip
// Giải nén vào thư mục: c:\xampp\htdocs\BanSach\src\includes\PHPMailer\
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Cấu hình email
 */
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'dubu2k4@gmail.com'); // Thay bằng email của bạn
define('SMTP_PASSWORD', 'uxwy nyio rdzv zeba'); // Thay bằng App Password của Gmail
define('FROM_EMAIL', 'dubu2k4@gmail.com'); // Email gửi đi
define('FROM_NAME', 'Bookstore - Nhà Sách Online');

/**
 * Gửi email thông báo đơn hàng đã giao
 * 
 * @param string $to_email Email người nhận
 * @param string $customer_name Tên khách hàng
 * @param int $order_id Mã đơn hàng
 * @param float $total_amount Tổng tiền đơn hàng
 * @param string $order_date Ngày đặt hàng
 * @return bool True nếu gửi thành công, False nếu thất bại
 */
function send_order_delivered_email($to_email, $customer_name, $order_id, $total_amount, $order_date) {
    $mail = new PHPMailer(true);
    
    try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Người gửi và người nhận
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to_email, $customer_name);
        
        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = 'Đơn hàng #' . $order_id . ' đã được giao thành công';
        
        // Template email
        $email_body = get_order_delivered_template($customer_name, $order_id, $total_amount, $order_date);
        $mail->Body = $email_body;
        
        // Nội dung text thuần (fallback)
        $mail->AltBody = strip_tags($email_body);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Template HTML cho email thông báo đơn hàng đã giao
 */
function get_order_delivered_template($customer_name, $order_id, $total_amount, $order_date) {
    $formatted_amount = number_format($total_amount, 0, ',', '.') . ' VNĐ';
    $formatted_date = date('d/m/Y H:i', strtotime($order_date));
    
    return '
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .container {
                max-width: 600px;
                margin: 20px auto;
                background: #ffffff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px;
                text-align: center;
            }
            .header h1 {
                margin: 0;
                font-size: 28px;
            }
            .content {
                padding: 30px;
            }
            .greeting {
                font-size: 18px;
                margin-bottom: 20px;
                color: #333;
            }
            .message {
                background: #f0f9ff;
                border-left: 4px solid #3b82f6;
                padding: 15px;
                margin: 20px 0;
                border-radius: 4px;
            }
            .order-info {
                background: #f9fafb;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
            }
            .order-info table {
                width: 100%;
                border-collapse: collapse;
            }
            .order-info td {
                padding: 10px 0;
                border-bottom: 1px solid #e5e7eb;
            }
            .order-info td:first-child {
                font-weight: bold;
                color: #6b7280;
                width: 40%;
            }
            .order-info td:last-child {
                color: #111827;
            }
            .cta-button {
                display: inline-block;
                padding: 12px 30px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-decoration: none;
                border-radius: 6px;
                margin: 20px 0;
                font-weight: bold;
            }
            .footer {
                background: #f9fafb;
                padding: 20px;
                text-align: center;
                font-size: 14px;
                color: #6b7280;
            }
            .success-icon {
                font-size: 48px;
                text-align: center;
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>📦 Đơn Hàng Đã Được Giao</h1>
            </div>
            
            <div class="content">
                <div class="success-icon">✅</div>
                
                <div class="greeting">
                    Xin chào <strong>' . htmlspecialchars($customer_name) . '</strong>,
                </div>
                
                <div class="message">
                    <p style="margin: 0;">
                        <strong>🎉 Tin vui!</strong> Đơn hàng của bạn đã được giao thành công đến địa chỉ nhận hàng.
                    </p>
                </div>
                
                <div class="order-info">
                    <table>
                        <tr>
                            <td>Mã đơn hàng:</td>
                            <td><strong>#' . $order_id . '</strong></td>
                        </tr>
                        <tr>
                            <td>Ngày đặt hàng:</td>
                            <td>' . $formatted_date . '</td>
                        </tr>
                        <tr>
                            <td>Tổng tiền:</td>
                            <td><strong style="color: #16a34a; font-size: 18px;">' . $formatted_amount . '</strong></td>
                        </tr>
                        <tr>
                            <td>Trạng thái:</td>
                            <td><span style="background: #16a34a; color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px;">✓ Đã giao</span></td>
                        </tr>
                    </table>
                </div>
                
                <p>Cảm ơn bạn đã tin tưởng và mua hàng tại cửa hàng chúng tôi. Chúng tôi rất mong bạn hài lòng với sản phẩm!</p>
                
                <p>Nếu bạn hài lòng với đơn hàng, vui lòng xác nhận hoàn thành đơn hàng để chúng tôi có thể cải thiện dịch vụ tốt hơn.</p>
                
                <center>
                    <a href="' . get_base_url() . '/track_order.php?order_id=' . $order_id . '" class="cta-button">
                        Xem Chi Tiết Đơn Hàng
                    </a>
                </center>
                
                <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
                    ⚠️ Lưu ý: Nếu có bất kỳ vấn đề gì với đơn hàng, bạn có thể yêu cầu trả hàng trong vòng 7 ngày kể từ ngày nhận hàng.
                </p>
            </div>
            
            <div class="footer">
                <p><strong>Bookstore - Nhà Sách Online</strong></p>
                <p>📧 Email: support@bookstore.com | 📞 Hotline: 1900-xxxx</p>
                <p>© 2026 Bookstore. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ';
}

/**
 * Lấy base URL của website
 */
function get_base_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host;
}

/**
 * Gửi email xác nhận đơn hàng mới
 * 
 * @param string $to_email Email người nhận
 * @param string $customer_name Tên khách hàng
 * @param int $order_id Mã đơn hàng
 * @param float $total_amount Tổng tiền đơn hàng
 * @param string $payment_method Phương thức thanh toán
 * @param array $order_items Danh sách sản phẩm trong đơn hàng
 * @return bool True nếu gửi thành công, False nếu thất bại
 */
function send_order_confirmation_email($to_email, $customer_name, $order_id, $total_amount, $payment_method, $order_items = []) {
    $mail = new PHPMailer(true);
    
    try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Người gửi và người nhận
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to_email, $customer_name);
        
        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = 'Xác nhận đơn hàng #' . $order_id . ' - Bookstore';
        
        // Template email
        $email_body = get_order_confirmation_template($customer_name, $order_id, $total_amount, $payment_method, $order_items);
        $mail->Body = $email_body;
        
        // Nội dung text thuần (fallback)
        $mail->AltBody = strip_tags($email_body);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Template HTML cho email xác nhận đơn hàng mới
 */
function get_order_confirmation_template($customer_name, $order_id, $total_amount, $payment_method, $order_items) {
    $formatted_amount = number_format($total_amount, 0, ',', '.') . ' VNĐ';
    $payment_text = $payment_method === 'bank_transfer' ? 'Chuyển khoản' : 'COD (Thanh toán khi nhận hàng)';
    $payment_status = $payment_method === 'bank_transfer' ? 'Chờ thanh toán' : 'Chờ xác nhận';
    
    // Tạo danh sách sản phẩm
    $products_html = '';
    if (!empty($order_items)) {
        foreach ($order_items as $item) {
            $item_total = number_format($item['price'] * $item['quantity'], 0, ',', '.');
            $products_html .= '
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb;">' . htmlspecialchars($item['product_name']) . '</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: center;">' . $item['quantity'] . '</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: right;">' . number_format($item['price'], 0, ',', '.') . ' VNĐ</td>
                <td style="padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: bold;">' . $item_total . ' VNĐ</td>
            </tr>';
        }
    }
    
    // Thông tin chuyển khoản nếu là thanh toán chuyển khoản
    $bank_info = '';
    if ($payment_method === 'bank_transfer') {
        $bank_info = '
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 8px;">
            <h3 style="margin-top: 0; color: #856404;">💳 Thông Tin Chuyển Khoản</h3>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 5px 0; color: #856404;"><strong>Ngân hàng:</strong></td>
                    <td style="padding: 5px 0; color: #856404;">VietcomBank</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #856404;"><strong>Số tài khoản:</strong></td>
                    <td style="padding: 5px 0; color: #856404; font-weight: bold;">1234567890</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #856404;"><strong>Chủ tài khoản:</strong></td>
                    <td style="padding: 5px 0; color: #856404;">BOOKSTORE COMPANY</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #856404;"><strong>Nội dung:</strong></td>
                    <td style="padding: 5px 0; color: #856404; font-weight: bold;">DH' . $order_id . ' ' . htmlspecialchars($customer_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #856404;"><strong>Số tiền:</strong></td>
                    <td style="padding: 5px 0; color: #d9534f; font-weight: bold; font-size: 18px;">' . $formatted_amount . '</td>
                </tr>
            </table>
            <p style="margin-bottom: 0; color: #856404; font-size: 14px;">
                ⚠️ Vui lòng chuyển khoản đúng nội dung để đơn hàng được xử lý nhanh nhất!
            </p>
        </div>';
    }
    
    return '
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .container {
                max-width: 600px;
                margin: 20px auto;
                background: #ffffff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px;
                text-align: center;
            }
            .header h1 {
                margin: 0;
                font-size: 28px;
            }
            .content {
                padding: 30px;
            }
            .greeting {
                font-size: 18px;
                margin-bottom: 20px;
                color: #333;
            }
            .message {
                background: #d4edda;
                border-left: 4px solid #28a745;
                padding: 15px;
                margin: 20px 0;
                border-radius: 4px;
                color: #155724;
            }
            .order-info {
                background: #f9fafb;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
            }
            .order-info table {
                width: 100%;
                border-collapse: collapse;
            }
            .order-info td {
                padding: 10px 0;
                border-bottom: 1px solid #e5e7eb;
            }
            .order-info td:first-child {
                font-weight: bold;
                color: #6b7280;
                width: 40%;
            }
            .order-info td:last-child {
                color: #111827;
            }
            .products-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            .products-table th {
                background: #f3f4f6;
                padding: 12px;
                text-align: left;
                font-weight: bold;
                color: #374151;
                border-bottom: 2px solid #e5e7eb;
            }
            .cta-button {
                display: inline-block;
                padding: 12px 30px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-decoration: none;
                border-radius: 6px;
                margin: 20px 0;
                font-weight: bold;
            }
            .footer {
                background: #f9fafb;
                padding: 20px;
                text-align: center;
                font-size: 14px;
                color: #6b7280;
            }
            .success-icon {
                font-size: 48px;
                text-align: center;
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>✨ Xác Nhận Đơn Hàng</h1>
            </div>
            
            <div class="content">
                <div class="success-icon">🎉</div>
                
                <div class="greeting">
                    Xin chào <strong>' . htmlspecialchars($customer_name) . '</strong>,
                </div>
                
                <div class="message">
                    <p style="margin: 0;">
                        <strong>Cảm ơn bạn đã đặt hàng!</strong> Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.
                    </p>
                </div>
                
                <div class="order-info">
                    <h3 style="margin-top: 0; color: #374151;">📋 Thông Tin Đơn Hàng</h3>
                    <table>
                        <tr>
                            <td>Mã đơn hàng:</td>
                            <td><strong style="color: #667eea; font-size: 18px;">#' . $order_id . '</strong></td>
                        </tr>
                        <tr>
                            <td>Ngày đặt hàng:</td>
                            <td>' . date('d/m/Y H:i') . '</td>
                        </tr>
                        <tr>
                            <td>Tổng tiền:</td>
                            <td><strong style="color: #16a34a; font-size: 18px;">' . $formatted_amount . '</strong></td>
                        </tr>
                        <tr>
                            <td>Phương thức:</td>
                            <td>' . $payment_text . '</td>
                        </tr>
                        <tr>
                            <td>Trạng thái:</td>
                            <td><span style="background: #fbbf24; color: #78350f; padding: 4px 12px; border-radius: 4px; font-size: 12px;">⏳ ' . $payment_status . '</span></td>
                        </tr>
                    </table>
                </div>
                
                ' . ($products_html ? '
                <h3 style="color: #374151;">📦 Sản Phẩm Đã Đặt</h3>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center;">SL</th>
                            <th style="text-align: right;">Đơn giá</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $products_html . '
                    </tbody>
                </table>
                ' : '') . '
                
                ' . $bank_info . '
                
                <p>Chúng tôi sẽ xử lý và giao hàng đến bạn trong thời gian sớm nhất. Bạn có thể theo dõi tình trạng đơn hàng qua link bên dưới:</p>
                
                <center>
                    <a href="' . get_base_url() . '/track_order.php?order_id=' . $order_id . '" class="cta-button">
                        Theo Dõi Đơn Hàng
                    </a>
                </center>
                
                <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
                    💡 <strong>Lưu ý:</strong> Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi qua email hoặc hotline bên dưới.
                </p>
            </div>
            
            <div class="footer">
                <p><strong>Bookstore - Nhà Sách Online</strong></p>
                <p>📧 Email: support@bookstore.com | 📞 Hotline: 1900-xxxx</p>
                <p>© 2026 Bookstore. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ';
}
