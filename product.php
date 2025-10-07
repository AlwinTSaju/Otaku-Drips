
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product - Otaku Drips</title>
    <style>
        .qty-btn {
            background-color: #ffcc00; 
            color: black;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            font-weight: bold;
            width: 22px;
            height: 22px;
            cursor: pointer;
        }

        .qty-btn:hover {
            background-color: #ffcc00; 
        }

        #productQty {
            width: 50px;
            text-align: center;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
        }
    </style>
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/product.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <?php
    $isLoggedIn = isset($_SESSION['customer_id']) ? 'true' : 'false';
    ?>
    <script>
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

                <div class="quantity-selector">
                    <h3>Quantity:</h3> 
                    <button type="button" class="qty-btn" id="decreaseQty">-</button> 
                    <input type="number" id="productQty" value="1" min="1"> 
                    <button type="button" class="qty-btn" id="increaseQty">+</button>
                </div>



                <div class="product-actions">
                    <button id="add-to-cart-btn" class="add-to-cart">Add to Cart</button>
                </div>

                <div class="product-description">
                    <h3>Description</h3>
                    <p id="product-desc"></p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const addToCartBtn = document.getElementById("add-to-cart-btn");

            addToCartBtn.addEventListener("click", (e) => {
                if (!isLoggedIn) { 
                    e.preventDefault(); 
                    window.location.href = "login.php"; 
                    return; 
                }

                const productId = new URLSearchParams(window.location.search).get("id");
                const size = document.querySelector("input[name='size']:checked")?.value;
                const qty = document.getElementById("productQty").value;

                if (!size) {
                    alert("Please select a size.");
                    return;
                }

                if (!productId) {
                    alert("Product ID missing!");
                    return;
                }

                fetch("add-to-cart.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        size: size,
                        qty: parseInt(qty)
                    })
                })
                .then(res => res.json().catch(() => null))
                .then(data => {
                    console.log("Server response:", data);
                    if (data && data.success) {
                        alert("Added to cart!");
                        const cartCount = document.getElementById("cart-count");
                        if (cartCount) cartCount.textContent = data.cart_count;
                    } else {
                        alert("Failed to add to cart");
                    }
                })
                .catch(err => console.error("Fetch error:", err));
            });
        });

        const qtyInput = document.getElementById("productQty");
            document.getElementById("decreaseQty").onclick = () => {
                if (qtyInput.value > 1) qtyInput.value--;
            };
            document.getElementById("increaseQty").onclick = () => {
                qtyInput.value++;
            };
        </script>


    <script type="module" src="scripts/product.js"></script>
</body>

</html>