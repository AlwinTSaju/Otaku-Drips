<?php
include 'includes/header.php';

$isLoggedIn = isset($_SESSION['customer_id']) ? 'true' : 'false';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product - Otaku Drips</title>
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/product.css">
</head>
<body>
    <script>
        // make login status available to JS
        const isLoggedIn = <?php echo $isLoggedIn; ?>;
    </script>

    <main class="product-main">
        <!-- Product Section -->
        <section class="product-section">
            <div class="product-gallery">
                <img id="main-product-image" src="" alt="Main Product Image" class="main-image">
                <div class="thumbnail-container">
                    <!-- thumbnails dynamically injected -->
                </div>
            </div>
            
            <div class="product-details">
                <h1 id="product-title"></h1>
                <span class="series-tag" id="product-category"></span>
                
                <div class="price-container">
                    <span class="current-price" id="product-price"></span>
                    <span class="original-price" id="original-price"></span>
                    <span class="discount-badge" id="discount-percent"></span>
                </div>
                
                <div class="size-selection">
                    <h3>Select Size</h3>
                    <div class="size-options">
                        <input type="radio" name="size" id="size-s" class="size-radio" value="s">
                        <label for="size-s" class="size-btn">S</label>

                        <input type="radio" name="size" id="size-m" class="size-radio" value="m">
                        <label for="size-m" class="size-btn">M</label>
                
                        <input type="radio" name="size" id="size-l" class="size-radio" value="l">
                        <label for="size-l" class="size-btn">L</label>
                
                        <input type="radio" name="size" id="size-xl" class="size-radio" value="xl">
                        <label for="size-xl" class="size-btn">XL</label>
                
                        <input type="radio" name="size" id="size-xxl" class="size-radio" value="xxl">
                        <label for="size-xxl" class="size-btn">XXL</label>
                    </div>
                </div>
                
                <div class="product-actions">
                    <button id="add-to-cart-btn" class="add-to-cart">Add to Cart</button>
                    <button class="wishlist-btn">♥ Add to Wishlist</button>
                </div>
                
                <div class="product-description">
                    <h3>Description</h3>
                    <p id="product-desc"></p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
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

    <!-- Scripts -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const addToCartBtn = document.getElementById("add-to-cart-btn");
        const wishlistBtn = document.querySelector(".wishlist-btn");

        if (addToCartBtn) {
            addToCartBtn.addEventListener("click", (e) => {
                if (!isLoggedIn) {
                    e.preventDefault();
                    window.location.href = "login.php";
                    return;
                }
                // If logged in, run Add to Cart logic
                console.log("Adding to cart...");
            });
        }

        if (wishlistBtn) {
            wishlistBtn.addEventListener("click", (e) => {
                if (!isLoggedIn) {
                    e.preventDefault();
                    window.location.href = "login.php";
                    return;
                }
                // If logged in, run Wishlist logic
                console.log("Adding to wishlist...");
            });
        }
    });
    </script>

    <script type="module" src="scripts/product-data.js"></script>
    <script type="module" src="scripts/product.js"></script>
</body>
</html>
