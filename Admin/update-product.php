<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit; 
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    header("Location: products.php"); 
    exit; 
}

// 1. Gather and sanitize input
$product_id = intval($_POST['product_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$original_price = floatval($_POST['original_price'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$current_image = trim($_POST['current_image'] ?? '');
$new_image_path = $current_image;

if ($product_id === 0 || !$name || !$category || $price <= 0 || $stock < 0) {
    $_SESSION['error'] = "Invalid product data provided.";
    header("Location: edit-product.php?product_id={$product_id}");
    exit;
}

// 2. Handle Image Upload
if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "../uploads/products/"; // Adjust path as necessary (outside of admin/ directory)
    // Create directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES['new_image']['name'], PATHINFO_EXTENSION);
    $new_file_name = "product_" . $product_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_file_name;

    // Move the uploaded file
    if (move_uploaded_file($_FILES['new_image']['tmp_name'], $target_file)) {
        // Successful upload, update path to DB format (relative to your root)
        $new_image_path = str_replace('../', './', $target_file); // Convert absolute path to relative DB path
        
        // OPTIONAL: Delete the old image file if it's not the default placeholder
        if ($current_image && $current_image !== './placeholder.jpg') {
             // In a real application, you'd add path checks to ensure it's safe to delete
             @unlink(str_replace('./', '../', $current_image)); 
        }

    } else {
        $_SESSION['error'] = "Failed to upload new image. Product not updated.";
        header("Location: edit-product.php?product_id={$product_id}");
        exit;
    }
}

// 3. Update Database
$sql = "UPDATE product SET 
            name = ?, 
            description = ?, 
            price = ?, 
            stock = ?, 
            category = ?, 
            image = ?, 
            original_price = ?
        WHERE product_id = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'ssdisssi', 
    $name, 
    $description, 
    $price, 
    $stock, 
    $category, 
    $new_image_path, 
    $original_price, 
    $product_id
);

$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    $_SESSION['success'] = "Product '{$name}' (#{$product_id}) updated successfully!";
} else {
    $_SESSION['error'] = "Error updating product: " . $conn->error;
}

// 4. Redirect back to product list
header("Location: products.php");
exit;