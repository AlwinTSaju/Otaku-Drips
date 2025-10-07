<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $original_price = floatval($_POST['original_price']);
    $stock = intval($_POST['stock']);
    $category = trim($_POST['category']);
    $image = trim($_POST['image']);

    if ($name && $price && $stock && $category && $image) {
        $stmt = $conn->prepare("
            INSERT INTO product (name, description, price, original_price, stock, category, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssddiss", $name, $description, $price, $original_price, $stock, $category, $image);

        if ($stmt->execute()) {
            $success = "Product added successfully!";
        } else {
            $error = "Failed to add product.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - Admin</title>
    <link rel="stylesheet" href="styles/admin.css">
    <link rel="stylesheet" href="styles/home.css">
    <style>
        .admin-menu li a.active {
            color: #fff;
            background-color: #e6b800;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .admin-table th {
            background-color: #f7f7f7;
            color: #e6b800;
        }

        .admin-table tr:hover {
            background-color: #fafafa;
        }

        form select {
            padding: 5px;
        }
        
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #e60000;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .form-group textarea {
            resize: vertical;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #e6b800;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        .submit-btn:hover {
            background-color: #ffcc00;
        }
        .notification {
            margin-bottom: 1rem;
            padding: 10px;
            border-radius: 5px;
            font-weight: 500;
            text-align: center;
        }
        .success {
            background-color: #e6f9ed;
            color: #2d7a3e;
            border-left: 4px solid #2d7a3e;
        }
        .error {
            background-color: #fdecea;
            color: #a4262c;
            border-left: 4px solid #a4262c;
        }
    </style>
</head>
<body>
    <header>
    <nav>
        <ul class="main-menu admin-menu">
            <li><a href="admin-dashboard.php">View Orders</a></li>
            <li><a href="add-product.php" class="active">Add Product</a></li>
            <li><a href="view-products.php">View Products</a></li>
        </ul>
        <div class="user-options">
            <span style="color:white;">Hi, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> (Admin)</span>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
</header>

<main class="form-container">
    <h2>Add New Product</h2>

    <?php if ($success): ?>
        <div class="notification success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="notification error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Product Name*</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price (₹)*</label>
            <input type="number" step="0.01" id="price" name="price" required>
        </div>

        <div class="form-group">
            <label for="original_price">Original Price (₹)</label>
            <input type="number" step="0.01" id="original_price" name="original_price">
        </div>

        <div class="form-group">
            <label for="stock">Stock*</label>
            <input type="number" id="stock" name="stock" required>
        </div>

        <div class="form-group">
            <label for="category">Category*</label>
            <input type="text" id="category" name="category" required>
        </div>

        <div class="form-group">
            <label for="image">Image Path*</label>
            <input type="text" id="image" name="image" placeholder="./images/products/example.png" required>
        </div>

        <button type="submit" class="submit-btn">Add Product</button>
    </form>
</main>
</body>
</html>
