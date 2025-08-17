<?php
session_start();
include 'db.php'; // your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_SESSION['customer_id']??5;
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $size = $_POST['size'];

    // 1. Insert into orders table
    $status = "Pending";
    $sql = "INSERT INTO orders (customer_id, status) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $customer_id, $status);
    $stmt->execute();

    // Get the newly created order_id
    $order_id = $stmt->insert_id;

    // 2. Insert into order_item
    $sql2 = "INSERT INTO order_item (order_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("iii", $order_id, $product_id, $quantity);
    $stmt2->execute();

    echo "✅ Order placed successfully! Order ID: " . $order_id;
}
?>
