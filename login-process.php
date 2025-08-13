<?php
session_start();
require 'db.php'; // adjust path if needed

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check Admin
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $adminResult = $stmt->get_result();

    if ($adminResult->num_rows === 1) {
        $admin = $adminResult->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['name'];
            header("Location: admin-dashboard.php");
            exit;
        }
    }

    // Check Customer
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $custResult = $stmt->get_result();

    if ($custResult->num_rows === 1) {
        $cust = $custResult->fetch_assoc();
        if (password_verify($password, $cust['password'])) {
            $_SESSION['customer_id'] = $cust['customer_id'];
            $_SESSION['customer_name'] = $cust['name'];
            header("Location: index.php");
            exit;
        }
    }

    // Invalid login
    $_SESSION['error'] = "Invalid email or password.";
    header("Location: login.php");
    exit;
}
?>
