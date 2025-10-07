<?php
session_start();
require 'db.php';

if (!isset($_SESSION['customer_id'])) {
    $_SESSION['login_needed'] = "Please login to view cart!";
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$subtotal = 0.00;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['qty'];
}
$shipping = 50.00;
$grand_total = $subtotal + $shipping;
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cart - OtakuDrips</title>
    <link rel="stylesheet" href="styles/cart.css">
    <link rel="stylesheet" href="styles/home.css">
    <style>
        .qty-btn {
            padding: 4px 8px;
            cursor: pointer;
            font-weight: bold;
            background-color: #ffcc00;
            border: none;
            border-radius: 4px;
        }
        .qty-input {
            width: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="cart-container">
    <h1>Your Cart</h1>

    <?php if (empty($cart)): ?>
        <p>Your cart is empty. <a href="shop.php#all">Continue shopping</a></p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price (₹)</th>
                    <th>Quantity</th>
                    <th>Line Total (₹)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($cart as $item): ?>
            <tr data-product-id="<?php echo htmlspecialchars($item['product_id']); ?>" data-size="<?php echo htmlspecialchars($item['size']); ?>">
                <td class="prod-cell">
                    <?php if (!empty($item['image'])): ?>
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="" class="cart-thumb">
                    <?php endif; ?>
                    <div class="prod-info">
                        <div class="prod-name"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="prod-id">ID: <?php echo htmlspecialchars($item['product_id']); ?></div>
                        <div class="prod-size">Size: <?php echo strtoupper(htmlspecialchars($item['size'])); ?></div>
                    </div>
                </td>
                <td class="price"><?php echo number_format($item['price'], 2); ?></td>
                <td>
                    <div style="display:flex; align-items:center; gap:4px;">
                        <button class="qty-btn minus">-</button>
                        <input type="number" class="qty-input" min="1" value="<?php echo (int)$item['qty']; ?>">
                        <button class="qty-btn plus">+</button>
                    </div>
                </td>
                <td class="line-total"><?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
                <td>
                    <button class="remove-btn small-btn danger">Remove</button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <div>Subtotal: ₹<span id="subtotal"><?php echo number_format($subtotal, 2); ?></span></div>
            <div>Shipping: ₹<span id="shipping"><?php echo number_format($shipping, 2); ?></span></div>
            <div class="grand">Grand Total: ₹<span id="grand_total"><?php echo number_format($grand_total, 2); ?></span></div>

            <form method="POST" action="checkout.php" style="margin-top:1rem;">
                <button class="checkout-btn" type="submit">Proceed to Checkout</button>
            </form>
        </div>
    <?php endif; ?>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {

    function parsePrice(str){
        return parseFloat(str.replace(/,/g,''));
    }

    function updateCart(productId, size, qty, callback) {
        fetch('update-cart.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({product_id: productId, size: size, qty: qty})
        })
        .then(res => res.json())
        .then(data => callback(data))
        .catch(err => console.error(err));
    }

    document.querySelectorAll('tr[data-product-id]').forEach(row => {
        const productId = row.dataset.productId;
        const size = row.dataset.size;
        const qtyInput = row.querySelector('.qty-input');
        const lineTotalEl = row.querySelector('.line-total');

        row.querySelector('.plus').addEventListener('click', e => {
            e.preventDefault();
            let qty = parseInt(qtyInput.value) + 1;
            qtyInput.value = qty;
            updateCart(productId, size, qty, data=>{
                if(data.success){
                    const price = parsePrice(row.querySelector('.price').textContent);
                    lineTotalEl.textContent = (price * qty).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('subtotal').textContent = parseFloat(data.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('grand_total').textContent = parseFloat(data.grand_total).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            });
        });

        row.querySelector('.minus').addEventListener('click', e => {
            e.preventDefault();
            let qty = Math.max(1, parseInt(qtyInput.value) - 1);
            qtyInput.value = qty;
            updateCart(productId, size, qty, data=>{
                if(data.success){
                    const price = parsePrice(row.querySelector('.price').textContent);
                    lineTotalEl.textContent = (price * qty).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('subtotal').textContent = parseFloat(data.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('grand_total').textContent = parseFloat(data.grand_total).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            });
        });

        qtyInput.addEventListener('change', e=>{
            let qty = Math.max(1, parseInt(qtyInput.value));
            qtyInput.value = qty;
            updateCart(productId, size, qty, data=>{
                if(data.success){
                    const price = parsePrice(row.querySelector('.price').textContent);
                    lineTotalEl.textContent = (price * qty).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('subtotal').textContent = parseFloat(data.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('grand_total').textContent = parseFloat(data.grand_total).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            });
        });

        row.querySelector('.remove-btn').addEventListener('click', e=>{
            e.preventDefault();
            updateCart(productId, size, 0, data=>{
                if(data.success){
                    row.remove();
                    document.getElementById('subtotal').textContent = parseFloat(data.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('grand_total').textContent = parseFloat(data.grand_total).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            });
        });
    });

});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
