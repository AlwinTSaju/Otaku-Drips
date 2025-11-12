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
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$original_price = floatval($_POST['original_price'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$image_path = './placeholder.jpg'; // Default path if upload fails

if (!$name || !$description || !$category || $price <= 0 || $stock < 0) {
    $_SESSION['error'] = "All required fields must be filled correctly.";
    header("Location: products.php");
    exit;
}

// 2. Handle Image Upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "../uploads/products/"; // Save images outside admin folder
    
    // Ensure the directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Generate a unique file name
    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    // Use a temporary name first, we will rename it with the final product_id later, 
    // but for simplicity here, we'll use a time-based name.
    $new_file_name = "product_temp_" . time() . "_" . uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_file_name;

    // Check file size and type (optional but recommended)
    // if ($_FILES['image']['size'] > 500000) { /* error logic */ }

    // Move the uploaded file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Successful upload, update path to DB format (relative to your root)
        $image_path = str_replace('../', './', $target_file); 
    } else {
        $_SESSION['error'] = "Failed to upload image. Product created without image.";
        // We will continue to insert with placeholder path or stop insertion depending on requirement.
        // For now, we continue but set an error.
    }
} else if ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Catch other upload errors (like size limit)
    $_SESSION['error'] = "Image upload failed with error code: " . $_FILES['image']['error'];
    header("Location: products.php");
    exit;
}

// 3. Insert Database
$sql = "INSERT INTO product (name, description, price, stock, category, image, original_price) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'ssdisss', 
    $name, 
    $description, 
    $price, 
    $stock, 
    $category, 
    $image_path, 
    $original_price
);

$ok = $stmt->execute();
$new_product_id = $conn->insert_id; // Get the ID of the new product
$stmt->close();

if ($ok) {
    // OPTIONAL: If using time-based filename, you might want to rename the file using the final $new_product_id
    // This step is omitted for simplicity but is good practice.

    $_SESSION['success'] = "New product '{$name}' (#{$new_product_id}) added successfully!";
} else {
    $_SESSION['error'] = "Error adding product: " . $conn->error;
}

// 4. Redirect back to product list
header("Location: products.php");
exit;