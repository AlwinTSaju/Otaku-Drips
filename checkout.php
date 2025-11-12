<?php
session_start();
require 'db.php'; // database connection

if (!isset($_SESSION['customer_id'])) {
    $_SESSION['login_needed'] = "Please login to checkout!";
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];
$customer_details = [];

// Fetch customer details from the database
$stmt = $conn->prepare("SELECT name, email, phone, address FROM customer WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $customer_details = $result->fetch_assoc();
}
$stmt->close();

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
    transition: border-color 0.2s;
}
/* CSS for Validation Feedback */
.billing-details input.error-input, .billing-details textarea.error-input {
    border: 2px solid #e74c3c; /* Red border */
}
.error-message {
    color: #e74c3c;
    font-size: 0.9rem;
    margin-top: -0.8rem;
    margin-bottom: 0.8rem;
    min-height: 1.2rem; /* Reserves space to prevent layout jump */
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
    flex-grow: 1; 
    margin: 0 5px; 
}
.payment-options button:first-child { margin-left: 0; }
.payment-options button:last-child { margin-right: 0; }
.payment-options button.selected { 
    box-shadow: 0 0 0 3px #000; 
    opacity: 1 !important;
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
            <input type="text" name="name" 
                value="<?php echo htmlspecialchars($customer_details['name'] ?? ''); ?>" required>

            <label>Email</label>
            <input type="email" name="email" 
                value="<?php echo htmlspecialchars($customer_details['email'] ?? ''); ?>" required>

            <label>Phone</label>
            <input type="tel" name="phone" id="phone" 
                value="<?php echo htmlspecialchars($customer_details['phone'] ?? ''); ?>" required>
            <p id="phone-error" class="error-message"></p>

            <label>Address</label>
            <textarea name="address" id="address" required><?php echo htmlspecialchars($customer_details['address'] ?? ''); ?></textarea>
            <p id="address-error" class="error-message"></p>
            
            <label>Pincode</label>
            <input type="text" name="pincode" id="pincode" required>
            <p id="pincode-error" class="error-message"></p>
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
    const checkoutForm = document.getElementById('checkout-form');

    // --- Payment Button Logic ---
    paymentButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            paymentButtons.forEach(b => {
                b.style.opacity = '0.6';
                b.classList.remove('selected');
            });
            
            btn.style.opacity = '1';
            btn.classList.add('selected');
            paymentInput.value = btn.dataset.method;
        });
    });

    if (paymentButtons.length > 0) {
        // Automatically select the first option on load and prevent the default hover behavior
        paymentButtons.forEach(b => b.style.opacity = '0.6');
        paymentButtons[0].click();
    }
    
    // --- Validation Logic ---

    // Utility function to handle error display
    const displayError = (inputElement, message, errorElementId) => {
        const errorElement = document.getElementById(errorElementId);
        errorElement.textContent = message;
        inputElement.classList.add('error-input');
    };

    checkoutForm.addEventListener('submit', function(event) {
        let isValid = true;

        // Clear previous errors
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        document.querySelectorAll('.error-input').forEach(el => el.classList.remove('error-input'));

        // 1. Phone Validation (Exactly 10 digits)
        const phoneInput = document.getElementById('phone');
        const phoneValue = phoneInput.value.trim();
        if (!/^[0-9]{10}$/.test(phoneValue)) {
            displayError(phoneInput, 'Phone number must be exactly 10 digits (numbers only).', 'phone-error');
            isValid = false;
        }

        // 2. Address Validation (Minimum 10 characters for detail)
        const addressInput = document.getElementById('address');
        const addressValue = addressInput.value.trim();
        if (addressValue.length < 10) {
            displayError(addressInput, 'Please provide a detailed address (min 10 characters).', 'address-error');
            isValid = false;
        }

        // 3. Pincode Validation (Exactly 6 digits)
        const pincodeInput = document.getElementById('pincode');
        const pincodeValue = pincodeInput.value.trim();
        if (!/^[0-9]{6}$/.test(pincodeValue)) {
            displayError(pincodeInput, 'Pincode must be exactly 6 digits.', 'pincode-error');
            isValid = false;
        }

        // 4. Payment Method Check
        if (!paymentInput.value) {
            alert('Please select a payment method before placing your order.');
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault(); // Stop form submission if validation failed
        }
    });
});
</script>

</body>
</html>