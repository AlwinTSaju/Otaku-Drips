<?php
session_start();
require '../db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: admin-dashboard.php"); exit; }

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$vehicle = trim($_POST['vehicle_info'] ?? '');
$password = trim($_POST['password'] ?? '');

if (!$name || !$phone || !$password) {
    $_SESSION['error'] = "Name, phone and password are required.";
    header("Location: admin-dashboard.php");
    exit;
}

// hash password (simple)
$stmt = $conn->prepare("INSERT INTO delivery_staff (name, phone, vehicle_info, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $name, $phone, $vehicle, $password);
$ok = $stmt->execute();
$stmt->close();


if ($ok) $_SESSION['success'] = "Delivery staff added.";
else $_SESSION['error'] = "Failed to add staff.";
header("Location: admin-dashboard.php");
exit;
