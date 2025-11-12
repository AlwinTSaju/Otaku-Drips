<?php
// ... (PHP code for session start, db require, redirects, and product fetching remains the same)

session_start();
require '../db.php'; // Includes $conn (mysqli)

// Redirect if not logged in admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all products
$products = [];
$sql = "SELECT product_id, name, price, stock, category, image FROM product ORDER BY product_id DESC";
$res = $conn->query($sql);
while ($r = $res->fetch_assoc()) {
    $products[] = $r;
}

// Fetch distinct categories for the dropdown in the modal (better practice)
$categories = [];
$cat_res = $conn->query("SELECT DISTINCT category FROM product WHERE category IS NOT NULL AND category != ''");
while ($r = $cat_res->fetch_assoc()) {
    $categories[] = $r['category'];
}

// Flash messages
$flash_success = $_SESSION['success'] ?? null;
$flash_error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Products | Admin</title>
<link rel="stylesheet" href="../styles/admin-dash.css">
<link rel="stylesheet" href="../styles/products.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
/* Added style to make the action buttons align better within the form (from previous revision) */
.action-form-group {
    display: flex;
    gap: 8px; /* Space between buttons */
}
</style>
</head>
<body>
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <?php include 'topbar.php'; ?>

            <div class="page-header-wrap">
                <h2 class="page-title">Product Catalog</h2>
            </div>
            
            <div class="data-table-card">
                <?php if ($flash_success): ?>
                    <div class="notice success"><?php echo htmlspecialchars($flash_success); ?></div>
                <?php endif; ?>
                <?php if ($flash_error): ?>
                    <div class="notice error"><?php echo htmlspecialchars($flash_error); ?></div>
                <?php endif; ?>

                <div class="action-bar">
                    <div class="filter-group">
                        <label for="categoryFilter">Filter by Category:</label>
                        <select id="categoryFilter" class="data-select">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive-wrap">
                    <table class="data-table" id="productsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price (₹)</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $p): ?>
                                <tr class="data-row">
                                    <td>#<?php echo $p['product_id']; ?></td>
                                    <td><img src="<?php echo htmlspecialchars(str_replace('./', '../', $p['image'] ?? 'placeholder.jpg')); ?>" alt="Product Image" class="product-list-thumb"></td>
                                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td><?php echo htmlspecialchars($p['category']); ?></td>
                                    <td><?php echo number_format($p['price'], 2); ?></td>
                                    <td><span class="stock-info <?php echo $p['stock'] < 10 ? 'low-stock' : 'in-stock'; ?>"><?php echo intval($p['stock']); ?></span></td>
                                    <td>
                                        <div class="action-form-group">
                                            <form method="GET" action="edit-product.php">
                                                <input type="hidden" name="product_id" value="<?php echo $p['product_id']; ?>">
                                                <button type="submit" class="btn small primary">Edit</button>
                                            </form>
                                            <form method="POST" action="delete-product.php" onsubmit="return confirm('Are you sure you want to delete product #<?php echo $p['product_id']; ?>? This cannot be undone.');">
                                                <input type="hidden" name="product_id" value="<?php echo $p['product_id']; ?>">
                                                <button type="submit" class="btn small delete-btn">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="addProductModal" class="modal">
        <div class="modal-inner">
            <h3 class="modal-title">Add New Product</h3>
            <form method="POST" action="add-product-raw.php"> 
                <div class="form-grid">
                    <div class="form-group">
                        <label for="new_name">Product Name</label>
                        <input type="text" id="new_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="new_category">Category</label>
                        <select id="new_category" name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                            <option value="New Category">-- Add New --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="new_original_price">Original Price (₹)</label>
                        <input type="number" id="new_original_price" name="original_price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="new_price">Selling Price (₹)</label>
                        <input type="number" id="new_price" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="new_stock">Initial Stock</label>
                        <input type="number" id="new_stock" name="stock" required>
                    </div>
                    <div class="form-group">
                        <label for="new_image_path">Image Path</label>
                        <input type="text" id="new_image_path" name="image" placeholder="eg: ./images/product/zoro.png" required>
                    </div>
                    <div class="form-group full-width">
                        <label for="new_description">Description</label>
                        <textarea id="new_description" name="description" required></textarea>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn" id="closeAddProductModal">Cancel</button>
                    <button type="submit" class="btn primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
    
<script src="../scripts/dark-mode.js"></script>
<script>
    // Modal toggle logic (Add Product)
    (function(){
        const modal = document.getElementById('addProductModal');
        const openBtn = document.getElementById('openAddProductModal');
        const closeBtn = document.getElementById('closeAddProductModal');
        if (openBtn && modal) openBtn.addEventListener('click', () => modal.classList.add('is-open'));
        if (closeBtn && modal) closeBtn.addEventListener('click', () => modal.classList.remove('is-open'));
        if (modal) modal.addEventListener('click', (e) => { if (e.target.classList.contains('modal')) modal.classList.remove('is-open'); });
    })();

    // Product filtering by category
    (function(){
        const categorySelect = document.getElementById('categoryFilter');
        const table = document.getElementById('productsTable');
        if (!categorySelect || !table) return;

        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.rows);

        function applyCategoryFilter() {
            const selected = categorySelect.value.trim();
            rows.forEach(row => {
                // category is in the 4th cell (0-based index 3)
                const catCell = row.cells[3];
                const cat = catCell ? catCell.textContent.trim() : '';
                if (!selected || selected === '' || selected === cat) row.style.display = '';
                else row.style.display = 'none';
            });
        }

        // Apply on change and on load (in case a category was preselected)
        categorySelect.addEventListener('change', applyCategoryFilter);
        applyCategoryFilter();
    })();
</script>
</body>
</html>