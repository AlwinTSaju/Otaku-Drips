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
                <!-- Product 1 -->
                <div class="product-card" data-category="one-piece" data-product-id="zoro-limited-edition">
                    <img src="./images/products/roronoa le.png" alt="Roronoa Zoro v4 Shirt">
                    <div class="product-info">
                        <span class="series-tag">One Piece</span>
                        <h3>Roronoa Zoro v4 Limited Edition</h3>
                        <div class="price">
                            <span class="original-price">₹1,200.00</span>
                            <span class="current-price">₹1,020.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn" onclick="document.getElementById('size-popup-toggle').checked = true">
                                Select Options
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 2 -->
                <div class="product-card" data-category="attack-on-titan" data-product-id="eren-yeager-v4">
                    <img src="./images/products/attack titan.png" alt="Eren Yeager Shirt">
                    <div class="product-info">
                        <span class="series-tag">Attack on Titan</span>
                        <h3>Attack Titan v4 T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹648.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn" onclick="document.getElementById('size-popup-toggle').checked = true">Select Options</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 3 -->
                <div class="product-card" data-category="one-piece" data-product-id="luffy-gear-5">
                    <img src="./images/products/luffy gear 5.png" alt="Luffy Gear 5 Shirt">
                    <div class="product-info">
                        <span class="series-tag">One Piece</span>
                        <h3>Luffy Gear 5 One Piece T-Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹1,000.00</span>
                            <span class="current-price">₹850.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 4 -->
                <div class="product-card" data-category="one-piece" data-product-id="zoro-v4">
                    <img src="./images/products/zoro v4.png" alt="Zoro v4 Shirt">
                    <div class="product-info">
                        <span class="series-tag">One Piece</span>
                        <h3>Roronoa Zoro v4 One Piece T-Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹1,000</span>
                            <span class="current-price">₹750</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 5 -->
                <div class="product-card" data-category="solo-leveling" data-product-id="igris-shirt">
                    <img src="./images/products/igris.png" alt="Igris Shirt">
                    <div class="product-info">
                        <span class="series-tag">Solo Leveling</span>
                        <h3>Igris Solo Leveling Oversized T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 6 -->
                <div class="product-card" data-category="berserk" data-product-id="guts-shirt">
                    <img src="./images/products/guts.png" alt="Guts Shirt">
                    <div class="product-info">
                        <span class="series-tag">Berserk</span>
                        <h3>Guts Berserk Hoodie</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 7 -->
                <div class="product-card" data-category="attack-on-titan" data-product-id="eren-shirt">
                    <img src="./images/products/eren.png" alt="Eren Yeager Shirt">
                    <div class="product-info">
                        <span class="series-tag">Attack on Titan</span>
                        <h3>Eren Yeager Attack on Titan T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 8 -->
                <div class="product-card" data-category="naruto" data-product-id="sasuke-shirt">
                    <img src="./images/products/sasuke.png" alt="Sasuke Uchiha T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Naruto</span>
                        <h3>Sasuke Uchiha T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹900.00</span>
                            <span class="current-price">₹765.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 9 -->
                <div class="product-card" data-category="black-clover" data-product-id="black-clover-shirt">
                    <img src="./images/products/black clover.png" alt="Black Clover T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Black Clover</span>
                        <h3>Black Clover T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 10 -->
                <div class="product-card" data-category="bleach" data-product-id="bleach-hoodie">
                    <img src="./images/products/bleach.png" alt="Bleach T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Bleach</span>
                        <h3>Bleach Hoodie</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 11 -->
                <div class="product-card" data-category="blue-lock" data-product-id="blue-lock-shirt">
                    <img src="./images/products/blue lock.png" alt="Blue Lock T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Blue Lock</span>
                        <h3>Blue Lock T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 12 -->
                <div class="product-card" data-category="chainsaw-man" data-product-id="chainsaw-man-shirt">
                    <img src="./images/products/chainsaw 2.png" alt="Chainsaw Man T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Chainsaw Man</span>
                        <h3>Chainsaw Man T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 13 -->
                <div class="product-card" data-category="death-note" data-product-id="death-note-shirt">
                    <img src="./images/products/death note.png" alt="Death Note T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Death Note</span>
                        <h3>Death Note T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 13 -->
                <div class="product-card" data-category="jujutsu-kaisen" data-product-id="gojo-shirt">
                    <img src="./images/products/gojo.png" alt="jujutsu Kaisen T Shirt">
                    <div class="product-info">
                        <span class="series-tag">jujutsu Kaisen</span>
                        <h3>jujutsu Kaisen T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 14 -->
                <div class="product-card" data-category="hunter-x-hunter" data-product-id="hunter-x-hunter-shirt">
                    <img src="./images/products/hunter x hunter.png" alt="Hunter X Hunter T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Hunter X Hunter</span>
                        <h3>Hunter X Hunter T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 15 -->
                <div class="product-card" data-category="mob-psycho" data-product-id="mob-psycho-shirt">
                    <img src="./images/products/mob.png" alt="Mob Psycho T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Mob Psycho</span>
                        <h3>Mob Psycho T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 16 -->
                <div class="product-card" data-category="demon-slayer" data-product-id="nezuko-crop-top">
                    <img src="./images/products/nezuko.png" alt="Nezuko T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Demon Slayer</span>
                        <h3>Nezuko Crop Top</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 17 -->
                <div class="product-card" data-category="demon-slayer" data-product-id="nine-tails-hoodie">
                    <img src="./images/products/nine tails.png" alt="Nine Tails T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Demon Slayer</span>
                        <h3>Nine Tails Hoodie</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 18 -->
                <div class="product-card" data-category="noragami" data-product-id="noragami-shirt">
                    <img src="./images/products/noragami.png" alt="Noragami T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Noragami</span>
                        <h3>Noragami T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 19 -->
                <div class="product-card" data-category="solo-leveling" data-product-id="solo-statue-shirt">
                    <img src="./images/products/solo statue.png" alt="Solo Leveling T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Solo Leveling</span>
                        <h3>Solo Leveling Statue T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 20 -->
                <div class="product-card" data-category="chainsaw-man" data-product-id="chainsaw-man2-shirt">
                    <img src="./images/products/chainsaw man.png" alt="Chainsaw Man T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Chainsaw Man</span>
                        <h3>Chainsaw Man Hoodie</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 21 -->
                <div class="product-card" data-category="vagabond" data-product-id="vagabond-shirt">
                    <img src="./images/products/vagabond.png" alt="Vagabond T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Vagabond</span>
                        <h3>Vagabond T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>

                <!-- Product 22 -->
                <div class="product-card" data-category="zenitsu-nezuko" data-product-id="zenitsu-nezuko-shirt">
                    <img src="./images/products/zenitsu nezuko.png" alt="Zenitsu Nezuko T Shirt">
                    <div class="product-info">
                        <span class="series-tag">Demon Slayer</span>
                        <h3>Zenitsu & Nezuko T Shirt</h3>
                        <div class="price">
                            <span class="original-price">₹800.00</span>
                            <span class="current-price">₹680.00</span>
                        </div>
                        <div class="product-actions">
                            <button class="wishlist-btn">♥</button>
                            <button class="select-btn">Select Options</button>
                        </div>
                    </div>
                </div>
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
                            <a href="#" class="add-to-cart-popup">Add to Cart</a>
                    </div>
                </div>
    </main>

    <!-- Footer (same as home.php) -->
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

    <script type="module" src="scripts/product-popup.js?v=2"></script>
</body>
</html>