<?php
require 'db.php';

if (isset($_GET['id'])) {
    // Single product fetch
    $productId = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT product_id, name, description, price, stock, category, image, original_price FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc() ?: ["error" => "Product not found"]);
    exit;
}

if (isset($_GET['query'])) {
    // Search multiple products
    $query = strtolower(trim($_GET['query']));
    $searchTerm = "%$query%";
    $stmt = $conn->prepare("SELECT product_id, name, description, price, stock, category, image, original_price 
                            FROM product 
                            WHERE LOWER(name) LIKE ? 
                               OR LOWER(category) LIKE ? 
                               OR LOWER(description) LIKE ?");
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode($products);
    exit;
}
