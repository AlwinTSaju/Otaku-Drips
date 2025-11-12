<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: delivery-staff-list.php"); exit; }

$staff_id = intval($_POST['staff_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$vehicle = trim($_POST['vehicle_info'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($staff_id === 0 || !$name || !$phone) {
    $_SESSION['error'] = "Invalid staff data provided.";
    header("Location: edit-staff.php?id={$staff_id}");
    exit;
}

// Build update statement; include password only if provided
if ($password !== '') {
    $sql = "UPDATE delivery_staff SET name = ?, phone = ?, vehicle_info = ?, password = ? WHERE staff_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $name, $phone, $vehicle, $password, $staff_id);
} else {
    $sql = "UPDATE delivery_staff SET name = ?, phone = ?, vehicle_info = ? WHERE staff_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $name, $phone, $vehicle, $staff_id);
}

$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    $_SESSION['success'] = "Delivery staff updated successfully.";
} else {
    $_SESSION['error'] = "Failed to update staff: " . $conn->error;
}

header("Location: delivery-staff-list.php");
exit;
