# Test Checklist - Order Status Workflow

## ✅ Checklist kiểm tra workflow đơn hàng

### 1. Test Admin Order Management
- [ ] Đăng nhập admin và vào trang quản lý đơn hàng
- [ ] Kiểm tra dropdown trạng thái KHÔNG có option "Hoàn thành" (trừ khi đơn đã hoàn thành)
- [ ] Kiểm tra có option "Đã giao" trong dropdown
- [ ] Thử set trạng thái đơn hàng lên "Đã giao" - phải thành công
- [ ] Thử set trạng thái đơn hàng lên "Hoàn thành" - phải bị chặn với thông báo lỗi
- [ ] Kiểm tra đơn "Hoàn thành" và "Đã hủy" có icon khóa đỏ
- [ ] Kiểm tra dropdown và button "Lưu" bị disabled cho đơn đã khóa

### 2. Test Customer Order Confirmation (Đã giao → Hoàn thành)
- [ ] Đăng nhập user và vào trang theo dõi đơn hàng
- [ ] Tìm đơn hàng có trạng thái "Đã giao"
- [ ] Kiểm tra có hiển thị nút "Xác nhận đã nhận hàng"
- [ ] Click nút xác nhận và confirm
- [ ] Kiểm tra đơn hàng chuyển sang trạng thái "Hoàn thành"
- [ ] Kiểm tra nút xác nhận biến mất sau khi hoàn thành
- [ ] Kiểm tra thông báo thành công hiển thị

### 3. Test Inventory Management
- [ ] Ghi nhớ số lượng tồn kho (stock_quantity) và đã bán (sold_quantity) của sản phẩm
- [ ] Admin set đơn hàng lên "Đã giao"
- [ ] Kiểm tra tồn kho KHÔNG thay đổi
- [ ] User xác nhận hoàn thành đơn hàng
- [ ] Kiểm tra tồn kho giảm đúng số lượng đã đặt
- [ ] Kiểm tra số lượng đã bán tăng đúng số lượng đã đặt

### 4. Test Order Status Locking
- [ ] Thử sửa trạng thái đơn "Hoàn thành" - phải bị chặn
- [ ] Thử sửa trạng thái đơn "Đã hủy" - phải bị chặn
- [ ] Kiểm tra thông báo lỗi hiển thị khi cố sửa đơn đã khóa

### 5. Test UI Display Logic
- [ ] Đơn "Đã giao": Chỉ hiện nút "Xác nhận đã nhận hàng"
- [ ] Đơn "Hoàn thành": Hiện options trả hàng (nếu trong thời gian cho phép)
- [ ] Đơn "Hoàn thành": KHÔNG hiện nút "Xác nhận đã nhận hàng"
- [ ] Kiểm tra các trạng thái khác không hiện nút xác nhận

### 6. Test Error Handling
- [ ] Thử xác nhận đơn hàng không thuộc về user - phải bị chặn
- [ ] Thử xác nhận đơn hàng không ở trạng thái "Đã giao" - phải bị chặn
- [ ] Kiểm tra thông báo lỗi rõ ràng cho mỗi trường hợp

### 7. Test Complete Flow (End-to-End)
- [ ] Tạo đơn hàng mới
- [ ] Admin duyệt qua các trạng thái: Chờ xác nhận → Đã xác nhận → Đang giao → Đã giao
- [ ] User xác nhận hoàn thành
- [ ] Kiểm tra trạng thái cuối cùng là "Hoàn thành"
- [ ] Kiểm tra tồn kho đã được trừ chính xác
- [ ] Kiểm tra completed_date được lưu
- [ ] Kiểm tra customer_confirmed = 1

## 📝 Ghi chú
- Tất cả các test case trên phải PASS trước khi deploy lên production
- Nếu có test case nào FAIL, cần fix ngay và test lại
- Kiểm tra cả trên desktop và mobile
