<?php
// PHP Setup
session_start();
require '../db.php';
// ... (admin check, db connection, etc.)

// Data Fetching: Customers
$customers = [];
$c_res = $conn->query("SELECT customer_id, name, email, phone, address FROM customer ORDER BY customer_id DESC");
while ($r = $c_res->fetch_assoc()) $customers[] = $r;
// ... (flash messages setup)
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Customers | Admin</title>
<link rel="stylesheet" href="../styles/admin-dash.css">
</head>
<body>
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <?php include 'topbar.php'; ?>

            <div class="page-header-wrap">
                <h2 class="page-title"> Customer Directory</h2>
            </div>

            <div class="data-table-card">
                <div class="action-bar">
                    <p>Showing <?php echo count($customers); ?> customers</p>
                </div>

                <div class="orders-table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address Summary</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($customers as $c): ?>
                            <tr>
                                <td>#<?php echo $c['customer_id']; ?></td>
                                <td><?php echo htmlspecialchars($c['name']); ?></td>
                                <td><?php echo htmlspecialchars($c['email']); ?></td>
                                <td><?php echo htmlspecialchars($c['phone']); ?></td>
                                <td><?php echo substr(htmlspecialchars($c['address']), 0, 30) . '...'; ?></td>
                                <td>
                                    <a href="view-customer.php?id=<?php echo $c['customer_id']; ?>" class="btn small">View Details</a>
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