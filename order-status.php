<?php
session_start();
require 'db.php';

// Get inputs from URL
$order_id = intval($_GET['order_id'] ?? 0);
$email = trim($_GET['email'] ?? '');

if (!$order_id || !$email) {
    die("Invalid order details.");
}

// Fetch order + customer
$stmt = $conn->prepare("
    SELECT o.order_id, o.order_date, o.status, c.name AS customer_name
    FROM orders o
    JOIN customer c ON o.customer_id = c.customer_id
    WHERE o.order_id = ? AND c.email = ?
");
$stmt->bind_param("is", $order_id, $email);
$stmt->execute();
$orderResult = $stmt->get_result();

if ($orderResult->num_rows === 0) {
    die("No order found for the given ID and email.");
}

$order = $orderResult->fetch_assoc();

// Fetch order items
$itemStmt = $conn->prepare("
    SELECT oi.product_id, oi.quantity, p.name AS product_name, p.price
    FROM order_item oi
    JOIN product p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");
$itemStmt->bind_param("i", $order_id);
$itemStmt->execute();
$itemsResult = $itemStmt->get_result();

// Determine progress steps
$statuses = ['pending' => 1, 'shipped' => 2, 'delivered' => 3];
$currentStep = $statuses[strtolower($order['status'])] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?php echo $order['order_id']; ?> - Otaku Drips</title>
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/shop.css">
    <style>
        body { background-color: #f5f5f5; font-family: sans-serif; }
        .order-container { max-width: 800px; margin: 3rem auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        h2 { text-align: center; margin-bottom: 1rem; }
        .order-info, .order-items { margin-bottom: 1.5rem; }
        .order-items table { width: 100%; border-collapse: collapse; }
        .order-items th, .order-items td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        .progress-container { display: flex; justify-content: space-between; margin: 2rem 0; position: relative; }
        .progress-container::before { content: ''; position: absolute; top: 15px; left: 0; width: 100%; height: 4px; background-color: #ddd; z-index: 1; }
        .progress-step { position: relative; z-index: 2; width: 30px; height: 30px; background-color: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #fff; }
        .progress-step.active { background-color: #e6b800; }
        .progress-labels { display: flex; justify-content: space-between; margin-top: 0.5rem; font-weight: bold; font-size: 0.9rem; }
        .status-text { text-align: center; font-weight: bold; margin-top: 1rem; color: #333; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="order-container">
    <h2>Order #<?php echo $order['order_id']; ?></h2>

    <div class="order-info">
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>Order Date:</strong> <?php echo date("d M Y", strtotime($order['order_date'])); ?></p>
        <p class="status-text"><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
    </div>

    <!-- Progress Bar -->
    <div class="progress-container">
        <div class="progress-step <?php echo $currentStep >= 1 ? 'active' : ''; ?>">1</div>
        <div class="progress-step <?php echo $currentStep >= 2 ? 'active' : ''; ?>">2</div>
        <div class="progress-step <?php echo $currentStep >= 3 ? 'active' : ''; ?>">3</div>
    </div>
    <div class="progress-labels">
        <span>Pending</span>
        <span>Shipped</span>
        <span>Delivered</span>
    </div>

    <div class="order-items">
        <h3>Products in this order</h3>
        <?php if ($itemsResult->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Product ID</th>
                        <th>Price</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $itemsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['product_id']; ?></td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products found for this order.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
