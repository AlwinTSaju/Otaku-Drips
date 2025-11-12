<?php
session_start();
require '../db.php';

// Security check
if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit; 
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    header("Location: admin-dashboard.php"); 
    exit; 
}

$order_id = intval($_POST['order_id'] ?? 0);
$staff_id = intval($_POST['staff_id'] ?? 0);

if ($order_id === 0 || $staff_id === 0) {
    $_SESSION['error'] = "Invalid order or staff selection.";
    header("Location: admin-dashboard.php");
    exit;
}

$conn->begin_transaction(); // Start transaction for data integrity
$ok = false;

try {
    // 1. Check if a delivery record already exists for this order
    $check_sql = "SELECT delivery_id FROM delivery WHERE order_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $existing_delivery_id = $res->fetch_assoc()['delivery_id'] ?? null;
    $stmt->close();

    // The staff assignment means the order is now ready for delivery and should be 'Processing'
    $new_order_status = 'Processing';
    $new_delivery_status = 'Pending'; // Pending pickup/start of delivery by staff

    if ($existing_delivery_id) {
        // 2a. Update existing delivery record
        $update_delivery_sql = "UPDATE delivery SET delivery_staff_id = ?, status = ? WHERE delivery_id = ?";
        $stmt = $conn->prepare($update_delivery_sql);
        $stmt->bind_param('isi', $staff_id, $new_delivery_status, $existing_delivery_id);
        $ok = $stmt->execute();
        $stmt->close();
    } else {
        // 2b. Insert new delivery record
        $insert_delivery_sql = "INSERT INTO delivery (order_id, delivery_staff_id, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_delivery_sql);
        $stmt->bind_param('iis', $order_id, $staff_id, $new_delivery_status);
        $ok = $stmt->execute();
        $stmt->close();
    }
    
    // 3. Update the primary orders table status to 'Processing'
    if ($ok) {
        $update_order_sql = "UPDATE orders SET status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($update_order_sql);
        $stmt->bind_param('si', $new_order_status, $order_id);
        $stmt->execute();
        $stmt->close();
    }

    if ($ok) {
        $conn->commit();
        $_SESSION['success'] = "Order #{$order_id} assigned to staff ID: {$staff_id}. Order status set to 'Processing'.";
    } else {
        $conn->rollback();
        $_SESSION['error'] = "Failed to assign delivery staff.";
    }

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}


header("Location: admin-dashboard.php");
exit;