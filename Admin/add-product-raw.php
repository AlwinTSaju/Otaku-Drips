<?php
session_start();
require '../db.php';

// Security check: Only logged-in admins can access this script
if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit; 
}

// Ensure the request is a POST submission from the form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    header("Location: products.php"); 
    exit; 
}

// 1. Gather and sanitize input
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$original_price = floatval($_POST['original_price'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
// Get image path directly from the POST data
$image_path = trim($_POST['image'] ?? './placeholder.jpg'); 

// 2. Simple validation
if (!$name || !$description || !$category || $price <= 0 || $stock < 0 || !$image_path) {
    $_SESSION['error'] = "All required fields (Name, Price, Stock, Category, Description, and Image Path) must be filled correctly.";
    header("Location: products.php");
    exit;
}

// 3. Insert Database Record
$sql = "INSERT INTO product (name, description, price, stock, category, image, original_price) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'ssdisss', 
    $name, 
    $description, 
    $price, 
    $stock, 
    $category, 
    $image_path, // Uses the raw path from the text input
    $original_price
);

$ok = $stmt->execute();
$new_product_id = $conn->insert_id;
$stmt->close();

// 4. Set flash message and redirect
if ($ok) {
    $_SESSION['success'] = "🎉 New product **'{$name}'** (#{$new_product_id}) added successfully with path: `{$image_path}`!";
} else {
    $_SESSION['error'] = "❌ Error adding product to the database: " . $conn->error;
}

header("Location: products.php");
exit;