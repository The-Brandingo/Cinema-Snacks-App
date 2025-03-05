<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../CSS/main-styles.css">
    <link rel="stylesheet" href="../CSS/overwrite.css">
    <link rel="stylesheet" href="../CSS/nav-bar.css">
    <link rel="stylesheet" href="../CSS/cart.css">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <script src="../JavaScript/popout-toggler.js" defer></script>
</head>
<body>

<?php require('../PHP_Handling/create-nav-bar.php'); ?>

<div class="cart-flex-container">
    <div class="white_plain_container" id="cart-container">

        <?php
        $database = Database::getInstance();
        $connection = $database->getConnection();
        foreach (glob("../PHP_Handling/functions/*.php") as $filename) {
            include_once $filename;
        }

        if (isset($_SESSION['user_id'])) {
            cartAccountTransfer($_SESSION['user_id'], $_COOKIE['browser_id']);
        }
        handleDuplicates();
        $user_food_in_cart = getCartItems("food");
        $user_drink_in_cart = getCartItems("drink");
        $cart_count = getCartItemCount();

        if ($user_drink_in_cart != 'fail' and $user_food_in_cart != 'fail') {
            if ($user_drink_in_cart->num_rows + $user_food_in_cart->num_rows > 0) {

                echo "<h style='font-weight: bold; font-size: x-large; margin-bottom: 10px;'> Your Cart (" . $cart_count . " Items) </h>";
                echo "<table class = \"cart-table\">"; // creates a table based on the table in the database im accessing

                echo "<tr></tr>";

                while ($row = $user_food_in_cart->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td style = 'font-weight:  bold' >" . $row["Type"] . "</td>";
                    echo "<td>" . $row["Food_Name"] . "</td>";
                    /*if ($row['Halal']) {
                        echo '<img style="max-width: 50px; max-height: 50px;" src="../Images/halal_icon.png" alt="Halal certification image">';
                    }*/
                    $price = format_price($row["Price"]);
                    $quantity_price = format_price($price * $row["Quantity"]);
                    if ($row["Quantity"] > 1) {
                        echo "<td>£$price (£$quantity_price)</td>";
                    } else {
                        echo "<td>£$price</td>";
                    }
                    echo "<td>";
                    echo '<form action="../PHP_Handling/cart_updater.php" method="post" class="select_container" id="cart_form">';
                    echo "<input type=\"hidden\" name=\"item_id\" value=\"{$row['Item_ID']}\">";
                    echo "<input type=\"hidden\" name=\"item_type\" value=\"{$row['Item_Type']}\">";
                    echo '<select name="Quantity" id="quantity_select" onchange="updateQuantity(this.form)">';
                    for ($i = 1; $i <= 50; $i++) {
                        $selected = ($i == $row['Quantity']) ? 'selected' : '';
                        echo "<option value=\"$i\" $selected>$i</option>";
                    }
                    echo '</select>';
                    echo '</form>';
                    echo "</td>";
                    echo "<td class = 'button_holder'>";
                    echo "<form action='../PHP_Handling/cart_updater.php' method='post'>";
                    echo "<input type=\"hidden\" name=\"delete_item_type\" value=\"{$row['Item_Type']}\">";
                    echo "<button type='submit' class = 'invisible-button' name='delete_item_from_cart' value ='" . $row["Item_ID"] . "' >X</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }

                while ($row = $user_drink_in_cart->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td style = 'font-weight:  bold' >" . $row["Type"] . "</td>";
                    echo "<td>" . $row["Drink_Name"] . "</td>";
                    $price = format_price($row["Price"]);
                    $quantity_price = format_price($price * $row["Quantity"]);
                    if ($row["Quantity"] > 1) {
                        echo "<td>£$price (£$quantity_price)</td>";
                    } else {
                        echo "<td>£$price</td>";
                    }
                    echo "<td>";
                    echo '<form action = "../PHP_Handling/cart_updater.php" method="post" class = "select_container" id = "cart_form">';
                    echo "<input type=\"hidden\" name=\"item_id\" value=\"{$row['Item_ID']}\">";
                    echo "<input type=\"hidden\" name=\"item_type\" value=\"{$row['Item_Type']}\">";
                    echo '<select name = "Quantity" id = "quantity_select" onchange="updateQuantity(this.form)">';
                    for ($i = 1; $i <= 50; $i++) {
                        $selected = ($i == $row['Quantity']) ? 'selected' : ''; // Check if the current option matches the cart quantity
                        echo "<option value=\"$i\" $selected>$i</option>";
                    }
                    echo '</select>';
                    echo '</form>';
                    echo "</td>";
                    echo "<td class = 'button_holder'>";
                    echo "<form action='../PHP_Handling/cart_updater.php' method='post'>";
                    echo "<input type=\"hidden\" name=\"delete_item_type\" value=\"{$row['Item_Type']}\">";
                    echo "<button type='submit' class = 'invisible-button' name='delete_item_from_cart' value ='" . $row["Item_ID"] . "' >X</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "Your cart is empty.";
            }
        }


        ?>
    </div>
    <div class="checkout_box">
        <header style="font-size: 25px;"><b>Total</b></header>
        <hr>
        <?php

        $price = getCartTotal();

        // Now $totalPrice contains the total price of all items in the cart
        echo "<p > Total Price: £" . number_format($price, 2) . " </p>";
        echo '<hr>';
        $user_id = null;
        $redirect_page = "";

        if (isset($_SESSION['user_id'])) {

            if ($cart_count > 0) {
                $redirect_page = "../Pages/checkout.php";
            } else if ($cart_count < 1) {
                $redirect_page = "../Pages/cart.php";
            }
        } else {
            $redirect_page = "../Pages/login.php";
        }
        ?>
        <button type="button" name="checkout" class="cart-checkout-button"
                onclick="location.href='<?php echo $redirect_page; ?>'"
        >Checkout
        </button>

    </div>
</div> <!-- ending big container for everything to do with cart/checking out-->


<script> // a bit of javascript to make the dropdown menu for changing quantity automatically submit the form
    function updateQuantity(form) {
        form.submit();
    }

    function headToCheckout($user_id, $items) {
        if ($user_id !== null && $items > 0) {
            window.location.href = "../Pages/checkout.php";
        } else if ($items < 1) {
            window.location.href = "../Pages/cart.php";
        } else {
            window.location.href = "../Pages/login.php";
        }
    }
</script>
</body>
</html>


