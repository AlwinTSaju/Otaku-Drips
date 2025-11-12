<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$product_id = intval($_GET['product_id'] ?? 0);

if ($product_id === 0) {
    $_SESSION['error'] = "No product selected for editing.";
    header("Location: products.php");
    exit;
}

// Fetch the product details
$product = null;
$sql = "SELECT * FROM product WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();
$stmt->close();

if (!$product) {
    $_SESSION['error'] = "Product #{$product_id} not found.";
    header("Location: products.php");
    exit;
}

// NOTE: In a real app, you would fetch all distinct categories for the dropdown
$categories = ['T-Shirt', 'Hoodie', 'Accessories', 'Manga', 'Figure']; 

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Product #<?php echo $product_id; ?> | Admin</title>
    <link rel="stylesheet" href="../styles/admin-dash.css"> 
    <link rel="stylesheet" href="../styles/edit-product.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <h1 class="brand-title">OTAKU DRIPS</h1>
            </div>
            <nav class="sidebar-nav">
                <a href="admin-dashboard.php" class="nav-link">Overview</a>
                <a href="products.php" class="nav-link active">Products</a>
                <a href="customers.php" class="nav-link">Customers</a>
                <a href="orders.php" class="nav-link">Orders</a>
                <a href="delivery-staff-list.php" class="nav-link">Delivery Staff</a>
                <a href="reports.php" class="nav-link">Reports</a>
                <a href="settings.php" class="nav-link">Settings</a>
            </nav>
            <div class="sidebar-bottom">
                <button id="darkModeToggle" class="btn-toggle">Dark Mode</button>
                <a href="../logout.php" class="btn-logout">Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="search">
                    <h2 style="color: #fff; font-size: 1.2em;">Editing Product ID: #<?php echo $product['product_id']; ?></h2>
                </div>
                <div class="top-actions">
                    <a href="products.php" class="btn small">Back to Products</a>
                    <div class="user-welcome">Hi, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> (Admin)</div>
                </div>
            </header>

            <div class="page-header-wrap">
                <h2 class="page-title">Edit Product Details</h2>
            </div>

            <div class="data-form-card">
                <form method="POST" action="update-product-raw.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    
                    <div class="image-preview">
                        <img src="<?php echo htmlspecialchars(str_replace('./', '../', $product['image'] ?? 'placeholder.jpg')); ?>" alt="Current Product Image" class="current-image">
                        <div>
                            <p style="color: #ddd; margin: 0 0 5px 0;">Image Path</p>
                            <input type="text" name="image" value="<?php echo htmlspecialchars($product['image']); ?>" placeholder="eg: ./images/product/zoro.png" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($product['category'] === $cat) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="original_price">Original Price (₹)</label>
                            <input type="number" id="original_price" name="original_price" step="0.01" value="<?php echo $product['original_price']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">Selling Price (₹)</label>
                            <input type="number" id="price" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" id="stock" name="stock" value="<?php echo $product['stock']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                             </div>
                        
                        <div class="form-group full-width">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <a href="products.php" class="btn">Cancel</a>
                        <button type="submit" class="btn primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
<script src="../scripts/dark-mode.js"></script> 
</body>
</html>