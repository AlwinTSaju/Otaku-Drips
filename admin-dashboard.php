<?php
session_start();
require 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all orders
$ordersQuery = $conn->prepare("
    SELECT o.order_id, o.customer_id, o.order_date, o.status
    FROM orders o
    ORDER BY o.order_date DESC
");
$ordersQuery->execute();
$orders = $ordersQuery->get_result();

// Fetch all delivery staff for assign dropdown
$staffQuery = $conn->prepare("SELECT staff_id, name FROM delivery_staff");
$staffQuery->execute();
$deliveryStaff = $staffQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - View Orders</title>
    <link rel="stylesheet" href="styles/deliv-staff.css">
    <link rel="stylesheet" href="styles/home.css">
    <style>
        .admin-menu li a.active {
            color: #fff;
            background-color: #e6b800;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .admin-table th {
            background-color: #f7f7f7;
            color: #e6b800;
        }

        .admin-table tr:hover {
            background-color: #fafafa;
        }

        form select {
            padding: 5px;
        }
    </style>
</head>
<body>
<header>
    <nav>
        <ul class="main-menu admin-menu">
            <li><a href="admin-dashboard.php" class="active">View Orders</a></li>
            <li><a href="add-product.php">Add Product</a></li>
            <li><a href="view-products.php">View Products</a></li>
        </ul>
        <div class="user-options">
            <span style="color:white;">Hi, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> (Admin)</span>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
</header>

<main class="dashboard-container">
    <h1>All Orders</h1>

    <?php
    if (isset($_SESSION['success'])) {
        echo '<div class="dashboard-notification success">'.$_SESSION['success'].'</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="dashboard-notification error">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    }
    ?>

    <?php if ($orders->num_rows > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Assign Delivery</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['customer_id']; ?></td>
                        <td><?php echo date("d M Y", strtotime($row['order_date'])); ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <form method="POST" action="assign-delivery.php">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                <select name="staff_id" onchange="this.form.submit()">
                                    <option value="">Assign Staff</option>
                                    <?php 
                                    $deliveryStaff->data_seek(0);
                                    while($staff = $deliveryStaff->fetch_assoc()): ?>
                                        <option value="<?php echo $staff['staff_id']; ?>"><?php echo htmlspecialchars($staff['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>
</main>
</body>
</html>
