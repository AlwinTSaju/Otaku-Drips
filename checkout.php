<?php
session_start();
require 'db.php';

if (!isset($_SESSION['customer_id'])) {
    $_SESSION['login_needed'] = "Please login to checkout!";
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Your cart is empty. <a href='shop.php'>Go shopping</a>";
    exit;
}

$cart = $_SESSION['cart'];
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['qty'];
}
$shipping = 50.00;
$grand_total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - OtakuDrips</title>
<link rel="stylesheet" href="styles/home.css">
<link rel="stylesheet" href="styles/cart.css">
<style>
.checkout-container {
    max-width: 800px;
    margin: 3rem auto;
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
}
h1, h2 { text-align: center; }
.billing-details label { display:block; margin:0.5rem 0 0.2rem; font-weight:bold; }
.billing-details input, .billing-details textarea, .billing-details select {
    width:100%; padding:0.7rem; margin-bottom:1rem; border-radius:5px; border:1px solid #ccc;
}
.payment-options {
    display:flex; justify-content: space-around; margin:1rem 0;
}
.payment-options button {
    padding: 1rem 2rem;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-weight:bold;
    transition: 0.3s;
}
.payment-options button:hover { opacity:0.9; }
.payment-card { background:#ffcc00; color:#000; }
.payment-upi { background:#00b894; color:#fff; }
.payment-cod { background:#636e72; color:#fff; }
.order-summary { margin-top:2rem; border-top:1px solid #ccc; padding-top:1rem; }
.order-summary ul { list-style:none; padding:0; }
.order-summary li { margin:0.5rem 0; }
.place-order-btn {
    margin-top:1rem;
    width:100%;
    padding:1rem;
    font-size:1.1rem;
    background:#0984e3;
    color:#fff;
    border:none;
    border-radius:5px;
    cursor:pointer;
    transition: 0.3s;
}
.place-order-btn:hover { background:#74b9ff; }
</style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="checkout-container">
    <h1>Checkout</h1>

    <section class="billing-details">
        <h2>Billing Details</h2>
        <form id="checkout-form" method="POST" action="process-checkout.php">
            <label>Name</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Phone</label>
            <input type="text" name="phone" required>

            <label>Address</label>
            <textarea name="address" required></textarea>

            <h2>Payment Method</h2>
            <div class="payment-options">
                <button type="button" class="payment-card" data-method="card">Credit/Debit Card</button>
                <button type="button" class="payment-upi" data-method="upi">UPI</button>
                <button type="button" class="payment-cod" data-method="cod">Cash on Delivery</button>
            </div>
            <input type="hidden" name="payment_method" id="payment_method" required>

            <div class="order-summary">
                <h2>Order Summary</h2>
                <ul>
                    <?php foreach ($cart as $item): ?>
                    <li><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['qty']; ?> - ₹<?php echo number_format($item['price'] * $item['qty'], 2); ?></li>
                    <?php endforeach; ?>
                </ul>
                <p>Subtotal: ₹<?php echo number_format($subtotal,2); ?></p>
                <p>Shipping: ₹<?php echo number_format($shipping,2); ?></p>
                <p><strong>Grand Total: ₹<?php echo number_format($grand_total,2); ?></strong></p>
            </div>

            <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>">

            <button type="submit" class="place-order-btn">Place Order</button>
        </form>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const paymentButtons = document.querySelectorAll('.payment-options button');
    const paymentInput = document.getElementById('payment_method');

    paymentButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            paymentButtons.forEach(b => b.style.opacity = '0.6');
            btn.style.opacity = '1';
            paymentInput.value = btn.dataset.method;
        });
    });
});
</script>

</body>
</html>
