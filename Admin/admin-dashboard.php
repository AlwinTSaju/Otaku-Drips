<?php
session_start();
require '../db.php'; // must set $conn (mysqli) and session admin info

// Redirect if not logged in admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Helpers
function fetch_one($conn, $sql, $types = null, $params = []) {
    $stmt = $conn->prepare($sql);
    if ($types) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_row();
    $stmt->close();
    return $row ? $row[0] : 0;
}

// 1) Totals
$totalRevenue = fetch_one($conn, "SELECT IFNULL(SUM(amount),0) FROM payment");
$totalCustomers = fetch_one($conn, "SELECT COUNT(*) FROM customer");
$totalProducts = fetch_one($conn, "SELECT COUNT(*) FROM product");

// 2) Revenue data for 7 days and 30 days
function getRevenueData($conn, $days) {
    $revenueDays = [];
    $revenueValues = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $day = date('Y-m-d', strtotime("-{$i} days"));
        $sql = "SELECT IFNULL(SUM(amount),0) FROM payment WHERE DATE(payment_date)=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $day);
        $stmt->execute();
        $sum = 0;
        $stmt->bind_result($sum);
        $stmt->fetch();
        $stmt->close();
        $revenueDays[] = ($days == 7) ? date('D', strtotime($day)) : date('d M', strtotime($day));
        $revenueValues[] = floatval($sum);
    }
    return [$revenueDays, $revenueValues];
}

list($revenueDays7, $revenueValues7) = getRevenueData($conn, 7);
list($revenueDays30, $revenueValues30) = getRevenueData($conn, 30);

$revenueDays7Js = json_encode($revenueDays7);
$revenueValues7Js = json_encode($revenueValues7);
$revenueDays30Js = json_encode($revenueDays30);
$revenueValues30Js = json_encode($revenueValues30);

// 3) Top products
$topProducts = [];
$tpSql = "
    SELECT p.product_id, p.name, p.image, IFNULL(SUM(oi.quantity),0) AS sold_qty
    FROM product p
    LEFT JOIN order_item oi ON p.product_id = oi.product_id
    GROUP BY p.product_id
    ORDER BY sold_qty DESC
    LIMIT 5
";
$res = $conn->query($tpSql);
while ($r = $res->fetch_assoc()) $topProducts[] = $r;

// 4) Recent orders with delivery info and staff name
$orders = [];
$ordersSql = "
    SELECT o.order_id, o.customer_id, o.order_date, o.status,
           d.delivery_id, d.delivery_staff_id, ds.name AS staff_name
    FROM orders o
    LEFT JOIN delivery d ON o.order_id = d.order_id
    LEFT JOIN delivery_staff ds ON d.delivery_staff_id = ds.staff_id
    ORDER BY o.order_date DESC
    LIMIT 50
";
$res = $conn->query($ordersSql);
while ($r = $res->fetch_assoc()) $orders[] = $r;

// 5) Delivery staff list for dropdown
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
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../styles/admin-dash.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'topbar.php'; ?>

        <section class="dashboard">
            <?php if ($flash_success): ?>
                <div class="notice success"><?php echo htmlspecialchars($flash_success); ?></div>
            <?php endif; ?>
            <?php if ($flash_error): ?>
                <div class="notice error"><?php echo htmlspecialchars($flash_error); ?></div>
            <?php endif; ?>

            <div class="cards">
                <div class="card stat-card">
                    <div class="stat-title">Total Revenue</div>
                    <div class="stat-value">₹ <?php echo number_format($totalRevenue, 2); ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-title">Total Customers</div>
                    <div class="stat-value"><?php echo intval($totalCustomers); ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-title">Total Products</div>
                    <div class="stat-value"><?php echo intval($totalProducts); ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-title">Orders (recent)</div>
                    <div class="stat-value"><?php echo count($orders); ?></div>
                </div>
            </div>

            <div class="grid-two">
                <div class="card" style="min-width:0;">
                    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                        <h3>Revenue Growth</h3>
                        <select id="revenueRange" class="dropdown">
                            <option value="7">Last 7 Days</option>
                            <option value="30">Last 30 Days</option>
                        </select>
                    </div>
                    <div id="revenueChartWrap" style="overflow-x: auto; width: 100%;">
                        <div id="revenueChartInner" style="display:inline-block;">
                            <canvas id="revenueChart" width="600" height="250" style="display: block; width:100%; height:250px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Top Products</h3>
                        <a class="link small" href="products.php">View more</a>
                    </div>
                    <div class="top-products">
                        <?php foreach($topProducts as $p): ?>
                            <div class="product-row">
                                <img src="<?php echo htmlspecialchars(str_replace('./', '../', $p['image'])); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-thumb">
                                <div class="product-info">
                                    <div class="product-name"><?php echo htmlspecialchars($p['name']); ?></div>
                                    <div class="product-meta"><?php echo intval($p['sold_qty']); ?> sold</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Orders</h3>
                </div>

                <div class="orders-table-wrap">
                    <table class="orders-table" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Delivery</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $o): ?>
                                <tr data-order="<?php echo intval($o['order_id']); ?>">
                                    <td>#<?php echo $o['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($o['customer_id']); ?></td>
                                    <td><?php echo date("d M Y", strtotime($o['order_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($o['status']); ?></td>
                                    <td>
                                        <?php if (!empty($o['delivery_staff_id'])): ?>
                                            <span class="assigned-label">
                                                Assigned to <?php echo htmlspecialchars($o['staff_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <form method="POST" action="assign-delivery.php" class="assign-form">
                                                <input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>">
                                                <select name="staff_id" class="assign-select" required>
                                                    <option value="">Assign</option>
                                                    <?php foreach($staffList as $s): ?>
                                                        <option value="<?php echo $s['staff_id']; ?>">
                                                            <?php echo htmlspecialchars($s['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn small">Save</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Add delivery staff modal -->
<div id="addStaffModal" class="modal">
    <div class="modal-inner">
        <h2>Add Delivery Staff</h2>
        <form method="POST" action="add-delivery-staff.php" id="addStaffForm">
            <label>Name</label>
            <input name="name" required>
            
            <label>Phone</label>
            <input name="phone" type="tel" pattern="[0-9]{10}" maxlength="10" required>
            <p id="phoneError" class="error-message"></p> 
            <label>Vehicle Info</label>
            <input name="vehicle_info">
            
            <label>Password</label>
            <input name="password" type="password" required>
            
            <div class="modal-actions">
                <button type="button" id="closeAddStaff" class="btn">Cancel</button>
                <button type="submit" class="btn primary">Add</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal
    document.getElementById('addStaffForm').addEventListener('submit', function(event) {
        const phoneInput = this.querySelector('input[name="phone"]');
        const phoneError = document.getElementById('phoneError');
        const phoneValue = phoneInput.value.trim();
        const phoneRegex = /^[0-9]{10}$/;
        phoneError.textContent = '';
        phoneInput.classList.remove('input-error');
        if (!phoneRegex.test(phoneValue)) {
            event.preventDefault(); 
            phoneError.textContent = 'Please enter a valid 10-digit phone number (no spaces or hyphens).';
            phoneInput.classList.add('input-error');
            phoneInput.focus(); 
            return false;
        }
    });
    document.getElementById('openAddStaff').addEventListener('click', () => {
        document.getElementById('addStaffModal').classList.add('is-open');
    });
    document.getElementById('closeAddStaff').addEventListener('click', () => {
        document.getElementById('addStaffModal').classList.remove('is-open');
    });

    // Revenue chart with dropdown
    const labels7 = <?php echo $revenueDays7Js; ?>;
    const values7 = <?php echo $revenueValues7Js; ?>;
    const labels30 = <?php echo $revenueDays30Js; ?>;
    const values30 = <?php echo $revenueValues30Js; ?>;
    const canvas = document.getElementById('revenueChart');
    const chartWrap = document.getElementById('revenueChartWrap');
    const ctx = canvas.getContext('2d');

    function drawChart(labels, values) {
        // Dynamically resize canvas based on number of bars (drawing buffer)
        const minWidth = 600; // comfortable for 7 days
        const barMinWidth = 28; // minimum visual width per bar
        const requiredWidth = Math.max(minWidth, values.length * barMinWidth + 80);

        // Set both the drawing buffer and the CSS layout width so the inner container
        // holds the canvas and only the inner wrapper scrolls (prevents whole page scroll).
        canvas.width = requiredWidth; // drawing buffer width
        // Put canvas inside an inner wrapper; set that wrapper's width so only it scrolls
        const inner = document.getElementById('revenueChartInner');
        if (inner) {
            inner.style.width = requiredWidth + 'px';
        }
        // Make sure canvas drawing buffer and style height are set
        canvas.height = 250;
        canvas.style.height = canvas.height + 'px';

        // Clear and draw
        ctx.clearRect(0,0,canvas.width,canvas.height);
    const padding = 40, w = canvas.width - padding*2, h = canvas.height - padding*2;
        const max = Math.max(...values, 10);
        const barSpacing = w / values.length;
        const barWidth = Math.max(6, Math.min(barSpacing * 0.7, 60));

        // Decide label sampling so labels don't collide
        const maxBottomLabels = 12; // target maximum bottom labels visible
        const bottomSkip = values.length > maxBottomLabels ? Math.ceil(values.length / maxBottomLabels) : 1;
        const maxTopLabels = 10;
        const topSkip = values.length > maxTopLabels ? Math.ceil(values.length / maxTopLabels) : 1;

        labels.forEach((lab, i) => {
            const val = values[i];
            const x = padding + i * barSpacing + (barSpacing - barWidth) / 2;
            const barH = (val / max) * h;
            const y = padding + (h - barH);
            ctx.fillStyle = '#ffcc00';
            ctx.fillRect(x, y, barWidth, barH);

            ctx.textAlign = 'center';

            // Bottom labels: sample by bottomSkip
            if (i % bottomSkip === 0) {
                const labelFontSize = Math.max(9, Math.min(12, Math.floor(barSpacing / 3)));
                ctx.font = labelFontSize + 'px sans-serif';
                ctx.fillStyle = '#fff';
                ctx.fillText(lab, x + barWidth/2, canvas.height - 10);
            }

            // Top values: show only sampled topSkip positions to avoid clutter
            if (i % topSkip === 0) {
                const valueFontSize = Math.max(9, Math.min(11, Math.floor(barSpacing / 4)));
                ctx.font = valueFontSize + 'px sans-serif';
                if (barH < 18) {
                    // not enough room above bar, draw inside with dark text
                    ctx.fillStyle = '#000';
                    ctx.fillText('₹' + val.toFixed(0), x + barWidth/2, y + 12);
                } else {
                    ctx.fillStyle = '#fff';
                    ctx.fillText('₹' + val.toFixed(0), x + barWidth/2, y - 8);
                }
            }
        });
    }

    // Initial 7-day chart
    drawChart(labels7, values7);

    // Dropdown change event
    document.getElementById('revenueRange').addEventListener('change', (e) => {
        if (e.target.value === '30') drawChart(labels30, values30);
        else drawChart(labels7, values7);
    });

    // Search filter
    document.getElementById('globalSearch').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#ordersTable tbody tr').forEach(tr => {
            tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
<script src="../scripts/dark-mode.js"></script>
</body>
</html>
