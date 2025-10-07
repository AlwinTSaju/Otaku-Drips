<?php
session_start();
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$productId = $data['product_id'] ?? null;
$size      = $data['size'] ?? null;
$qty       = isset($data['qty']) ? (int)$data['qty'] : 1;

if (!$productId || !$size) {
    echo json_encode(["success" => false, "msg" => "missing fields"]);
    exit;
}

$stmt = $conn->prepare("SELECT product_id, name, price, image FROM product WHERE product_id = ?");
$stmt->bind_param("s", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    echo json_encode(["success" => false, "msg" => "product not found"]);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] === $productId && $item['size'] === $size) {
        $item['qty'] += $qty;
        $found = true;
        break;
    }
}
unset($item);

if (!$found) {
    $_SESSION['cart'][] = [
        "product_id" => $product['product_id'],
        "name"       => $product['name'],
        "price"      => $product['price'],
        "image"      => $product['image'],
        "size"       => $size,
        "qty"        => $qty
    ];
}

$cart_count = array_sum(array_column($_SESSION['cart'], 'qty'));
echo json_encode(["success" => true, "cart_count" => $cart_count]);
