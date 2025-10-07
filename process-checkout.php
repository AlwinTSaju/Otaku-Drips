<?php
session_start();
require 'db.php';

if (!isset($_SESSION['customer_id']) || empty($_SESSION['cart'])) {
    header('Location: shop.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];
$cart = $_SESSION['cart'];
$grand_total = $_POST['grand_total'];

// 1. Insert into orders table
$stmt = $conn->prepare("INSERT INTO orders (customer_id, order_date, status) VALUES (?, NOW(), 'Processing')");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$order_id = $stmt->insert_id;

// 2. Insert each cart item into order_item table
$stmt_item = $conn->prepare("INSERT INTO order_item (order_id, product_id, quantity) VALUES (?, ?, ?)");
foreach ($cart as $item) {
    $stmt_item->bind_param("iii", $order_id, $item['product_id'], $item['qty']);
    $stmt_item->execute();
}

// 3. Clear cart
$_SESSION['cart'] = [];

// 4. Redirect to order success page
header("Location: order-success.php?order_id=$order_id");
exit;
