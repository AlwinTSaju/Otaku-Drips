<?php
session_start();
require '../db.php';

// Redirect if not logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: delivery-login.php");
    exit;
}

$staff_id = intval($_SESSION['staff_id']);

// Fetch delivery staff info
$staffQuery = $conn->prepare("SELECT name, phone FROM delivery_staff WHERE staff_id=?");
$staffQuery->bind_param('i', $staff_id);
$staffQuery->execute();
$staffRes = $staffQuery->get_result();
$staffInfo = $staffRes->fetch_assoc();
$staffQuery->close();

// Fetch assigned orders with delivery_id
$stmt = $conn->prepare("
    SELECT 
        d.delivery_id,
        o.order_id, 
        o.customer_id, 
        o.order_date, 
        o.status AS order_status, 
        d.status AS delivery_status,
        IFNULL(p.amount, 0) AS amount
    FROM delivery d
    JOIN orders o ON d.order_id = o.order_id
    LEFT JOIN payment p ON o.order_id = p.order_id
    WHERE d.delivery_staff_id = ?
    ORDER BY o.order_date DESC
");
$stmt->bind_param('i', $staff_id);
$stmt->execute();
$res = $stmt->get_result();
$all_orders = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Separate orders into Active and Past for display
$active_orders = [];
$past_orders = [];
$total_delivered = 0;
$total_pending = 0;

foreach ($all_orders as $order) {
    if ($order['delivery_status'] === 'Delivered' || $order['delivery_status'] === 'Cancelled') {
        $past_orders[] = $order;
        if ($order['delivery_status'] === 'Delivered') $total_delivered++;
    } else {
        $active_orders[] = $order;
        if ($order['delivery_status'] === 'Pending') $total_pending++;
    }
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
    <title>Delivery Dashboard</title>
    <link rel="stylesheet" href="../styles/admin-dash.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
<div class="app-shell">
    <!-- Delivery Staff Sidebar (similar to admin) -->
    <aside class="sidebar">
        <div class="brand">
            <h1 class="brand-title">OTAKU DRIPS</h1>
        </div>

        <nav class="sidebar-nav">
            <a href="deliv-staff-dash.php" class="nav-link active">Dashboard</a>
            <a href="delivery-profile.php" class="nav-link">Profile</a>
        </nav>

        <div class="sidebar-bottom">
        </div>
    </aside>

    <main class="main-content">
        <!-- Delivery Staff Topbar (similar to admin) -->
        <header class="topbar">
            <div class="search">
                <span style="color: #fff; font-weight: 600;">Delivery Dashboard</span>
            </div>
            <div class="top-actions">
                <button id="darkModeToggle" class="btn small" aria-pressed="false">Dark Mode</button>
                <div class="user-welcome">Hi, <?php echo htmlspecialchars($staffInfo['name'] ?? 'Staff'); ?></div>
                <a href="../logout.php" class="btn small" style="color: #ffcc00; border-color: rgba(255, 204, 0, 0.15);">Logout</a>
            </div>
        </header>

        <section class="dashboard">
            <?php if ($flash_success): ?>
                <div class="notice success"><?php echo htmlspecialchars($flash_success); ?></div>
            <?php endif; ?>
            <?php if ($flash_error): ?>
                <div class="notice error"><?php echo htmlspecialchars($flash_error); ?></div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="cards">
                <div class="card stat-card">
                    <div class="stat-title">Active Orders</div>
                    <div class="stat-value"><?php echo count($active_orders); ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-title">Pending</div>
                    <div class="stat-value"><?php echo $total_pending; ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-title">Delivered</div>
                    <div class="stat-value"><?php echo $total_delivered; ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-title">Total Orders</div>
                    <div class="stat-value"><?php echo count($all_orders); ?></div>
                </div>
            </div>

            <!-- Active Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h3>Active Orders</h3>
                </div>

                <div class="orders-table-wrap">
                    <table class="orders-table" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Order Status</th>
                                <th>Delivery Status</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($active_orders) > 0): ?>
                                <?php foreach ($active_orders as $order): ?>
                                    <tr data-order-id="<?php echo intval($order['order_id']); ?>">
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_id']); ?></td>
                                        <td><?php echo date("d M Y", strtotime($order['order_date'])); ?></td>
                                        <td><span class="status-tag <?php echo strtolower(str_replace(' ', '-', $order['order_status'])); ?>"><?php echo htmlspecialchars($order['order_status']); ?></span></td>
                                        <td>
                                            <select class="status-dropdown assign-select" 
                                                data-delivery-id="<?php echo $order['delivery_id']; ?>"
                                                data-order-id="<?php echo $order['order_id']; ?>">
                                                <option value="Pending" <?php echo ($order['delivery_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Shipped" <?php echo ($order['delivery_status'] === 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="Delivered" <?php echo ($order['delivery_status'] === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="Cancelled" <?php echo ($order['delivery_status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </td>
                                        <td>₹ <?php echo number_format($order['amount'], 2); ?></td>
                                        <td>
                                            <a href="view-order.php?order_id=<?php echo $order['order_id']; ?>" class="btn small">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #bbb; padding: 20px;">No active orders assigned.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Past Orders Table -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h3>Past Orders (Delivered/Cancelled)</h3>
                </div>

                <div class="orders-table-wrap">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Order Status</th>
                                <th>Delivery Status</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($past_orders) > 0): ?>
                                <?php foreach ($past_orders as $order): ?>
                                    <tr data-order-id="<?php echo intval($order['order_id']); ?>">
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_id']); ?></td>
                                        <td><?php echo date("d M Y", strtotime($order['order_date'])); ?></td>
                                        <td><span class="status-tag <?php echo strtolower(str_replace(' ', '-', $order['order_status'])); ?>"><?php echo htmlspecialchars($order['order_status']); ?></span></td>
                                        <td><span class="status-tag <?php echo strtolower(str_replace(' ', '-', $order['delivery_status'])); ?>"><?php echo htmlspecialchars($order['delivery_status']); ?></span></td>
                                        <td>₹ <?php echo number_format($order['amount'], 2); ?></td>
                                        <td>
                                            <a href="view-order.php?order_id=<?php echo $order['order_id']; ?>" class="btn small">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #bbb; padding: 20px;">No past orders yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<script src="../scripts/dark-mode.js"></script>
<script>
    // Handle delivery status update for active orders
    document.querySelectorAll('.status-dropdown').forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            const deliveryId = this.getAttribute('data-delivery-id');
            const orderId = this.getAttribute('data-order-id');
            const newStatus = this.value;
            const dropdown = this;

            fetch('update-delivery-status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'delivery_id=' + deliveryId + '&status=' + encodeURIComponent(newStatus)
            })
            .then(response => {
                if (!response.ok) throw new Error('Server responded with status: ' + response.status);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success notice
                    const notices = document.querySelector('.notice.success');
                    if (notices) {
                        notices.textContent = 'Order #' + orderId + ' updated to ' + newStatus;
                        notices.style.display = 'block';
                        setTimeout(() => { notices.style.display = 'none'; }, 3000);
                    }

                    // If status is Delivered or Cancelled, reload page to reorganize tables
                    if (newStatus === 'Delivered' || newStatus === 'Cancelled') {
                        setTimeout(() => { location.reload(); }, 1000);
                    }
                } else {
                    alert('Update failed: ' + data.message);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('An error occurred while updating the status.');
            });
        });
    });
</script>
</body>
</html>