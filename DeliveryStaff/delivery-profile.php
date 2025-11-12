<?php
session_start();
require '../db.php';

// Redirect if not logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: delivery-login.php");
    exit;
}

$staff_id = intval($_SESSION['staff_id']);
$alert_message = "";

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $vehicle_info = trim($_POST['vehicle_info']);

    $updateQuery = $conn->prepare("UPDATE delivery_staff SET name=?, phone=?, vehicle_info=? WHERE staff_id=?");
    $updateQuery->bind_param('sssi', $name, $phone, $vehicle_info, $staff_id);
    if ($updateQuery->execute()) {
        $alert_message = "Profile updated successfully!";
    } else {
        $alert_message = "Error updating profile. Please try again.";
    }
    $updateQuery->close();
}

// Fetch updated staff info
$query = $conn->prepare("SELECT name, phone, vehicle_info FROM delivery_staff WHERE staff_id = ?");
$query->bind_param('i', $staff_id);
$query->execute();
$result = $query->get_result();
$staff = $result->fetch_assoc();
$query->close();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Delivery Profile</title>
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
            <a href="deliv-staff-dash.php" class="nav-link">Dashboard</a>
            <a href="delivery-profile.php" class="nav-link active">Profile</a>
        </nav>

        <div class="sidebar-bottom">
        </div>
    </aside>

    <main class="main-content">
        <!-- Delivery Staff Topbar (similar to admin) -->
        <header class="topbar">
            <div class="search">
                <span style="color: #fff; font-weight: 600;">Your Profile</span>
            </div>
            <div class="top-actions">
                <button id="darkModeToggle" class="btn small" aria-pressed="false">Dark Mode</button>
                <div class="user-welcome">Hi, <?php echo htmlspecialchars($staff['name'] ?? 'Staff'); ?></div>
                <a href="../logout.php" class="btn small" style="color: #ffcc00; border-color: rgba(255, 204, 0, 0.15);">Logout</a>
            </div>
        </header>

        <section class="dashboard">
            <?php if ($alert_message): ?>
                <div class="notice success"><?php echo htmlspecialchars($alert_message); ?></div>
            <?php endif; ?>

            <div class="page-header-wrap">
                <h2 class="page-title">Edit Your Profile</h2>
                <a href="deliv-staff-dash.php" class="btn">Back to Dashboard</a>
            </div>

            <div class="data-table-card">
                <div class="card-header">
                    <h3>Personal Information</h3>
                </div>

                <form method="POST" style="padding: 20px;">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($staff['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" 
                                value="<?php echo htmlspecialchars($staff['phone']); ?>" 
                                required maxlength="10" oninput="validatePhone()">
                            <span id="phone-error" class="error-message" style="color: #ff4d4f; font-size: 12px; display: block; margin-top: 4px;"></span>
                        </div>

                        <div class="form-group full-width">
                            <label for="vehicle_info">Vehicle Info</label>
                            <input type="text" id="vehicle_info" name="vehicle_info" value="<?php echo htmlspecialchars($staff['vehicle_info']); ?>">
                        </div>
                    </div>

                    <div class="modal-actions" style="margin-top: 25px;">
                        <a href="deliv-staff-dash.php" class="btn">Cancel</a>
                        <button type="submit" class="btn primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</div>

<script src="../scripts/dark-mode.js"></script>
<script>
function validatePhone() {
    const phoneInput = document.getElementById("phone");
    const errorMsg = document.getElementById("phone-error");
    const value = phoneInput.value.trim();

    // Allow only numbers
    if (!/^\d*$/.test(value)) {
        errorMsg.textContent = "Only digits are allowed.";
        phoneInput.style.borderColor = "#ff4d4f";
        return false;
    }

    // Must be exactly 10 digits
    if (value.length > 0 && value.length !== 10) {
        errorMsg.textContent = "Phone number must be 10 digits.";
        phoneInput.style.borderColor = "#ff4d4f";
        return false;
    }

    // Clear error
    errorMsg.textContent = "";
    phoneInput.style.borderColor = "rgba(255, 255, 255, 0.1)";
    return true;
}

// Final check before submitting the form
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", function(e) {
            if (!validatePhone()) {
                e.preventDefault();
            }
        });
    }
});
</script>
</body>
</html>
