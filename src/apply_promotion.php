<?php
// Tắt hiển thị lỗi để không làm hỏng JSON response
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);

session_start();
require_once 'config/connect.php';

/** @var mysqli $conn */

header('Content-Type: application/json');

// Input: cart_items, coupon_code (optional)
$cart_items = json_decode($_POST['cart_items'] ?? '[]', true);
$coupon_code = trim($_POST['coupon_code'] ?? '');
$user_id = $_SESSION['user_id'] ?? null;

$response = [
    'success' => false,
    'subtotal' => 0,
    'discount' => 0,
    'total' => 0,
    'applied_promotions' => [],
    'message' => '',
    'debug' => []
];

if (empty($cart_items)) {
    $response['message'] = 'Giỏ hàng trống';
    echo json_encode($response);
    exit;
}

$response['debug'][] = 'Số sản phẩm trong giỏ: ' . count($cart_items);

// Tính tổng tiền gốc
$subtotal = 0;
$product_ids = [];
$category_ids = [];

foreach ($cart_items as $item) {
    $product_id = $item['product_id'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    
    $subtotal += $price * $quantity;
    $product_ids[] = $product_id;
    
    // Lấy category_id của sản phẩm (nếu có bảng categories)
    $cat_query = $conn->query("SELECT category_id FROM products WHERE product_id = '$product_id'");
    if ($cat_query && $cat_row = $cat_query->fetch_assoc()) {
        if (!empty($cat_row['category_id'])) {
            $category_ids[] = $cat_row['category_id'];
        }
    }
}

$response['subtotal'] = $subtotal;
$response['debug'][] = 'Subtotal: ' . $subtotal;
$response['debug'][] = 'Coupon code: ' . $coupon_code;

$now = date('Y-m-d H:i:s');
$total_discount = 0;
$applied_promotions = [];

// 1. Tìm các khuyến mãi có thể áp dụng
$applicable_promotions = [];

// Flash Sale (toàn cửa hàng)
$flash_query = "SELECT * FROM promotions 
                WHERE promotion_type = 'flash_sale' 
                AND status = 'active' 
                AND '$now' BETWEEN start_date AND end_date 
                AND (usage_limit IS NULL OR used_count < usage_limit)
                AND min_order_amount <= $subtotal
                ORDER BY discount_value DESC";
$flash_result = $conn->query($flash_query);
$response['debug'][] = 'Flash sale query: ' . $flash_query;
$response['debug'][] = 'Flash sale found: ' . $flash_result->num_rows;
while ($promo = $flash_result->fetch_assoc()) {
    $applicable_promotions[] = $promo;
}

// Khuyến mãi theo sản phẩm
if (!empty($product_ids)) {
    $product_ids_str = "'" . implode("','", $product_ids) . "'";
    $product_promo_query = "SELECT p.* FROM promotions p
                            INNER JOIN promotion_products pp ON p.promotion_id = pp.promotion_id
                            WHERE p.promotion_type = 'product'
                            AND p.status = 'active'
                            AND '$now' BETWEEN p.start_date AND p.end_date
                            AND (p.usage_limit IS NULL OR p.used_count < p.usage_limit)
                            AND p.min_order_amount <= $subtotal
                            AND pp.product_id IN ($product_ids_str)
                            GROUP BY p.promotion_id
                            ORDER BY p.discount_value DESC";
    $product_promo_result = $conn->query($product_promo_query);
    while ($promo = $product_promo_result->fetch_assoc()) {
        $applicable_promotions[] = $promo;
    }
}

// Khuyến mãi theo danh mục
if (!empty($category_ids)) {
    $category_ids_str = implode(",", array_unique($category_ids));
    $category_promo_query = "SELECT p.* FROM promotions p
                             INNER JOIN promotion_categories pc ON p.promotion_id = pc.promotion_id
                             WHERE p.promotion_type = 'category'
                             AND p.status = 'active'
                             AND '$now' BETWEEN p.start_date AND p.end_date
                             AND (p.usage_limit IS NULL OR p.used_count < p.usage_limit)
                             AND p.min_order_amount <= $subtotal
                             AND pc.category_id IN ($category_ids_str)
                             GROUP BY p.promotion_id
                             ORDER BY p.discount_value DESC";
    $category_promo_result = $conn->query($category_promo_query);
    while ($promo = $category_promo_result->fetch_assoc()) {
        $applicable_promotions[] = $promo;
    }
}

// Khuyến mãi theo đơn hàng tối thiểu
$minimum_order_query = "SELECT * FROM promotions
                        WHERE promotion_type = 'minimum_order'
                        AND status = 'active'
                        AND '$now' BETWEEN start_date AND end_date
                        AND (usage_limit IS NULL OR used_count < usage_limit)
                        AND min_order_amount <= $subtotal
                        ORDER BY discount_value DESC";
$minimum_order_result = $conn->query($minimum_order_query);
while ($promo = $minimum_order_result->fetch_assoc()) {
    $applicable_promotions[] = $promo;
}

// 2. Xử lý mã giảm giá (coupon)
if (!empty($coupon_code)) {
    $response['debug'][] = 'Checking coupon: ' . $coupon_code;
    $coupon_query = $conn->prepare("SELECT * FROM promotions 
                                    WHERE promotion_type = 'coupon' 
                                    AND promotion_code = ?
                                    AND status = 'active'
                                    AND ? BETWEEN start_date AND end_date
                                    AND (usage_limit IS NULL OR used_count < usage_limit)
                                    AND min_order_amount <= ?");
    $coupon_query->bind_param("ssd", $coupon_code, $now, $subtotal);
    $coupon_query->execute();
    $coupon_result = $coupon_query->get_result();
    
    $response['debug'][] = 'Coupon found: ' . $coupon_result->num_rows;
    
    if ($coupon_result->num_rows > 0) {
        $coupon = $coupon_result->fetch_assoc();
        $applicable_promotions[] = $coupon;
        $response['debug'][] = 'Coupon applied: ' . $coupon['promotion_name'];
    } else {
        $response['message'] = 'Mã giảm giá không hợp lệ hoặc đã hết hạn';
        $response['debug'][] = 'Coupon invalid';
    }
    $coupon_query->close();
}

// 3. Tính toán giảm giá (chọn khuyến mãi tốt nhất cho khách hàng)
// Loại bỏ trùng lặp và sắp xếp theo giá trị giảm giá
$unique_promotions = [];
foreach ($applicable_promotions as $promo) {
    $unique_promotions[$promo['promotion_id']] = $promo;
}

// Tính toán giá trị giảm thực tế cho mỗi khuyến mãi
$promotion_discounts = [];
foreach ($unique_promotions as $promo) {
    $discount_amount = 0;
    
    if ($promo['discount_type'] == 'percentage') {
        $discount_amount = ($subtotal * $promo['discount_value']) / 100;
        
        // Áp dụng giảm tối đa nếu có
        if ($promo['max_discount'] !== null && $discount_amount > $promo['max_discount']) {
            $discount_amount = $promo['max_discount'];
        }
    } else {
        // fixed_amount
        $discount_amount = $promo['discount_value'];
    }
    
    // Đảm bảo giảm giá không vượt quá tổng tiền
    if ($discount_amount > $subtotal) {
        $discount_amount = $subtotal;
    }
    
    $promotion_discounts[] = [
        'promotion' => $promo,
        'discount' => $discount_amount
    ];
}

// Sắp xếp theo giá trị giảm giá giảm dần
usort($promotion_discounts, function($a, $b) {
    return $b['discount'] <=> $a['discount'];
});

// Áp dụng khuyến mãi tốt nhất (hoặc có thể cho phép stack nhiều khuyến mãi)
// Hiện tại: chỉ áp dụng 1 khuyến mãi tốt nhất
$response['debug'][] = 'Applicable promotions count: ' . count($applicable_promotions);
$response['debug'][] = 'Promotion discounts count: ' . count($promotion_discounts);

if (!empty($promotion_discounts)) {
    $best_promotion = $promotion_discounts[0];
    $total_discount = $best_promotion['discount'];
    
    $response['debug'][] = 'Best promotion: ' . $best_promotion['promotion']['promotion_name'];
    $response['debug'][] = 'Discount amount: ' . $total_discount;
    
    $applied_promotions[] = [
        'promotion_id' => $best_promotion['promotion']['promotion_id'],
        'promotion_code' => $best_promotion['promotion']['promotion_code'],
        'promotion_name' => $best_promotion['promotion']['promotion_name'],
        'promotion_type' => $best_promotion['promotion']['promotion_type'],
        'discount_type' => $best_promotion['promotion']['discount_type'],
        'discount_value' => $best_promotion['promotion']['discount_value'],
        'discount_amount' => $total_discount
    ];
    
    $response['success'] = true;
    $response['message'] = 'Đã áp dụng khuyến mãi: ' . $best_promotion['promotion']['promotion_name'];
} else {
    $response['message'] = 'Không có khuyến mãi phù hợp';
    $response['debug'][] = 'No promotions available';
}

// 4. Tính tổng cuối cùng
$response['discount'] = $total_discount;
$response['total'] = max(0, $subtotal - $total_discount);
$response['applied_promotions'] = $applied_promotions;

// 5. Lưu thông tin sử dụng khuyến mãi (sẽ được cập nhật khi đặt hàng thành công)
if (!empty($applied_promotions) && $user_id) {
    $_SESSION['pending_promotion'] = $applied_promotions[0];
}

echo json_encode($response);
$conn->close();
?>
