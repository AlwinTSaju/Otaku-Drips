<?php
// Determine current page for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
$nav_links = [
    'admin-dashboard.php' => 'Overview',
    'products.php' => 'Products',
    'customers.php' => 'Customers',
    'orders.php' => 'Orders',
    'delivery-staff-list.php' => 'Delivery Staff',
];
?>
<aside class="sidebar">
    <div class="brand">
        <h1 class="brand-title">OTAKU DRIPS</h1>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($nav_links as $page => $label): ?>
            <a href="<?php echo $page; ?>" class="nav-link <?php echo $current_page === $page ? 'active' : ''; ?>">
                <?php echo $label; ?>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
