<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Tracking - Otaku Drips</title>
  <link rel="stylesheet" href="styles/home.css" />
  <link rel="stylesheet" href="styles/shop.css" />
  <style>

    body {
        background-image: url(./images/japan.jpg);
        background-size: cover;
    }
    .tracking-page {
      max-width: 600px;
      margin: 4rem auto;
      padding: 2rem;
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 8px;
    }
    .tracking-page h2 {
      text-align: center;
      margin-bottom: 2rem;
    }
    .tracking-form {
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
    }
    .tracking-form label {
      font-weight: 600;
    }
    .tracking-form input {
      padding: 0.8rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
    }
    .tracking-form button {
      padding: 0.9rem;
      background-color: #ffcc00;
      border: none;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .tracking-form button:hover {
      background-color: #ffdb4d;
    }
    .tracking-status {
      margin-top: 2rem;
      font-size: 1rem;
      color: #333;
      text-align: center;
    }
  </style>
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main class="tracking-page">
    <div>
        <h2>Track Your Order</h2>
        <form class="tracking-form">
            <label for="order-id">Order ID</label>
            <input type="text" id="order-id" placeholder="Enter your order ID" required />

            <label for="email">Email Address</label>
            <input type="email" id="email" placeholder="Enter your email" required />

            <button type="submit">Track Order</button>
        </form>
        <div class="tracking-status" id="tracking-status">
      <!-- Placeholder: Order status result will be shown here -->
        </div>
    </div>
  </main>

  <footer class="footer">
  <div class="footer-container">
    <div class="footer-brand">
      <h2>Otaku Drips</h2>
      <p>Unleash your anime style.</p>
    </div>

    <div class="footer-links">
      <div>
        <h4>Shop</h4>
        <ul>
          <li><a href="shop.php#all">All Products</a></li>
          <li><a href="wishlist.html">Wishlist</a></li>
          <li><a href="order-tracking.php">Order Tracking</a></li>
        </ul>
      </div>

      <div>
        <h4>Support</h4>
        <ul>
          <li><a href="contact.html">Contact</a></li>
          <li><a href="shipping-info.html">Shipping Info</a></li>
          <li><a href="return-policy.html">Return Policy</a></li>
        </ul>
      </div>

      <div>
        <h4>Legal</h4>
        <ul>
          <li><a href="privacy.html">Privacy Policy</a></li>
          <li><a href="terms.html">Terms & Conditions</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <p>© 2025 Otaku Drips. All Rights Reserved.</p>
  </div>
</footer>

  <script>
    document.querySelector(".tracking-form").addEventListener("submit", function(e) {
      e.preventDefault();
      const statusBox = document.getElementById("tracking-status");
      statusBox.innerText = "Fetching order details... (feature coming soon)";
    });
  </script>
</body>
</html>
