<?php
session_start();
require '../db.php';

// Redirect if not logged in admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Optional filter: show active orders for a specific staff when staff_id provided
$orders = [];
$staff_id = isset($_GET['staff_id']) ? intval($_GET['staff_id']) : 0;
$filterInfo = null; // will hold text about the active filter

if ($staff_id > 0) {
    // Fetch staff name for display
    $stmt = $conn->prepare("SELECT name FROM delivery_staff WHERE staff_id = ? LIMIT 1");
    $stmt->bind_param('i', $staff_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $staffName = $row['name'] ?? '';
    $stmt->close();

    // Only active orders (not delivered or cancelled) assigned to this staff
    $sql = "SELECT DISTINCT o.order_id, o.customer_id, o.order_date, o.status
            FROM orders o
            INNER JOIN delivery d ON o.order_id = d.order_id
            WHERE d.delivery_staff_id = ? AND o.status NOT IN ('delivered','cancelled')
            ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $staff_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $oid = intval($r['order_id']);
        
        // Fetch delivery info
        $d_stmt = $conn->prepare("SELECT delivery_id, delivery_staff_id FROM delivery WHERE order_id = ? LIMIT 1");
        $d_stmt->bind_param('i', $oid);
        $d_stmt->execute();
        $d_res = $d_stmt->get_result();
        $delivery = $d_res->fetch_assoc();
        $d_stmt->close();
        
        // Fetch staff name
        $staff_name = null;
        if ($delivery && $delivery['delivery_staff_id']) {
            $s_stmt = $conn->prepare("SELECT name FROM delivery_staff WHERE staff_id = ? LIMIT 1");
            $s_stmt->bind_param('i', $delivery['delivery_staff_id']);
            $s_stmt->execute();
            $s_res = $s_stmt->get_result();
            $staff = $s_res->fetch_assoc();
            $staff_name = $staff['name'] ?? null;
            $s_stmt->close();
        }
        
        // Fetch payment amount
        $p_stmt = $conn->prepare("SELECT IFNULL(SUM(amount), 0) AS amount FROM payment WHERE order_id = ?");
        $p_stmt->bind_param('i', $oid);
        $p_stmt->execute();
        $p_res = $p_stmt->get_result();
        $payment = $p_res->fetch_assoc();
        $p_stmt->close();
        
        $orders[] = [
            'order_id' => $r['order_id'],
            'customer_id' => $r['customer_id'],
            'order_date' => $r['order_date'],
            'status' => $r['status'],
            'delivery_id' => $delivery['delivery_id'] ?? null,
            'delivery_staff_id' => $delivery['delivery_staff_id'] ?? null,
            'staff_name' => $staff_name,
            'amount' => $payment['amount'] ?? 0
        ];
    }
    $stmt->close();

    $filterInfo = 'Active orders for: ' . htmlspecialchars($staffName);
} else {
    // Fetch all orders - avoid duplication from multiple payments/deliveries
    $sql = "SELECT DISTINCT o.order_id, o.customer_id, o.order_date, o.status FROM orders o ORDER BY o.order_date DESC";
    $res = $conn->query($sql);
    while ($r = $res->fetch_assoc()) {
        $oid = intval($r['order_id']);
        
        // Fetch delivery info for this order (only first one)
        $d_stmt = $conn->prepare("SELECT delivery_id, delivery_staff_id FROM delivery WHERE order_id = ? LIMIT 1");
        $d_stmt->bind_param('i', $oid);
        $d_stmt->execute();
        $d_res = $d_stmt->get_result();
        $delivery = $d_res->fetch_assoc();
        $d_stmt->close();
        
        // Fetch staff name if delivery is assigned
        $staff_name = null;
        if ($delivery && $delivery['delivery_staff_id']) {
            $s_stmt = $conn->prepare("SELECT name FROM delivery_staff WHERE staff_id = ? LIMIT 1");
            $s_stmt->bind_param('i', $delivery['delivery_staff_id']);
            $s_stmt->execute();
            $s_res = $s_stmt->get_result();
            $staff = $s_res->fetch_assoc();
            $staff_name = $staff['name'] ?? null;
            $s_stmt->close();
        }
        
        // Fetch payment amount - SUM all payments for this order
        $p_stmt = $conn->prepare("SELECT IFNULL(SUM(amount), 0) AS amount FROM payment WHERE order_id = ?");
        $p_stmt->bind_param('i', $oid);
        $p_stmt->execute();
        $p_res = $p_stmt->get_result();
        $payment = $p_res->fetch_assoc();
        $p_stmt->close();
        
        $orders[] = [
            'order_id' => $r['order_id'],
            'customer_id' => $r['customer_id'],
            'order_date' => $r['order_date'],
            'status' => $r['status'],
            'delivery_id' => $delivery['delivery_id'] ?? null,
            'delivery_staff_id' => $delivery['delivery_staff_id'] ?? null,
            'staff_name' => $staff_name,
            'amount' => $payment['amount'] ?? 0
        ];
    }
}

// Delivery staff list for dropdown
$staffList = [];
$s = $conn->query("SELECT staff_id, name FROM delivery_staff ORDER BY name");
while ($r = $s->fetch_assoc()) $staffList[] = $r;

// Flash messages
$flash_success = $_SESSION['success'] ?? null;
$flash_error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Orders | Admin</title>
<link rel="stylesheet" href="../styles/admin-dash.css">
</head>
<body>
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <?php include 'topbar.php'; ?>

            <div class="page-header-wrap">
                <h2 class="page-title"> Orders</h2>
            </div>

            <?php if ($flash_success): ?> <div class="notice success"><?php echo htmlspecialchars($flash_success); ?></div> <?php endif; ?>
            <?php if ($flash_error): ?> <div class="notice error"><?php echo htmlspecialchars($flash_error); ?></div> <?php endif; ?>

            <div class="data-table-card">
                <div class="action-bar">
                    <p>Showing <?php echo count($orders); ?> orders <?php if ($filterInfo): echo '(' . htmlspecialchars($filterInfo) . ')'; endif; ?></p>
                </div>

                <div class="orders-table-wrap">
                    <table class="data-table" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Delivery</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $o): ?>
                            <tr>
                                <td>#<?php echo intval($o['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($o['customer_id']); ?></td>
                                <td><?php echo date("d M Y", strtotime($o['order_date'])); ?></td>
                                <td><?php echo htmlspecialchars($o['status']); ?></td>
                                <td>
                                    <?php if (!empty($o['delivery_staff_id'])): ?>
                                        <span class="assigned-label">Assigned to <?php echo htmlspecialchars($o['staff_name']); ?></span>
                                    <?php else: ?>
                                        <form method="POST" action="assign-delivery.php" class="assign-form">
                                            <input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>">
                                            <select name="staff_id" class="assign-select" required>
                                                <option value="">Assign</option>
                                                <?php foreach($staffList as $s): ?>
                                                    <option value="<?php echo $s['staff_id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn small">Save</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td>₹ <?php echo number_format((float)$o['amount'], 2); ?></td>
                                <td>
                                    <a href="view-order.php?order_id=<?php echo intval($o['order_id']); ?>" class="btn small">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

<script>
// simple client-side search filter
document.getElementById('orderSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#ordersTable tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
</body>
</html>
