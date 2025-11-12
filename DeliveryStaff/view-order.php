<?php
session_start();
require '../db.php';

// Delivery staff login check
if (!isset($_SESSION['staff_id'])) {
    header('Location: delivery-login.php');
    exit;
}

$order_id = intval($_GET['order_id'] ?? 0);
if (!$order_id) {
    $_SESSION['error'] = 'Invalid order id';
    header('Location: deliv-staff-dash.php');
    exit;
}

// Fetch order, customer, payment and delivery info
$stmt = $conn->prepare("SELECT o.order_id, o.customer_id, o.order_date, o.status,
    IFNULL(p.amount,0) AS amount,
    c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone, c.address AS customer_address,
    d.delivery_id, d.delivery_staff_id, d.status AS delivery_status, ds.name AS staff_name
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
    header('Location: deliv-staff-dash.php');
    exit;
}

// Verify the order is assigned to the current staff
if (empty($order['delivery_staff_id']) || $order['delivery_staff_id'] != $_SESSION['staff_id']) {
    $_SESSION['error'] = 'You do not have permission to view this order';
    header('Location: deliv-staff-dash.php');
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

// Fetch delivery staff info for topbar
$staffQuery = $conn->prepare("SELECT name FROM delivery_staff WHERE staff_id=?");
$staffQuery->bind_param('i', $_SESSION['staff_id']);
$staffQuery->execute();
$staffRes = $staffQuery->get_result();
$staffInfo = $staffRes->fetch_assoc();
$staffQuery->close();

$flash_success = $_SESSION['success'] ?? null;
$flash_error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order #<?php echo intval($order['order_id']); ?> | Delivery</title>
    <link rel="stylesheet" href="../styles/admin-dash.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
    <div class="app-shell">
        <!-- Delivery Staff Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <h1 class="brand-title">OTAKU DRIPS</h1>
            </div>

            <nav class="sidebar-nav">
                <a href="deliv-staff-dash.php" class="nav-link">Dashboard</a>
                <a href="delivery-profile.php" class="nav-link">Profile</a>
            </nav>

            <div class="sidebar-bottom">
            </div>
        </aside>

        <main class="main-content">
            <!-- Delivery Staff Topbar -->
            <header class="topbar">
                <div class="search">
                    <span style="color: #fff; font-weight: 600;">Order Details</span>
                </div>
                <div class="top-actions">
                    <button id="darkModeToggle" class="btn small" aria-pressed="false">Dark Mode</button>
                    <div class="user-welcome">Hi, <?php echo htmlspecialchars($staffInfo['name'] ?? 'Staff'); ?></div>
                    <a href="../logout.php" class="btn small" style="color: #ffcc00; border-color: rgba(255, 204, 0, 0.15);">Logout</a>
                </div>
            </header>

            <div class="page-header-wrap">
                <h2 class="page-title">Order #<?php echo intval($order['order_id']); ?></h2>
                <a href="deliv-staff-dash.php" class="btn">Back to Dashboard</a>
            </div>

            <?php if ($flash_success): ?> <div class="notice success"><?php echo htmlspecialchars($flash_success); ?></div> <?php endif; ?>
            <?php if ($flash_error): ?> <div class="notice error"><?php echo htmlspecialchars($flash_error); ?></div> <?php endif; ?>

            <div class="data-table-card">
                <div class="card-header">
                    <h3>Summary</h3>
                </div>
                <div style="padding: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Order ID</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;">#<?php echo intval($order['order_id']); ?></p>
                    </div>
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Date</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;"><?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></p>
                    </div>
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Order Status</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;"><?php echo htmlspecialchars($order['status']); ?></p>
                    </div>
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Delivery Status</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;"><?php echo htmlspecialchars($order['delivery_status'] ?? 'Not assigned'); ?></p>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Amount (Payment)</p>
                        <p style="color: #fff; font-weight: 600; margin: 0; font-size: 18px;">₹ <?php echo number_format((float)$order['amount'], 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="data-table-card" style="margin-top: 20px;">
                <div class="card-header">
                    <h3>Customer Information</h3>
                </div>
                <div style="padding: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Name</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;"><?php echo htmlspecialchars($order['customer_name'] ?? ''); ?></p>
                    </div>
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Email</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;"><?php echo htmlspecialchars($order['customer_email'] ?? ''); ?></p>
                    </div>
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Phone</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;"><?php echo htmlspecialchars($order['customer_phone'] ?? ''); ?></p>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Address</p>
                        <p style="color: #fff; font-weight: 600; margin: 0; word-wrap: break-word;"><?php echo nl2br(htmlspecialchars($order['customer_address'] ?? '')); ?></p>
                    </div>
                </div>
            </div>

            <div class="data-table-card" style="margin-top: 20px;">
                <div class="card-header">
                    <h3>Order Items</h3>
                </div>
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
                                <td><strong>₹ <?php echo number_format($items_total, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="data-table-card" style="margin-top: 20px;">
                <div class="card-header">
                    <h3>Delivery Information</h3>
                </div>
                <div style="padding: 15px; display: grid; grid-template-columns: 1fr; gap: 20px;">
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Assigned Delivery Staff</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;"><?php echo htmlspecialchars($order['staff_name'] ?? 'You'); ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../scripts/dark-mode.js"></script>
</body>
</html>
