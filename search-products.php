<?php
require 'db.php';

$query = isset($_GET['query']) ? strtolower(trim($_GET['query'])) : '';

if (empty($query)) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT product_id, name, category, price, original_price, image, description 
        FROM product
        WHERE LOWER(name) LIKE ? 
           OR LOWER(category) LIKE ? 
           OR LOWER(description) LIKE ?";

$stmt = $conn->prepare($sql);
$searchTerm = "%$query%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
