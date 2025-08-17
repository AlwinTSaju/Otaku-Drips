<?php
session_start();
require 'db.php'; // database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = trim($_POST['email']); // can be email or name
    $password = trim($_POST['password']);

    // ----------------------------
    // Check Admin
    // ----------------------------
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? OR name = ?");
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            $_SESSION['admin_id'] = $user['admin_id'];
            $_SESSION['admin_name'] = $user['name'];
            header("Location: admin-dashboard.php");
            exit;
        }
    }

    // ----------------------------
    // Check Customer
    // ----------------------------
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ? OR name = ?");
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            $_SESSION['customer_id'] = $user['customer_id'];
            $_SESSION['customer_name'] = $user['name'];
            header("Location: home.php");
            exit;
        }
    }

    // ----------------------------
    // Check Delivery Staff (name only)
    // ----------------------------
    $stmt = $conn->prepare("SELECT * FROM delivery_staff WHERE name = ?");
    $stmt->bind_param("s", $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            $_SESSION['staff_id'] = $user['staff_id'];
            $_SESSION['staff_name'] = $user['name'];
            header("Location: deliv-staff-dash.php");
            exit;
        }
    }

    // ----------------------------
    // Invalid login
    // ----------------------------
    $_SESSION['error'] = "Invalid username/email or password.";
    header("Location: login.php");
    exit;
}
?>
