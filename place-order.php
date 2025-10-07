<?php
session_start();
require 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_SESSION['customer_id'];
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment_method'];
    $total = $_POST['total'];

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, fullname, address, city, phone, payment_method, total, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isssssd", $customer_id, $fullname, $address, $city, $phone, $payment_method, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    $cartQuery = $conn->prepare("SELECT c.product_id, c.size, c.qty, p.price FROM cart c JOIN product p ON c.product_id = p.product_id WHERE c.customer_id = ?");
    $cartQuery->bind_param("i", $customer_id);
    $cartQuery->execute();
    $result = $cartQuery->get_result();

    while ($row = $result->fetch_assoc()) {
        $insertItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, size, qty, price) VALUES (?, ?, ?, ?, ?)");
        $insertItem->bind_param("iisid", $order_id, $row['product_id'], $row['size'], $row['qty'], $row['price']);
        $insertItem->execute();
    }

    // Clear cart
    $deleteCart = $conn->prepare("DELETE FROM cart WHERE customer_id = ?");
    $deleteCart->bind_param("i", $customer_id);
    $deleteCart->execute();

    header("Location: order-success.php?id=$order_id");
    exit;
}
?>
