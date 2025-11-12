<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$staff_id = intval($_GET['id'] ?? 0);
if ($staff_id === 0) {
    $_SESSION['error'] = "No staff selected for editing.";
    header("Location: delivery-staff-list.php");
    exit;
}

// Fetch staff details
$sql = "SELECT staff_id, name, phone, vehicle_info FROM delivery_staff WHERE staff_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $staff_id);
$stmt->execute();
$res = $stmt->get_result();
$staff = $res->fetch_assoc();
$stmt->close();

if (!$staff) {
    $_SESSION['error'] = "Delivery staff #{$staff_id} not found.";
    header("Location: delivery-staff-list.php");
    exit;
}

// show form
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Delivery Staff #<?php echo $staff['staff_id']; ?> | Admin</title>
    <link rel="stylesheet" href="../styles/admin-dash.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <?php include 'topbar.php'; ?>

            <div class="page-header-wrap">
                <h2 class="page-title">Edit Delivery Staff</h2>
                <a href="delivery-staff-list.php" class="btn">Back to List</a>
            </div>

            <div class="data-form-card">
                <form method="POST" action="update-delivery-staff.php">
                    <input type="hidden" name="staff_id" value="<?php echo intval($staff['staff_id']); ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($staff['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" type="text" value="<?php echo htmlspecialchars($staff['phone']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="vehicle_info">Vehicle Info</label>
                            <input id="vehicle_info" name="vehicle_info" type="text" value="<?php echo htmlspecialchars($staff['vehicle_info']); ?>">
                        </div>

                        <div class="form-group full-width">
                            <label for="password">Password (leave blank to keep unchanged)</label>
                            <input id="password" name="password" type="password" placeholder="New password (optional)">
                        </div>
                    </div>

                    <div class="modal-actions">
                        <a href="delivery-staff-list.php" class="btn">Cancel</a>
                        <button type="submit" class="btn primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
