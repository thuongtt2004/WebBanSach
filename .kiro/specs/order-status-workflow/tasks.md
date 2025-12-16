# Implementation Plan

- [x] 1. Update admin order status management



  - Modify `admin/admin_orders.php` to remove "Hoàn thành" from status dropdown
  - Add "Đã giao" status option to dropdown
  - Update status validation to prevent admin from setting "Hoàn thành"
  - Add validation error message when admin attempts to set "Hoàn thành"





  - _Requirements: 1.1, 1.2, 1.3_



- [ ] 2. Modify inventory management logic
  - Remove inventory deduction from admin status update process


  - Keep inventory logic only in customer confirmation flow
  - Ensure stock is only deducted when customer confirms completion


  - _Requirements: 3.1, 3.2, 3.3_

- [ ] 3. Create customer order confirmation endpoint
  - Create new PHP file `confirm_order_completion.php` for handling customer confirmation
  - Implement order status validation (must be "Đã giao")


  - Update order status from "Đã giao" to "Hoàn thành"
  - Add completion timestamp recording
  - Implement inventory deduction logic (subtract from stock, add to sold)
  - Add error handling for invalid order states
  - _Requirements: 2.2, 2.3, 3.1, 3.2, 3.4_






- [ ] 4. Update customer order tracking UI
  - Modify `track_order.php` or relevant customer order view
  - Add conditional display logic for confirmation button
  - Show confirmation button only when status is "Đã giao"
  - Hide confirmation button when status is "Hoàn thành"


  - Add success message display after confirmation
  - _Requirements: 2.1, 2.5, 4.1, 4.3, 4.4_

- [x] 5. Implement order status locking



  - Add validation to prevent status changes for "Hoàn thành" orders
  - Add validation to prevent status changes for "Đã hủy" orders
  - Display locked indicator in admin interface for locked orders
  - Show error message when attempting to modify locked orders
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ] 6. Update account/profile order display
  - Modify `account.php` or order history view
  - Ensure confirmation button displays correctly in order history
  - Apply same conditional logic as track_order page
  - _Requirements: 2.1, 4.1, 5.3_

- [ ] 7. Add return/refund statuses to database
  - Update orders table to add new columns: `return_request_date`, `return_reason`, `refund_date`
  - Modify status enum to include "Yêu cầu trả hàng", "Đang trả hàng", "Đã hoàn tiền"
  - Create database migration script
  - _Requirements: 4.3, 4.5, 6.1_

- [ ] 8. Create customer return request endpoint
  - Create new PHP file `request_order_return.php` for handling return requests
  - Implement order status validation (must be "Đã giao" or "Hoàn thành")
  - For "Hoàn thành" orders, validate within return period (7 days)
  - Update order status to "Yêu cầu trả hàng"
  - Record return reason and return_request_date
  - Add error handling for invalid states and expired return period
  - _Requirements: 4.2, 4.3, 4.5, 5.1, 5.2_

- [ ] 9. Update customer UI for return requests
  - Modify `track_order.php` to add return request button
  - Show return button when status is "Đã giao"
  - Show return button when status is "Hoàn thành" AND within return period
  - Hide return button for other statuses
  - Create return reason form modal
  - Add AJAX call to submit return request
  - Display return status messages
  - _Requirements: 4.1, 5.1, 5.2_

- [ ] 10. Update admin order management for returns
  - Modify `admin/admin_orders.php` to add return statuses to dropdown
  - Add "Yêu cầu trả hàng", "Đang trả hàng", "Đã hoàn tiền" to status options
  - Display return reason when order status is "Yêu cầu trả hàng"
  - Add approve/reject buttons for return requests
  - Implement approve action: "Yêu cầu trả hàng" → "Đang trả hàng"
  - Implement reject action: "Yêu cầu trả hàng" → "Đã giao"
  - Update locked statuses to include "Đã hoàn tiền"
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 8.3, 8.5_

- [ ] 11. Implement inventory restoration on refund
  - Modify inventory management logic to handle refunds
  - When admin sets status to "Đá hoàn tiền", restore inventory
  - Add returned quantity back to stock
  - Subtract returned quantity from sold (if order was completed)
  - Record refund_date timestamp
  - _Requirements: 7.1, 7.2, 7.4_

- [ ] 12. Update account page with return functionality
  - Modify `account.php` to show return request buttons
  - Apply same conditional logic as track_order page
  - Display return status in order history
  - _Requirements: 4.1, 5.1_

- [ ] 13. Test complete workflow including returns
  - Test admin can only set status up to "Đã giao"
  - Test customer confirmation button appears for "Đã giao" orders
  - Test customer confirmation updates status to "Hoàn thành"
  - Test inventory is deducted only on customer confirmation
  - Test locked orders cannot be modified
  - Test return request from "Đã giao" status
  - Test return request from "Hoàn thành" within period
  - Test return request blocked after period expires
  - Test admin approve return request
  - Test admin reject return request
  - Test inventory restoration on refund
  - _Requirements: All_
