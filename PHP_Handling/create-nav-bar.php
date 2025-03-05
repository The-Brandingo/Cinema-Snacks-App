<?php
session_start();
foreach (glob("../PHP_Handling/functions/*.php") as $filename) {
    include_once $filename;
}

echo '<nav class="top-bar">';
echo '<div class = "desktop-bar">';
echo '<div class = "nav-section left" >';
echo '<a href="../index.php" class="nav-links">Menu</a>';
echo '<a href="../Pages/account_overview.php" class="nav-links" >Account</a>';
echo '<div class="cart-header-display">';
$price = getCartTotal();
echo '<a href="../Pages/cart.php" class="cart-link">';
echo '<img src="../Images/shoppingcart.svg" class="shopping-cart-svg" alt="Shopping Cart Image" />';
echo '<span class="price-text">£' . $price . '</span>';
echo '</a>';
echo '</div>';
echo '</div>';
echo '<a href="../index.php" style="color: white;display:inline;font-size: 300%;font-weight: bold;margin:0;padding:0; ">EVERYMAN  </a>';
echo '<div class = "nav-section right" >';
echo '<div class="nav-form-container" >';
if (!isset($_SESSION['user_id'])) {
    echo '<button class="nav-log-button" onclick="location.href=\'login.php\'"> Login</button>';
} else {

    echo '<form action="../PHP_Handling/login_handler.php" method="post">';
    echo '<button type="submit" class="nav-log-button" name="try_logout" >' . $_SESSION['user_name'] . ' - Logout</button>';
    echo '</form>';
}
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class = "mobile-bar">'; // MOBILE TOP BAR
echo '<img src="../Images/hamburger.svg" class="hamburger-svg" onclick="hamburgerToggle()" alt="menu button" />';

echo '<div style = "margin-left:30px;padding:0;">';
echo '<p style="font-size: 130%;font-weight: bold;margin:0;padding: 0;letter-spacing: 1px;">EVERYMAN</p>';
echo '</div>';

echo '<div class="cart-header-display">';
$price = getCartTotal();
echo '<a href="../Pages/cart.php" class="cart-link ">';
echo '<img src="../Images/shoppingcart.svg" class="shopping-cart-svg" alt="Shopping Cart Image" />';
echo '<span class="price-text">£' . $price . '</span>';
echo '</a>';
echo '</div>';

echo '</div>';
echo '</nav>';

include '../HTML/hamburger-popout.php';