<?php
session_start();
require 'db.php';

if (!isset($_GET['order_id'])) {
    header("Location: shop.php");
    exit;
}

$order_id = intval($_GET['order_id']);
$customer_id = $_SESSION['customer_id'] ?? 0;

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id=? AND customer_id=?");
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit;
}

// Fetch order items
$stmt = $conn->prepare("SELECT oi.*, p.name, p.image FROM order_item oi JOIN product p ON oi.product_id=p.product_id WHERE oi.order_id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Success - OtakuDrips</title>
<link rel="stylesheet" href="styles/home.css">
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #000;
    color: #fff;
    margin:0; padding:0;
}
.success-container {
    max-width: 700px;
    margin: 4rem auto;
    background-color: #ffcc00;
    color: #000;
    padding: 2rem;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
.success-container h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}
.success-container p {
    font-size: 1.2rem;
    margin: 0.5rem 0;
}
.success-container .order-id {
    font-weight: bold;
    margin-top: 1rem;
}
.success-container .order-items {
    margin-top: 2rem;
    text-align: left;
}
.success-container .order-items h2 {
    margin-bottom: 1rem;
    text-align: center;
}
.success-container .order-items ul {
    list-style: none;
    padding: 0;
}
.success-container .order-items li {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #000;
}
.success-container .order-items img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
}
.success-container a.btn-home {
    display: inline-block;
    margin-top: 2rem;
    padding: 0.8rem 2rem;
    background-color: #000;
    color: #ffcc00;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
    transition: 0.3s;
}
.success-container a.btn-home:hover {
    background-color: #333;
}
</style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="success-container">
    <h1>Order Placed Successfully!</h1>
    <p>Thank you for your purchase. Your order has been received and is being processed.</p>
    <p class="order-id">Order ID: #<?php echo htmlspecialchars($order['order_id']); ?></p>
    <div class="order-items">
        <h2>Order Details</h2>
        <ul>
            <?php while($item = $items->fetch_assoc()): ?>
            <li>
                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
            </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <a href="shop.php" class="btn-home">Continue Shopping</a>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
