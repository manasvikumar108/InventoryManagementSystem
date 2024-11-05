<?php
// Get the current page name
$currentPage = basename($_SERVER['PHP_SELF']);

// Left-side navigation bar
echo '<link rel="stylesheet" href="Style/navbar.css">'; // Link to navbar CSS
echo '<div class="navbar">'; // Use class instead of inline styles
echo '<ul>';
echo '<li><a href="index.php" class="' . ($currentPage == 'index.php' ? 'active' : '') . '">Dashboard</a></li>';
echo '<li><a href="items.php" class="' . ($currentPage == 'items.php' ? 'active' : '') . '">Items</a></li>';
echo '<li><a href="categories.php" class="' . ($currentPage == 'categories.php' ? 'active' : '') . '">Categories</a></li>';
echo '<li><a href="customers.php" class="' . ($currentPage == 'customers.php' ? 'active' : '') . '">Customers</a></li>';
echo '<li><a href="billing.php" class="' . ($currentPage == 'billing.php' ? 'active' : '') . '">Billing</a></li>';
echo '<li><a href="logout.php" class="' . ($currentPage == 'logout.php' ? 'active' : '') . '">Logout</a></li>';
echo '</ul>';
echo '</div>';
?>
