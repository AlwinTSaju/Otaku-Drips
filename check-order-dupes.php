<?php
require 'db.php';

echo "<h2>Orders without Delivery Records</h2>";
$res = $conn->query("SELECT o.order_id, o.customer_id, o.order_date, o.status FROM orders o LEFT JOIN delivery d ON o.order_id = d.order_id WHERE d.delivery_id IS NULL ORDER BY o.order_id");

echo "Count: " . $res->num_rows . " orders without delivery<br><br>";

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Order ID</th><th>Customer ID</th><th>Order Date</th><th>Status</th></tr>";
while ($r = $res->fetch_assoc()) {
    echo "<tr><td>" . $r['order_id'] . "</td><td>" . $r['customer_id'] . "</td><td>" . $r['order_date'] . "</td><td>" . $r['status'] . "</td></tr>";
}
echo "</table>";
?>
