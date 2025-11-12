<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$customer_id = intval($_GET['id'] ?? 0);
if ($customer_id === 0) {
    $_SESSION['error'] = "No customer selected.";
    header("Location: customers.php");
    exit;
}

// Fetch customer details
$sql = "SELECT customer_id, name, email, phone, address FROM customer WHERE customer_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$res = $stmt->get_result();
$customer = $res->fetch_assoc();
$stmt->close();

if (!$customer) {
    $_SESSION['error'] = "Customer #{$customer_id} not found.";
    header("Location: customers.php");
    exit;
}

// Fetch customer orders
$orders = [];
$order_sql = "
    SELECT o.order_id, o.order_date, o.status, IFNULL(p.amount, 0) AS amount
    FROM orders o
    LEFT JOIN payment p ON o.order_id = p.order_id
    WHERE o.customer_id = ?
    ORDER BY o.order_date DESC
";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) $orders[] = $r;
$stmt->close();

// Calculate customer stats
$total_spent = 0;
$total_orders = count($orders);
foreach ($orders as $o) {
    $total_spent += floatval($o['amount']);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Customer Details #<?php echo $customer_id; ?> | Admin</title>
    <link rel="stylesheet" href="../styles/admin-dash.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <?php include 'topbar.php'; ?>

            <div class="page-header-wrap">
                <h2 class="page-title">Customer Details</h2>
                <a href="customers.php" class="btn">Back to Customers</a>
            </div>

            <div class="data-table-card">
                <div class="card-header">
                    <h3>Customer Information</h3>
                </div>

                <div style="padding: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Customer ID</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;">#{<?php echo $customer['customer_id']; ?>}</p>
                    </div>
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Name</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;"><?php echo htmlspecialchars($customer['name']); ?></p>
                    </div>
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Email</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;"><?php echo htmlspecialchars($customer['email']); ?></p>
                    </div>
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Phone</p>
                        <p style="color: #fff; font-weight: 600; margin: 0;"><?php echo htmlspecialchars($customer['phone']); ?></p>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Address</p>
                        <p style="color: #fff; font-weight: 600; margin: 0; word-wrap: break-word;"><?php echo htmlspecialchars($customer['address']); ?></p>
                    </div>
                </div>
            </div>

            <div class="data-table-card" style="margin-top: 20px;">
                <div class="card-header">
                    <h3>Customer Statistics</h3>
                </div>

                <div style="padding: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Total Orders</p>
                        <p style="color: #fff; font-size: 20px; font-weight: 700; margin: 0;"><?php echo $total_orders; ?></p>
                    </div>
                    <div>
                        <p style="color: #bbb; font-size: 13px; margin: 0 0 5px 0;">Total Spent</p>
                        <p style="color: #fff; font-size: 20px; font-weight: 700; margin: 0;">₹ <?php echo number_format($total_spent, 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="data-table-card" style="margin-top: 20px;">
                <div class="card-header">
                    <h3>Order History</h3>
                </div>

                <?php if (empty($orders)): ?>
                    <div style="padding: 20px; text-align: center; color: #bbb;">
                        <p>No orders yet.</p>
                    </div>
                <?php else: ?>
                    <div class="orders-table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $o): ?>
                                    <tr>
                                        <td>#<?php echo $o['order_id']; ?></td>
                                        <td><?php echo date("d M Y", strtotime($o['order_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($o['status']); ?></td>
                                        <td>₹ <?php echo number_format($o['amount'], 2); ?></td>
                                        <td>
                                            <a href="view-order.php?order_id=<?php echo $o['order_id']; ?>" class="btn small">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
