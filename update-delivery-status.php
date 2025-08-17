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

    // Update delivery status
    $stmt = $conn->prepare("UPDATE delivery SET status = ? WHERE delivery_id = ? AND delivery_staff_id = ?");
    $stmt->bind_param("sii", $status, $delivery_id, $_SESSION['staff_id']);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update status.";
    }

    header("Location: deliv-staff-dash.php");
    exit;
}
?>
