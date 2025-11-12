<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <nav>
        <ul class="main-menu">
            <li><a href="./home.php">Home</a></li>
            <li class="dropdown">
                <a href="shop.php#all">Categories</a>
                <ul class="dropdown-menu">
                    <li><a class="ddtxt" href="shop.php#one-piece">One Piece</a></li>
                    <li><a class="ddtxt" href="shop.php#attack-on-titan">Attack on Titan</a></li>
                    <li><a class="ddtxt" href="shop.php#demon-slayer">Demon Slayer</a></li>
                    <li><a class="ddtxt" href="shop.php#chainsaw-man">Chainsaw Man</a></li>
                    <li><a class="ddtxt" href="shop.php#naruto">Naruto</a></li>
                    <li><a class="ddtxt" href="shop.php#hunter-x-hunter">Hunter X Hunter</a></li>
                    <li><a class="ddtxt" href="shop.php#berserk">Berserk</a></li>
                    <li><a class="ddtxt" href="shop.php#blue-lock">Blue Lock</a></li>
                    <li><a class="ddtxt" href="shop.php#bleach">Bleach</a></li>
                    <li><a class="ddtxt" href="shop.php#black-clover">Black Clover</a></li>
                    <li><a class="ddtxt" href="shop.php#noragami">Noragami</a></li>
                    <li><a class="ddtxt" href="shop.php#solo-leveling">Solo Leveling</a></li>
                    <li><a class="ddtxt" href="shop.php#jujutsu-kaisen">Jujutsu Kaisen</a></li>
                    <li><a class="ddtxt" href="shop.php#mob-psycho">Mob Psycho 100</a></li>
                    <li><a class="ddtxt" href="shop.php#jojo">Jojo</a></li>
                    <li><a class="ddtxt" href="shop.php#vagabond">Vagabond</a></li>
                </ul>
            </li>
            <li><a href="shop.php#all">Shop</a></li>
            <li><a href="order-tracking.php">Order Tracking</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="user-options">
            <form id="search-form" action="search.html" method="GET">
                <input type="text" name="query" placeholder="Search products..." required>
                <button type="submit">Search</button>
            </form>

            <?php if (isset($_SESSION['customer_id'])): ?>
                <span style="color: white;">Hi, <?php echo htmlspecialchars($_SESSION['customer_name']); ?></span>
                <a href="logout.php">Logout</a>
            <?php elseif (isset($_SESSION['staff_id'])): ?>
                <span style="color: white;">Hi, <?php echo htmlspecialchars($_SESSION['staff_name']); ?> (Delivery)</span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; 
            
            $cart_count = 0;
            $cart_total = 0.00;

            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $c) {
                    $qty = isset($c['qty']) ? (int)$c['qty'] : 1;
                    $price = isset($c['price']) ? (float)$c['price'] : 0;
                    $cart_count += $qty;
                    $cart_total += $price * $qty;
                }
            }
            ?>
            <a href="cart.php" class="cart-link">
                Cart (<span id="cart-count"><?php echo $cart_count; ?></span>)
            </a>
        </div>
    </nav>
</header>
