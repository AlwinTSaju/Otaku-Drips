<?php
include 'includes/header.php';
require 'db.php';

// Check if delivery staff is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch deliveries assigned to this staff
$staff_id = $_SESSION['staff_id'];
$deliveriesQuery = $conn->prepare("
    SELECT d.delivery_id, d.order_id, d.status, o.customer_id, o.order_date
    FROM delivery d
    JOIN orders o ON d.order_id = o.order_id
    WHERE d.delivery_staff_id = ?
    ORDER BY o.order_date DESC
");
$deliveriesQuery->bind_param("i", $staff_id);
$deliveriesQuery->execute();
$deliveries = $deliveriesQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Staff Dashboard</title>
    <link rel="stylesheet" href="styles/deliv-staff.css">
    <link rel="stylesheet" href="styles/home.css">
</head>
<body>

<main class="dashboard-container">

    <h1>Welcome, <?php echo $_SESSION['staff_name']; ?></h1>

    <!-- Notifications -->
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

    <!-- Delivery List -->
    <section class="dashboard-section">
        <h2>Your Assigned Deliveries</h2>
        <?php if ($deliveries->num_rows > 0): ?>
            <table class="delivery-table">
                <thead>
                    <tr>
                        <th>Delivery ID</th>
                        <th>Order ID</th>
                        <th>Customer ID</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $deliveries->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['delivery_id']; ?></td>
                        <td><?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['customer_id']; ?></td>
                        <td><?php echo date("d M Y", strtotime($row['order_date'])); ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <form method="POST" action="update-delivery-status.php" class="status-form">
                                <input type="hidden" name="delivery_id" value="<?php echo $row['delivery_id']; ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                                    <option value="shipped" <?php if($row['status']=='shipped') echo 'selected'; ?>>Shipped</option>
                                    <option value="delivered" <?php if($row['status']=='delivered') echo 'selected'; ?>>Delivered</option>
                                    <option value="cancelled" <?php if($row['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No deliveries assigned yet.</p>
        <?php endif; ?>
    </section>

</main>

</body>
</html>
