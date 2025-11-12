<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: Admin/login.php");
    exit;
}

// Count total orders in the database
$count_res = $conn->query("SELECT COUNT(*) as cnt FROM orders");
$count_row = $count_res->fetch_assoc();
$total_orders = $count_row['cnt'];

// Count distinct order IDs
$distinct_res = $conn->query("SELECT COUNT(DISTINCT order_id) as cnt FROM orders");
$distinct_row = $distinct_res->fetch_assoc();
$distinct_orders = $distinct_row['cnt'];

// Check for duplicates
$duplicates_res = $conn->query("
    SELECT order_id, COUNT(*) as cnt 
    FROM orders 
    GROUP BY order_id 
    HAVING cnt > 1
");

$has_duplicates = $duplicates_res->num_rows > 0;
$duplicates = [];
while ($row = $duplicates_res->fetch_assoc()) {
    $duplicates[] = $row;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Debug: Orders Database Check</title>
    <link rel="stylesheet" href="styles/admin-dash.css">
</head>
<body>
    <div class="app-shell">
        <main class="main-content" style="padding: 20px;">
            <h1>Database Debug Check</h1>
            
            <div class="card">
                <h3>Orders Table Analysis</h3>
                <p><strong>Total rows in orders table:</strong> <?php echo $total_orders; ?></p>
                <p><strong>Distinct order_id values:</strong> <?php echo $distinct_orders; ?></p>
                <p><strong>Has duplicates:</strong> <?php echo $has_duplicates ? 'YES' : 'NO'; ?></p>
            </div>

            <?php if ($has_duplicates): ?>
                <div class="card" style="background: #ff4d4f; color: #fff;">
                    <h3>Duplicate Order IDs Found:</h3>
                    <ul>
                        <?php foreach ($duplicates as $dup): ?>
                            <li>Order #<?php echo $dup['order_id']; ?> appears <?php echo $dup['cnt']; ?> times</li>
                        <?php endforeach; ?>
                    </ul>
                    <p><strong>Action:</strong> These duplicates are causing the 64 order count. You need to delete duplicate rows from the orders table.</p>
                </div>
            <?php else: ?>
                <div class="card" style="background: #2ecc71; color: #fff;">
                    <h3>No duplicates found</h3>
                    <p>The orders table is clean. The duplication might be in the payments or delivery tables.</p>
                </div>
            <?php endif; ?>

            <div class="card">
                <h3>Payment Table Analysis</h3>
                <?php
                    $p_count = $conn->query("SELECT COUNT(*) as cnt FROM payment")->fetch_assoc()['cnt'];
                    $p_distinct = $conn->query("SELECT COUNT(DISTINCT order_id) as cnt FROM payment")->fetch_assoc()['cnt'];
                ?>
                <p><strong>Total payment rows:</strong> <?php echo $p_count; ?></p>
                <p><strong>Distinct order_ids in payments:</strong> <?php echo $p_distinct; ?></p>
            </div>

            <div class="card">
                <h3>Delivery Table Analysis</h3>
                <?php
                    $d_count = $conn->query("SELECT COUNT(*) as cnt FROM delivery")->fetch_assoc()['cnt'];
                    $d_distinct = $conn->query("SELECT COUNT(DISTINCT order_id) as cnt FROM delivery")->fetch_assoc()['cnt'];
                ?>
                <p><strong>Total delivery rows:</strong> <?php echo $d_count; ?></p>
                <p><strong>Distinct order_ids in delivery:</strong> <?php echo $d_distinct; ?></p>
            </div>

            <div style="margin-top: 20px;">
                <a href="Admin/orders.php" class="btn">Back to Orders</a>
            </div>
        </main>
    </div>
</body>
</html>
