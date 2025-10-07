<?php
session_start();
require 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $staff_id = intval($_POST['staff_id']);

    if (!$order_id || !$staff_id) {
        $_SESSION['error'] = "Invalid order or staff selection.";
        header("Location: admin-dashboard.php");
        exit;
    }

    // Check if a delivery already exists for this order
    $checkStmt = $conn->prepare("SELECT delivery_id FROM delivery WHERE order_id = ?");
    $checkStmt->bind_param("i", $order_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing delivery
        $row = $result->fetch_assoc();
        $updateStmt = $conn->prepare("UPDATE delivery SET delivery_staff_id = ?, status = 'pending' WHERE delivery_id = ?");
        $updateStmt->bind_param("ii", $staff_id, $row['delivery_id']);
        $updateStmt->execute();
        $_SESSION['success'] = "Delivery reassigned successfully.";
    } else {
        // Insert new delivery
        $insertStmt = $conn->prepare("INSERT INTO delivery (order_id, delivery_staff_id, status) VALUES (?, ?, 'pending')");
        $insertStmt->bind_param("ii", $order_id, $staff_id);
        $insertStmt->execute();
        $_SESSION['success'] = "Delivery assigned successfully.";
    }

    header("Location: admin-dashboard.php");
    exit;
}

$_SESSION['error'] = "Invalid request method.";
header("Location: admin-dashboard.php");
exit;
