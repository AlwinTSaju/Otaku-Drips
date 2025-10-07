<?php
session_start();
require 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product_id'])) {
    $delete_id = intval($_POST['delete_product_id']);
    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $_SESSION['success'] = "Product deleted successfully!";
    header("Location: view-products.php");
    exit;
}

// Fetch products
$productsQuery = $conn->prepare("SELECT product_id, name, category, price, original_price, stock, image FROM product ORDER BY name ASC");
$productsQuery->execute();
$products = $productsQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Products - Admin</title>
    <link rel="stylesheet" href="styles/deliv-staff.css">
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
    </style>
</head>
<body>
<header>
    <nav>
        <ul class="main-menu admin-menu">
            <li><a href="admin-dashboard.php">View Orders</a></li>
            <li><a href="add-product.php">Add Product</a></li>
            <li><a href="view-products.php" class="active">View Products</a></li>
        </ul>
        <div class="user-options">
            <span style="color:white;">Hi, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> (Admin)</span>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
</header>

<main class="dashboard-container">
    <h1>All Products</h1>

    <?php
    if (isset($_SESSION['success'])) { echo '<div class="dashboard-notification success">'.$_SESSION['success'].'</div>'; unset($_SESSION['success']); }
    if (isset($_SESSION['error'])) { echo '<div class="dashboard-notification error">'.$_SESSION['error'].'</div>'; unset($_SESSION['error']); }
    ?>

    <?php if ($products->num_rows > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price (₹)</th>
                    <th>Original Price (₹)</th>
                    <th>Stock</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $products->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['product_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['original_price'] ? number_format($row['original_price'],2) : '-'; ?></td>
                    <td><?php echo (int)$row['stock']; ?></td>
                    <td>
                        <?php if(!empty($row['image'])): ?>
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                        <?php else: echo '-'; endif; ?>
                    </td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Are you sure to delete this product?');">
                            <input type="hidden" name="delete_product_id" value="<?php echo $row['product_id']; ?>">
                            <button type="submit" style="background-color:#e60000;color:white;padding:4px 8px;border:none;border-radius:4px;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
</main>
</body>
</html>
