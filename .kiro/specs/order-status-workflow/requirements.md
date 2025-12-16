# Requirements Document

## Introduction

Hệ thống quản lý đơn hàng hiện tại cho phép admin trực tiếp đặt trạng thái đơn hàng thành "Hoàn thành". Yêu cầu này nhằm thay đổi quy trình để admin chỉ có thể đặt trạng thái tối đa là "Đã giao", và chỉ khi người dùng xác nhận hài lòng thì đơn hàng mới chuyển sang trạng thái "Hoàn thành". Ngoài ra, hệ thống cần hỗ trợ quy trình trả hàng/hoàn tiền cho khách hàng khi đơn hàng ở trạng thái "Đã giao" hoặc "Hoàn thành" (trong thời gian cho phép). Điều này giúp đảm bảo người dùng thực sự đã nhận hàng trước khi đơn hàng được coi là hoàn tất, đồng thời cung cấp cơ chế xử lý các trường hợp hàng bị lỗi hoặc không đúng mô tả.

## Glossary

- **Admin**: Quản trị viên hệ thống có quyền quản lý đơn hàng
- **User**: Người dùng/khách hàng đặt hàng
- **Order Status**: Trạng thái đơn hàng (Chờ thanh toán, Chờ xác nhận, Đã xác nhận, Đang giao, Đã giao, Yêu cầu trả hàng, Đang trả hàng, Đã hoàn tiền, Hoàn thành, Đã hủy)
- **Order Management System**: Hệ thống quản lý đơn hàng
- **Customer Confirmation**: Xác nhận của khách hàng về việc đã nhận hàng và hài lòng
- **Return Request**: Yêu cầu trả hàng/hoàn tiền từ khách hàng
- **Return Period**: Thời gian cho phép khách hàng yêu cầu trả hàng sau khi hoàn thành đơn
- **Inventory System**: Hệ thống quản lý tồn kho

## Requirements

### Requirement 1

**User Story:** Là một admin, tôi muốn chỉ có thể đặt trạng thái đơn hàng tối đa là "Đã giao", để đảm bảo khách hàng phải xác nhận trước khi đơn hàng được coi là hoàn thành.

#### Acceptance Criteria

1. WHEN admin cập nhật trạng thái đơn hàng THEN the Order Management System SHALL allow status values "Chờ thanh toán", "Chờ xác nhận", "Đã xác nhận", "Đang giao", "Đã giao", and "Đã hủy"
2. WHEN admin attempts to set order status to "Hoàn thành" THEN the Order Management System SHALL prevent the action and maintain current status
3. WHEN admin views the status dropdown THEN the Order Management System SHALL display all available statuses except "Hoàn thành"
4. WHEN order status is "Đã giao" THEN the Order Management System SHALL wait for customer confirmation before transitioning to "Hoàn thành"

### Requirement 2

**User Story:** Là một khách hàng, tôi muốn xác nhận đã nhận hàng và hài lòng, để đơn hàng được chuyển sang trạng thái hoàn thành.

#### Acceptance Criteria

1. WHEN order status is "Đã giao" THEN the Order Management System SHALL display a confirmation button to the User
2. WHEN User clicks the confirmation button THEN the Order Management System SHALL update order status to "Hoàn thành"
3. WHEN User confirms delivery THEN the Inventory System SHALL deduct stock quantity and increment sold quantity
4. WHEN order status is "Hoàn thành" THEN the Order Management System SHALL hide the confirmation button
5. WHEN order status is not "Đã giao" THEN the Order Management System SHALL not display the confirmation button

### Requirement 3

**User Story:** Là một admin, tôi muốn hệ thống tự động quản lý tồn kho khi đơn hàng được xác nhận hoàn thành, để đảm bảo dữ liệu tồn kho chính xác.

#### Acceptance Criteria

1. WHEN User confirms order completion THEN the Inventory System SHALL subtract ordered quantity from stock quantity for each product
2. WHEN User confirms order completion THEN the Inventory System SHALL add ordered quantity to sold quantity for each product
3. WHEN admin sets order status to "Đã giao" THEN the Inventory System SHALL not modify stock or sold quantities
4. WHEN order transitions to "Hoàn thành" THEN the Order Management System SHALL record the completion timestamp

### Requirement 4

**User Story:** Là một khách hàng, tôi muốn có thể yêu cầu trả hàng/hoàn tiền khi đơn hàng ở trạng thái "Đã giao", để xử lý các trường hợp hàng bị lỗi hoặc không đúng mô tả.

#### Acceptance Criteria

1. WHEN order status is "Đã giao" THEN the Order Management System SHALL display both confirmation button and return request button to the User
2. WHEN User clicks return request button THEN the Order Management System SHALL display return reason form
3. WHEN User submits return request THEN the Order Management System SHALL update order status to "Yêu cầu trả hàng"
4. WHEN order status is "Yêu cầu trả hàng" THEN the Order Management System SHALL hide both confirmation and return buttons
5. WHEN User submits return request THEN the Order Management System SHALL record return reason and timestamp

### Requirement 5

**User Story:** Là một khách hàng, tôi muốn chỉ thấy tùy chọn yêu cầu trả hàng khi đơn hàng đã hoàn thành trong thời gian cho phép, để có thể xử lý các vấn đề phát sinh sau khi xác nhận.

#### Acceptance Criteria

1. WHEN order status is "Hoàn thành" AND within return period THEN the Order Management System SHALL display return request option
2. WHEN order status is "Hoàn thành" AND beyond return period THEN the Order Management System SHALL hide return request option
3. WHEN order status is "Hoàn thành" THEN the Order Management System SHALL hide the delivery confirmation button
4. WHEN User has confirmed delivery THEN the Order Management System SHALL display confirmation success message

### Requirement 6

**User Story:** Là một admin, tôi muốn xử lý các yêu cầu trả hàng/hoàn tiền từ khách hàng, để đảm bảo quy trình hoàn trả được thực hiện đúng cách.

#### Acceptance Criteria

1. WHEN order status is "Yêu cầu trả hàng" THEN the Order Management System SHALL allow admin to view return reason
2. WHEN admin reviews return request THEN the Order Management System SHALL allow admin to approve or reject the request
3. WHEN admin approves return request THEN the Order Management System SHALL update order status to "Đang trả hàng"
4. WHEN admin rejects return request THEN the Order Management System SHALL update order status back to "Đã giao"
5. WHEN order status is "Đang trả hàng" THEN the Order Management System SHALL allow admin to set status to "Đã hoàn tiền" after receiving returned items

### Requirement 7

**User Story:** Là một admin, tôi muốn hệ thống tự động hoàn trả tồn kho khi đơn hàng được hoàn tiền, để đảm bảo dữ liệu tồn kho chính xác.

#### Acceptance Criteria

1. WHEN admin sets order status to "Đã hoàn tiền" THEN the Inventory System SHALL add returned quantity back to stock quantity for each product
2. WHEN order transitions to "Đã hoàn tiền" THEN the Inventory System SHALL subtract returned quantity from sold quantity for each product
3. WHEN order status is "Yêu cầu trả hàng" or "Đang trả hàng" THEN the Inventory System SHALL not modify stock or sold quantities
4. WHEN order transitions to "Đã hoàn tiền" THEN the Order Management System SHALL record the refund timestamp

### Requirement 8

**User Story:** Là một admin, tôi muốn không thể thay đổi trạng thái của đơn hàng đã hoàn thành, đã hủy, hoặc đã hoàn tiền, để đảm bảo tính toàn vẹn dữ liệu.

#### Acceptance Criteria

1. WHEN order status is "Hoàn thành" THEN the Order Management System SHALL disable status modification by admin
2. WHEN order status is "Đã hủy" THEN the Order Management System SHALL disable status modification by admin
3. WHEN order status is "Đã hoàn tiền" THEN the Order Management System SHALL disable status modification by admin
4. WHEN admin attempts to modify locked order status THEN the Order Management System SHALL display an error message
5. WHEN order status is locked THEN the Order Management System SHALL display a locked indicator in the admin interface
