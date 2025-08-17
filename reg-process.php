<?php
session_start();
require 'db.php'; // database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate password match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO customer (name, email, password, address, phone) VALUES (?, ?, ?, '', '')");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        // Get the inserted user ID
        $customer_id = $stmt->insert_id;

        // Log the user in
        $_SESSION['customer_id'] = $customer_id;
        $_SESSION['customer_name'] = $name;

        // Redirect back to where they came from (if set)
        if (!empty($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $_SESSION['error'] = "Error creating account. Please try again.";
        header("Location: register.php");
        exit;
    }
}
?>
