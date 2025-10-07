<?php
session_start();
require 'db.php';

// Get input
$input = json_decode(file_get_contents("php://input"), true);
$order_id = intval($input['order_id'] ?? 0);
$email = trim($input['email'] ?? '');

if (!$order_id || !$email) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

// Check order + email
$query = $conn->prepare("
    SELECT o.order_id, o.order_date, o.status, oi.product_id, oi.quantity
    FROM orders o
    LEFT JOIN order_item oi ON o.order_id = oi.order_id
    JOIN customer c ON o.customer_id = c.customer_id
    WHERE o.order_id = ? AND c.email = ?
");
$query->bind_param("is", $order_id, $email);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No order found with given details"]);
    exit;
}

$orderData = [];
$items = [];

while ($row = $result->fetch_assoc()) {
    $orderData['order_id'] = $row['order_id'];
    $orderData['order_date'] = $row['order_date'];
    $orderData['status'] = $row['status'];
    $items[] = [
        "product_id" => $row['product_id'],
        "quantity" => $row['quantity']
    ];
}

$orderData['items'] = $items;
$orderData['success'] = true;

echo json_encode($orderData);
