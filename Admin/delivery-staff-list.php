<?php
// PHP Setup
session_start();
require '../db.php';
// ... (admin check, db connection, etc.)

// Data Fetching: Delivery Staff
$staffList = [];
// Fetch staff with count of active orders assigned to them (not delivered or cancelled)
$s_sql = "SELECT ds.staff_id, ds.name, ds.phone, ds.vehicle_info,
                     (SELECT COUNT(*) FROM delivery d JOIN orders o ON d.order_id = o.order_id
                         WHERE d.delivery_staff_id = ds.staff_id AND o.status NOT IN ('delivered','cancelled')) AS active_orders
                     FROM delivery_staff ds
                     ORDER BY ds.name ASC";
$s_res = $conn->query($s_sql);
while ($r = $s_res->fetch_assoc()) $staffList[] = $r;
// Flash messages (initialize to avoid undefined variable warnings)
$flash_success = $_SESSION['success'] ?? null;
$flash_error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Delivery Staff | Admin</title>
<link rel="stylesheet" href="../styles/admin-dash.css">
</head>
<body>
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <?php include 'topbar.php'; ?>

            <div class="page-header-wrap">
                <h2 class="page-title"> Delivery Staff List</h2>
            </div>

            <?php if ($flash_success): ?> <div class="notice success"><?php echo htmlspecialchars($flash_success); ?></div> <?php endif; ?>
            <?php if ($flash_error): ?> <div class="notice error"><?php echo htmlspecialchars($flash_error); ?></div> <?php endif; ?>

            <div class="data-table-card">
                <div class="action-bar">
                    <p>Total Staff: <?php echo count($staffList); ?></p>
                </div>

                <div class="orders-table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Vehicle Info</th>
                                <th>Active Orders (Feature)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($staffList as $s): ?>
                            <tr>
                                <td>#<?php echo $s['staff_id']; ?></td>
                                <td><?php echo htmlspecialchars($s['name']); ?></td>
                                <td><?php echo htmlspecialchars($s['phone']); ?></td>
                                <td><?php echo htmlspecialchars($s['vehicle_info']); ?></td>
                                <td>
                                    <a href="orders.php?staff_id=<?php echo intval($s['staff_id']); ?>">
                                        <span class="status-tag processing"><?php echo intval($s['active_orders']); ?></span>
                                    </a>
                                </td>
                                <td>
                                    <a href="edit-staff.php?id=<?php echo $s['staff_id']; ?>" class="btn small">Edit</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>