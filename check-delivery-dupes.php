<?php
require 'db.php';

echo "<h2>Orders with Multiple Delivery Records</h2>";
$res = $conn->query("SELECT order_id, COUNT(*) as count FROM delivery GROUP BY order_id HAVING COUNT(*) > 1 ORDER BY order_id");
if ($res->num_rows === 0) {
    echo "No duplicate delivery records found.<br>";
} else {
    echo "<table border='1'>";
    echo "<tr><th>Order ID</th><th>Delivery Count</th></tr>";
    while ($r = $res->fetch_assoc()) {
        echo "<tr><td>" . $r['order_id'] . "</td><td>" . $r['count'] . "</td></tr>";
    }
    echo "</table><br>";
}

echo "<h2>All Delivery Records Count</h2>";
$total = $conn->query("SELECT COUNT(*) as count FROM delivery")->fetch_assoc();
echo "Total delivery records: " . $total['count'] . "<br>";

echo "<h2>Unique Orders in Delivery Table</h2>";
$unique = $conn->query("SELECT COUNT(DISTINCT order_id) as count FROM delivery")->fetch_assoc();
echo "Unique order_ids in delivery: " . $unique['count'] . "<br>";

echo "<h2>All Orders Count</h2>";
$all_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc();
echo "Total orders: " . $all_orders['count'] . "<br>";
?>
