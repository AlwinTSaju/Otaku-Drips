<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = intval($_GET['order_id'] ?? 0);
if (!$order_id) {
    $_SESSION['error'] = 'Invalid order id';
    header('Location: orders.php');
    exit;
}

// Fetch order, customer, payment and delivery info
$stmt = $conn->prepare("SELECT o.order_id, o.customer_id, o.order_date, o.status,
    IFNULL(p.amount,0) AS amount,
    c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone, c.address AS customer_address,
    d.delivery_id, d.delivery_staff_id, ds.name AS staff_name
    FROM orders o
    LEFT JOIN payment p ON o.order_id = p.order_id
    LEFT JOIN customer c ON o.customer_id = c.customer_id
    LEFT JOIN delivery d ON o.order_id = d.order_id
    LEFT JOIN delivery_staff ds ON d.delivery_staff_id = ds.staff_id
    WHERE o.order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$res = $stmt->get_result();
$order = $res->fetch_assoc();
$stmt->close();

if (!$order) {
    $_SESSION['error'] = 'Order not found';
    header('Location: orders.php');
    exit;
}

// Fetch order items with product info
$items = [];
$stmt = $conn->prepare("SELECT oi.order_item_id, oi.product_id, oi.quantity, p.name AS product_name, p.price AS product_price
    FROM order_item oi
    LEFT JOIN product p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) $items[] = $r;
$stmt->close();

// compute totals
$items_total = 0.0;
foreach ($items as $it) {
    $price = isset($it['product_price']) ? (float)$it['product_price'] : 0.0;
    $items_total += $price * (int)$it['quantity'];
}

$flash_success = $_SESSION['success'] ?? null;
$flash_error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Order #<?php echo intval($order['order_id']); ?> | Admin</title>
<link rel="stylesheet" href="../styles/admin-dash.css">
<link rel="stylesheet" href="../styles/products.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <?php include 'topbar.php'; ?>

            <div class="page-header-wrap">
                <h2 class="page-title">Order #<?php echo intval($order['order_id']); ?></h2>
                <a href="orders.php" class="btn">Back to Orders</a>
            </div>

            <?php if ($flash_success): ?> <div class="notice success"><?php echo htmlspecialchars($flash_success); ?></div> <?php endif; ?>
            <?php if ($flash_error): ?> <div class="notice error"><?php echo htmlspecialchars($flash_error); ?></div> <?php endif; ?>

            <div class="card">
                <h3>Summary</h3>
                <p><strong>Order ID:</strong> #<?php echo intval($order['order_id']); ?></p>
                <p><strong>Date:</strong> <?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                <p><strong>Amount (payment):</strong> ₹ <?php echo number_format((float)$order['amount'], 2); ?></p>
            </div>

            <div class="card">
                <h3>Customer</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name'] ?? ''); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email'] ?? ''); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone'] ?? ''); ?></p>
                <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($order['customer_address'] ?? '')); ?></p>
            </div>

            <div class="card">
                <h3>Items</h3>
                <div class="orders-table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price (₹)</th>
                                <th>Quantity</th>
                                <th>Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $it): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($it['product_name'] ?? ''); ?></td>
                                <td>₹ <?php echo number_format((float)($it['product_price'] ?? 0), 2); ?></td>
                                <td><?php echo intval($it['quantity']); ?></td>
                                <td>₹ <?php echo number_format((float)($it['product_price'] ?? 0) * (int)$it['quantity'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align:right"><strong>Items total</strong></td>
                                <td>₹ <?php echo number_format($items_total, 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card">
                <h3>Delivery</h3>
                <?php if (!empty($order['delivery_id'])): ?>
                    <p><strong>Assigned to:</strong> <?php echo htmlspecialchars($order['staff_name'] ?? ''); ?></p>
                <?php else: ?>
                    <p><em>No delivery assigned</em></p>
                <?php endif; ?>
            </div>

        </main>
    </div>
</body>
</html>
