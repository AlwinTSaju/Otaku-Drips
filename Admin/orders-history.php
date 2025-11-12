<?php
session_start();
require '../db.php'; // Includes $conn (mysqli)

// Redirect if not logged in admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$from_date = $_GET['from'] ?? '';
$to_date = $_GET['to'] ?? '';

if (!$from_date || !$to_date) {
    http_response_code(400);
    echo json_encode(['error' => 'Both from and to dates are required.']);
    exit;
}

// Ensure valid date format and prevent SQL injection
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $from_date) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $to_date)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format.']);
    exit;
}

$orders = [];
$ordersSql = "
    SELECT o.order_id, o.customer_id, DATE(o.order_date) as order_date, o.status,
           d.delivery_id, d.delivery_staff_id
    FROM orders o
    LEFT JOIN delivery d ON o.order_id = d.order_id
    WHERE o.order_date >= ? AND o.order_date <= ?
    ORDER BY o.order_date DESC
";

$stmt = $conn->prepare($ordersSql);
$stmt->bind_param('ss', $from_date, $to_date);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    // Format the date for consistent display in JS
    $r['order_date'] = date("d M Y", strtotime($r['order_date']));
    $orders[] = $r;
}
$stmt->close();

echo json_encode($orders);