<?php
session_start();
require '../db.php';

// Set content type for JSON response
header('Content-Type: application/json');

// Check for staff session and POST request
if (!isset($_SESSION['staff_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Access denied or invalid request method.']);
    exit;
}

$staff_id = intval($_SESSION['staff_id']);
$delivery_id = isset($_POST['delivery_id']) ? intval($_POST['delivery_id']) : 0;
$new_status = isset($_POST['status']) ? trim($_POST['status']) : '';

// 1. Validation
$valid_statuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
if (!in_array($new_status, $valid_statuses) || $delivery_id <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid delivery ID or status value.']);
    exit;
}

// Start Transaction
$conn->begin_transaction();

try {
    // --- Step 1: Update the status in the 'delivery' table ---
    $stmt_delivery = $conn->prepare("UPDATE delivery SET status = ? WHERE delivery_id = ? AND delivery_staff_id = ?");
    $stmt_delivery->bind_param('sii', $new_status, $delivery_id, $staff_id);
    $stmt_delivery->execute();
    $stmt_delivery->close();
    
    // Check for success (either row updated or status was already the same)
    // If we only check affected_rows, we fail if the status hasn't changed, so we continue.

    // --- Step 2: Get the corresponding order_id ---
    $order_id = null;
    $stmt_fetch_order = $conn->prepare("SELECT order_id FROM delivery WHERE delivery_id = ?");
    $stmt_fetch_order->bind_param('i', $delivery_id);
    $stmt_fetch_order->execute();
    $result_order = $stmt_fetch_order->get_result();
    
    if ($row = $result_order->fetch_assoc()) {
        $order_id = $row['order_id'];
    }
    $stmt_fetch_order->close();
    
    if (!$order_id) {
        throw new Exception("Order ID not found for delivery ID: $delivery_id");
    }

    // --- Step 3: Update the status in the 'orders' table ---
    // The order status must match the delivery status as per the requirement.
    $stmt_orders = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt_orders->bind_param('si', $new_status, $order_id);
    $stmt_orders->execute();
    $stmt_orders->close();
    
    // Commit transaction
    $conn->commit();
    
    // Send success response
    echo json_encode(['success' => true, 'message' => 'Status and order updated successfully.', 'new_status' => $new_status, 'order_id' => $order_id]);

} catch (Exception $e) {
    // Rollback transaction on failure
    $conn->rollback();
    
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Database transaction failed.']);
    
} finally {
    $conn->close();
}
?>