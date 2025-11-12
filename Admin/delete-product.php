<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: products.php"); exit; }

$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id === 0) {
    $_SESSION['error'] = "Invalid product ID.";
    header("Location: products.php");
    exit;
}

// In a real application, you would check dependencies (order_item) and then delete.
// For now, simple deletion:
$sql = "DELETE FROM product WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $product_id);
$ok = $stmt->execute();
$stmt->close();

if ($ok) $_SESSION['success'] = "Product #{$product_id} successfully deleted.";
else $_SESSION['error'] = "Failed to delete product #{$product_id}. It may be referenced in existing orders.";

header("Location: products.php");
exit;