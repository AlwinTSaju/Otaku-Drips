<?php
session_start();
require 'db.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $delivery_id = intval($_POST['delivery_id']);
    $status = trim($_POST['status']);

    // Validate status
    $allowed_status = ['pending', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($status, $allowed_status)) {
        $_SESSION['error'] = "Invalid status selected.";
        header("Location: deliv-staff-dash.php");
        exit;
    }

    // Update delivery status for this staff
    $stmt = $conn->prepare("UPDATE delivery SET status = ? WHERE delivery_id = ? AND delivery_staff_id = ?");
    $stmt->bind_param("sii", $status, $delivery_id, $_SESSION['staff_id']);
    if ($stmt->execute()) {
        // Update the corresponding order status
        $updateOrder = $conn->prepare("
            UPDATE orders 
            SET status = ? 
            WHERE order_id = (SELECT order_id FROM delivery WHERE delivery_id = ?)
        ");
        $updateOrder->bind_param("si", $status, $delivery_id);
        $updateOrder->execute();

        $_SESSION['success'] = "Status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update status.";
    }

    header("Location: deliv-staff-dash.php");
    exit;
}

// If request is not POST
$_SESSION['error'] = "Invalid request.";
header("Location: deliv-staff-dash.php");
exit;
?>
