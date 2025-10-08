<?php
$isLoggedIn = isset($_SESSION['customer_id']) ? 'true' : 'false';
?>
<script>
    const isLoggedIn = <?php echo $isLoggedIn; ?>;
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Otaku Drips</title>
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/shop.css">
</head>
<body>
    <script type="module" src="scripts/product.js"></script>
    <script type="module" src="scripts/product-popup.js"></script>
    <div id="all"></div>
    <div id="one-piece"></div>
    <div id="attack-on-titan"></div>
    <div id="demon-slayer"></div>
    <div id="chainsaw-man"></div>
    <div id="naruto"></div>
    <div id="hunter-x-hunter"></div>
    <div id="berserk"></div>
    <div id="blue-lock"></div>
    <div id="bleach"></div>
    <div id="black-clover"></div>
    <div id="noragami"></div>
    <div id="solo-leveling"></div>
    <div id="jujutsu-kaisen"></div>
    <div id="mob-psycho"></div>
    <div id="jojo"></div>
    <div id="vagabond"></div>
    <div id="products"></div>
    <?php include 'includes/header.php'; ?>

    <main class="shop-main">
        <!-- Shop Hero Section -->
<section class="shop-hero">
    <!-- Video Background -->
    <div class="hero-video-bg">
        <video autoplay muted loop>
            <source src="./images/Kakeguri eyes.mp4" type="video/mp4">
        </video>
    </div>
    
    <!-- Hero Content -->
    <div class="hero-content">
        <h1>Otaku Drips Collection</h1>
        <p class="subtitle">Where Anime Meets Minimalist Fashion</p>
        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-number">20+</span>
                <span class="stat-label">Exclusive Designs</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">15+</span>
                <span class="stat-label">Anime Series</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">5★</span>
                <span class="stat-label">Rated Quality</span>
            </div>
        </div>
        <a href="#all" class="shop-cta" id="prodscroll">Explore Collection</a>
        <script>
            document.getElementById('prodscroll').addEventListener('click', () => {
  window.scrollBy({
    top: 600,           
    behavior: 'smooth'  
  });
});
        </script>
    </div>
    <div class="hero-image">
        <img src="https://via.placeholder.com/800x600" alt="Featured Anime Apparel" class="hero-main-image">
        <div class="hero-badge">New Drops</div>
    </div>
</section>

        <!-- Product Grid -->
        <section class="product-grid-section" id="products">
            <h2>All Products</h2>
            <div class="product-grid",>
                <?php
                require 'db.php';

                // Fetch all products
                $result = $conn->query("SELECT * FROM product");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <div class="product-card" 
                            data-category="<?php echo htmlspecialchars($row['category']); ?>" 
                            data-product-id="<?php echo htmlspecialchars($row['product_id']); ?>">

                            <a href="product.php?id=<?php echo urlencode($row['product_id']); ?>"><img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>"></a>

                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                <div class="price">
                                    <span class="original-price">₹<?php echo number_format($row['original_price'], 2); ?></span>
                                    <span class="current-price">₹<?php echo number_format($row['price'], 2); ?></span>
                                </div>
                                <div class="product-actions">
                                    <button class="select-btn">Select Options</button>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No products found.</p>";
                }
                ?>
            </div>
        </section>

        <!-- Size Popup -->
        <div class="size-popup">
                    <div class="popup-content">
                        <button class="close-popup">&times;</button>
                        <h4>Select Size</h4>
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
                            <a href="#" class="add-to-cart-popup">Select</a>
                    </div>
                </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script type="module" src="scripts/product-popup.js?v=2"></script>
</body>
</html>