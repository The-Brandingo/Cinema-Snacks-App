<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Snacks CRUD</title>

    <link rel="stylesheet" href="../CSS/overwrite.css">
    <link rel="stylesheet" href="../CSS/nav-bar.css">
    <link rel="stylesheet" href="../CSS/main-styles.css">
    <link rel="stylesheet" href="../CSS/checkout.css">
    <link rel="stylesheet" href="../CSS/popout.css">
    <script src="../JavaScript/card-functions.js" defer></script>
    <script src="../JavaScript/popout-toggler.js" defer></script>
    <script src="../JavaScript/format-functions.js" defer></script>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
</head>
<body>

<?php require('../PHP_Handling/create-nav-bar.php');

if (!isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") { // check if the user is logged in and if so redirect them away from the login page
    header("Location:../Pages/login.php");
}
?>

<div class="checkout-flex-container">
    <div class="all-payment-container">
        <div class="white_plain_container" id="total-window">
            <h1 style="margin:0;padding: 0;">Summary</h1>
            <hr style="margin:0;padding: 0;">
            <?php
            echo '<div style="align-items: center; display: flex; flex-direction: column;margin:0;padding: 0;">';
            $price = getCartTotal();
            echo '<div style="display: flex;justify-content:space-between;margin:0;padding: 0;width: 100%;">';
            echo '<p style="font-size:large;align-content: flex-start;margin:4px;"> Cart Total</p>';
            echo '<p style="font-size: large;align-content: flex-end;margin:4px;"> £' . number_format($price, 2) . ' </p>';
            echo '</div>';

            echo '<div style="display: flex;justify-content:space-between;margin:0;padding: 0;width: 100%;">';
            echo '<p style="font-size: large; margin:4px;margin-bottom: 2px;">Discounts</p>';
            echo '<p style="font-size: large; margin:4px;margin-bottom: 2px;">£0.00 </p>';
            echo '</div>';
            // Now $totalPrice contains the total price of all items in the cart
            echo '<hr style="background-color: black;width: 100%;">';
            echo '<div style="display: flex;justify-content:space-between;margin:0;padding: 0;width: 100%;">';
            echo '<p style="font-size: larger;margin:5px;margin-top:3px;"> TOTAL TO PAY </p>';
            echo '<p style="font-weight: bold; font-size: larger;margin:5px;margin-top: 3px;">£' . number_format($price, 2) . ' </p>';

            echo '</div>';
            echo '</div>';
            ?>

        </div>

        <div class="white_plain_container" id="payment-box">
            <h1 style="margin:0;padding: 0;">Payment</h1>
            <hr style="margin:0;padding: 0;">
            <div style="margin:5%">

                <div style="display: flex; align-items: baseline;">
                    <label for="cardSelector">Select a Card:</label>
                    <button style='padding:4px;margin:1px; margin-bottom: 2px;margin-left: auto;'
                            onclick="popoutToggler('add-card-popout')">Add Card
                    </button>
                </div>
                <select style='width: 100%' id="cardSelector" onchange="displayCardInfo()">

                    <?php
                    $cardsResult = getCards();

                    // Fetch all cards into an array
                    if ($cardsResult->num_rows > 0) {
                        $cards = [];
                        while ($row = $cardsResult->fetch_assoc()) {
                            $cards[] = $row;
                        }
                    } else {
                        echo "<option>No cards found...</option>";
                    }

                    foreach ($cards as $card) {
                        $paymentID = $card["Payment_ID"];
                        $cardNumber = substr($card["Card_Number"], -4);
                        $hiddenCardNumber = str_repeat('&bull;', strlen($card["Card_Number"]) - 4);
                        $paymentName = $card["Payment_Name"];
                        $expiry = $card['Expiry'];

                        // Encode card information as JSON and escape for HTML attribute
                        $encodedCardInfo = htmlspecialchars(json_encode([
                            'Payment_ID' => $paymentID,
                            'Card_Number' => $cardNumber,
                            'Payment_Name' => $paymentName,
                            'Expiry' => date('m/y', strtotime($expiry))
                        ]), ENT_QUOTES, 'UTF-8');

                        echo "<option value='$paymentID' data-card='$encodedCardInfo'>Card ending in $cardNumber</option>";
                    }
                    ?>
                </select>

                <div id="cardInfoBox">

                </div>

            </div>

        </div>
        <!-- end of card part -->

        <div class="white_plain_container" id="seat-selection">
            <form id="seat-info"
                  style="margin: 0; padding: 4px; display: flex; width: 100%; justify-content: space-between;">
                <div style="display: flex; flex: 1; align-items: center;">
                    <label style="padding:0;margin:0;" for="screen_number">Screen:</label>
                    <select style="width: 20px;font-weight:bold;font-family:monospace;" id="screen_number"
                            name="screen_number" required>
                        <?php
                        for ($i = 1; $i <= 10; $i++) {
                            echo "<option style = 'font-weight:bold;font-family:monospace;' value=\"$i\">$i</option>";
                        }
                        ?>
                    </select>
                </div>

                <div style="display: flex; flex: 0.9; align-items: center;">
                    <label style="padding:0;margin:0;" for="seat_number">Seat:</label>
                    <select style="width: 20px;font-weight:bold;font-family:monospace;" id="seat_number"
                            name="seat_number" required>
                        <?php
                        for ($i = 1; $i <= 10; $i++) {
                            echo "<option style = 'font-weight:bold;font-family:monospace;' value=\"$i\">$i</option>";
                        }
                        ?>
                    </select>
                </div>

                <div style="display: flex; flex: 0.8; align-items: center;">
                    <label style="padding:0;margin:0;" for="row">Row:</label>
                    <select style="width: 20px;font-weight:bold;font-family:monospace;" id="row" name="row" required>
                        <?php
                        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                        foreach ($rows as $row) {
                            echo "<option style = 'font-weight:bold;font-family:monospace;' value=\"$row\">$row</option>";
                        }
                        ?>
                    </select>
                </div>
            </form>
        </div>

        <?php echo '<button onclick="submitCombinedForm()" type="button" class="green-pay-button">PAY £' . number_format($price, 2) . '</button>' ?>
    </div>

    <div class="white_plain_container" id="checkout_container">

        <?php
        $database = Database::getInstance();
        $connection = $database->getConnection();
        foreach (glob("../PHP_Handling/functions/*.php") as $filename) {
            include_once $filename;
        }
        if (isset($_SESSION['user_id'])) {
            cartAccountTransfer($_SESSION['user_id'], $_COOKIE['browser_id']);
        }

        $user_food_in_cart = getCartItems("food");
        $user_drink_in_cart = getCartItems("drink");
        $item_count = getCartItemCount();
        $price = number_format(getCartTotal(), 2);
        if ($user_drink_in_cart != 'fail' and $user_food_in_cart != 'fail') {
            if ($user_drink_in_cart->num_rows + $user_food_in_cart->num_rows > 0) {
                echo "<table class = \"checkout-items\">"; // creates a table based on the table in the database im accessing
                echo "<div style='display: flex; align-items: center; justify-content: space-between;'>";
                echo "<h1 style='font-weight: bold; font-size: 27px;'>" . $item_count . " Items</h1>";
                echo "<button> Edit Cart</button>";
                echo "</div>";

                while ($row = $user_food_in_cart->fetch_assoc()) {

                    echo "<tr class = 'top-checkout-row' >";
                    echo "<td style = 'font-weight: bold;font-size: 20px' class = \"item-head\"><p style ='font-size: 17px;display:inline;margin:0;text-decoration: underline;font-weight: bold'>" . $row["Type"] . "</p> " . $row["Food_Name"] . "</td>";
                    echo "</tr>";
                    echo "<tr class ='checkout-items-bottom-row' >";
                    $price = format_price($row["Price"]);
                    $quantity_price = format_price($price * $row["Quantity"]);
                    if ($row["Quantity"] > 1) {
                        echo "<td class='amount-data'>";
                        echo "<p class='checkout-price-left'>£$price (£$quantity_price)</p>";

                        echo "<p class='checkout-quantity-right'>Quantity: " . $row["Quantity"] . "</p>";
                        echo "</td>";
                    } else {
                        echo "<td class='amount-data'>";
                        echo "<p class='checkout-price-left'>£$price</p>";

                        echo "<p class='checkout-quantity-right'>Quantity: " . $row["Quantity"] . "</p>";
                        echo "</td>";
                    }
                    echo "</tr>";


                }
                while ($row = $user_drink_in_cart->fetch_assoc()) {
                    echo "<tr class = 'top-checkout-row' >";
                    echo "<td style = 'font-weight: bold;font-size: 20px' class = \"item-head\"><p style ='font-size: 17px;display:inline;margin:0;text-decoration: underline;font-weight: bold'>" . $row["Type"] . "</p> " . $row["Drink_Name"] . "</td>";
                    echo "</tr>";
                    echo "<tr class ='checkout-items-bottom-row' >";
                    $price = format_price($row["Price"]);
                    $quantity_price = format_price($price * $row["Quantity"]);
                    if ($row["Quantity"] > 1) {
                        echo "<td class='amount-data'>";
                        echo "<p class='checkout-price-left'>£$price (£$quantity_price)</p>";

                        echo "<p class='checkout-quantity-right'>Quantity: " . $row["Quantity"] . "</p>";
                        echo "</td>";
                    } else {
                        echo "<td class='amount-data'>";
                        echo "<p class='checkout-price-left'>£$price</p>";

                        echo "<p class='checkout-quantity-right'>Quantity: " . $row["Quantity"] . "</p>";
                        echo "</td>";
                    }
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "Your cart is empty.";
            }


        }


        ?>
    </div>

</div> <!-- ending big container for everything to do with cart/checking out-->


<?php include '../HTML/add-card-popout.html'; ?>

<?php include '../HTML/delete-card-popout.html' ?>

<script>


    function submitCombinedForm() {
        // Gather data from Form 1
        var dataForm1 = new FormData();

// Assuming 'seat-info' is the ID of your form element
        var seatInfoForm = document.getElementById('seat-info');

// Iterate through all dropdown selectors in 'seat-info' form
        var dropdowns = seatInfoForm.querySelectorAll('select');
        dropdowns.forEach(function (dropdown) {
            dataForm1.append(dropdown.name, dropdown.value);
        });

        // Gather data from Form 2
        var selectField = document.getElementById('cardSelector');
        var selectedOption = selectField.options[selectField.selectedIndex];
        var dataForm2 = new FormData();
        dataForm2.append('selected-card', selectedOption.value);

        // Gather data from Form 3
        var dataForm3 = new FormData();

// Assuming 'cvvInput' is the ID of your input element
        var cvvInput = document.getElementById('cvv');

// Assuming 'cvv' is the name of the input field
        dataForm3.append('cvv', cvvInput.value);

        // Create a new form
        var combinedForm = document.createElement('form');
        combinedForm.method = 'post'; // Adjust method as needed
        combinedForm.action = '../PHP_Handling/order_process.php'; // Adjust action URL as needed
        appendFormField(combinedForm, 'process-order', true)
        // Append data from Form 1
        for (var pair of dataForm1.entries()) {
            appendFormField(combinedForm, pair[0], pair[1]);
        }

        // Append data from Form 2
        for (var pair of dataForm2.entries()) {
            appendFormField(combinedForm, pair[0], pair[1]);
        }

        // Append data from Form 3
        for (var pair of dataForm3.entries()) {
            appendFormField(combinedForm, pair[0], pair[1]);
        }

        // Append the new form to the document
        document.body.appendChild(combinedForm);

        // Submit the combined form
        combinedForm.submit();
    }

    function appendFormField(form, name, value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }

    function displayCardInfo() {
        var cardSelector = document.getElementById("cardSelector");

        var selectedOption = cardSelector.options[cardSelector.selectedIndex]; // checks the select cardSelector
        var selectedCardInfo = selectedOption.getAttribute("data-card"); // finds the selectedCard
        var selectedCard = JSON.parse(selectedCardInfo); // parses the data

        var displayCardNumber = partialHash(selectedCard['Card_Number']);
        var displayName = selectedCard['Payment_Name'];
        var displayPayID = selectedCard['Payment_ID'];
        var displayExpiry = selectedCard['Expiry'];

        var selectedCardNumberElement = document.getElementById("selectedCardNumber");
        selectedCardNumberElement.textContent = selectedCard['Card_Number'];

        var cardInfoBox = document.getElementById("cardInfoBox");
        displayCard(displayPayID, displayName, displayCardNumber, displayExpiry, cardInfoBox, true);
    }


    window.onload = function () {
        displayCardInfo();
    }

</script>

</body>
</html>


