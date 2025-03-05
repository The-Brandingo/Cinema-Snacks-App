<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../CSS/overwrite.css">
    <link rel="stylesheet" href="../CSS/main-styles.css">
    <link rel="stylesheet" href="../CSS/nav-bar.css">
    <link rel="stylesheet" href="../CSS/popout.css">
    <link rel="stylesheet" href="../CSS/account-overview.css">

    <script src="../JavaScript/card-functions.js" defer></script>
    <script src="../JavaScript/format-functions.js" defer></script>
    <script src="../JavaScript/popout-toggler.js" defer></script>
    <title>Create an account</title>

</head>
<body>

<?php require('../PHP_Handling/create-nav-bar.php');

if (!isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") { // check if the user is logged in and if so redirect them away from the login page
    header("Location:../Pages/login.php");
}
?>


<div class="container">
    <header class="account-header">
        Account
        Overview
    </header>

    <div class="card">
        <h2>Personal Information</h2>
        <?php
        $customer_id = $_SESSION['user_id'];

        $connection = getConnection();
        $cart_id = getCartID();
        $query = "SELECT
        customer.First_Name,
        customer.Surname,
        customer.Email,
        customer.Phone_Number,
        customer.Date_of_Birth
        FROM customer
        WHERE customer.Customer_ID = $customer_id ";

        $customer_query = $connection->query($query);

        if ($customer_query !== false) {
            if ($customer_query->num_rows > 0) {
                $customer_data = $customer_query->fetch_assoc();
                if (isset($_SESSION['account_detail_error'])) {
                    echo '<p style="color: red;">' . $_SESSION['account_detail_error'] . '</p>';//
                    unset($_SESSION['account_detail_error']);
                }
                echo '<form action = "../PHP_Handling/edit_account_handler.php" method = "post">';
                echo '<span id = "personal-info-box" class="fullName" <p> Full Name: ' . $customer_data['First_Name'] . ' ' . $customer_data['Surname'] . ' </p></span> ';
                echo '<hr>';
                echo '<span id = "personal-info-box" class="dateOfBirth"><p>Date of Birth:  ' . $customer_data['Date_of_Birth'] . '</p></span>';
                echo '<hr>';
                echo '<span id = "personal-info-box" class="phoneNumber"><p>Phone Number: ' . $customer_data['Phone_Number'] . '</p></span> ';
                echo '<hr>';
            } else {
                echo '<p>No customer found for the given ID</p>';
            }
        } else {
            echo '<p>Query failed: ' . $connection->error . '</p>';
        }
        echo '<button type = "button" name ="edit" data-firstname= "' . $customer_data['First_Name'] . '" data-surname= "' . $customer_data['Surname'] . '" class="editButton"  >Edit</button>';
        echo '</form>';
        ?>


    </div>


    <div class="card">
        <h2>Email Address</h2>
        <?php
        $customer_id = $_SESSION['user_id'];

        $connection = getConnection();

        $query = "SELECT
        customer.Email
        FROM customer
        WHERE customer.Customer_ID = $customer_id ";

        $customer_query = $connection->query($query);

        if ($customer_query !== false) {
            if ($customer_query->num_rows > 0) {
                $customer_data = $customer_query->fetch_assoc();

                if (isset($_SESSION['account_edit_error'])) {
                    echo '<p style="color: red;">' . $_SESSION['account_edit_error'] . '</p>';//
                    unset($_SESSION['account_edit_error']);
                }
                if (isset($_SESSION['password_no_match'])) {
                    echo '<p style="color: red;">' . $_SESSION['password_no_match'] . '</p>';//
                    unset($_SESSION['password_no_match']);
                }
                echo '<form action = "../PHP_Handling/edit_account_handler.php" method = "post">';
                echo '<span id = "personal-info-box" class="emailAddress"> <p> Email: ' . $customer_data['Email'] . ' </p></span>';
            } else {
                echo '<p>No customer found for the given ID</p>';
            }
        } else {
            echo '<p>Query failed: ' . $connection->error . '</p>';
        }
        echo '<button type = "button" name ="edit" class="editEmail" >Edit</button>';
        echo '</form>';
        ?>
    </div>

    <div class="card">
        <form action="../PHP_Handling/edit_account_handler.php" method="post">
            <h2>Password</h2>
            <?php if (isset($_SESSION['new_password_no_match'])) {
                echo '<p style="color: red;">' . $_SESSION['new_password_no_match'] . '</p>';//
                unset($_SESSION['new_password_no_match']);
            } ?>
            <span class="passwordHolder"> Password: ******** </span>
            <button style="margin-top: 20px;" type="button" class="changePassword" name="edit">Change
                Password
            </button>
        </form>
    </div>

    <div class="card">
        <h2>Payment Information</h2>
        <div class="edit-card-container">

            <label for="cardSelector">Select a Card:</label>
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
            <div style="display: flex; align-items: baseline;">

                <button
                        onclick="popoutToggler('add-card-popout')">Add Card
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Order History</h2>


    </div>
</body>

<?php include '../HTML/add-card-popout.html'; ?>
<?php include '../HTML/delete-card-popout.html' ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
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
        displayCard(displayPayID, displayName, displayCardNumber, displayExpiry, cardInfoBox, false);
    }

    window.onload = function () {
        displayCardInfo();
    }

    $(document).ready(function () {
        $('.editEmail').click(function () {
            const emailSpan = $('.emailAddress');
            const emailEditButton = $('.editEmail');
            const emailLabel = $('<label for="password">Enter New Email:</label>');
            const emailAddressInput = $('<input type="email" id="emailAddress" name = "new_email" placeholder="' + emailSpan.text().split(':')[1].trim() + '">');
            const passwordLabel = $('<label for="password">Enter Password:</label>');
            const passwordInput = $('<input type="password" id="password" name="password" placeholder="********" required>');
            const passwordInputContainer = $('<div>');

            const saveButton = $('<button class = "saveButton" type="submit" name = "edit_email"> Save</button>');
            passwordInputContainer.append(passwordLabel, passwordInput, emailLabel, emailAddressInput);

            emailSpan.hide();
            emailEditButton.hide();

            emailSpan.replaceWith(passwordInputContainer);
            emailEditButton.replaceWith(saveButton);
        });

        $('.changePassword').click(function () {
            const passwordButtonSpan = $('.changePassword');
            const passwordHolder = $('.passwordHolder');

            const passwordLabel = $('<label for="password">Current Password:</label>');
            const passwordInput = $('<input type="password" id="password" name="password" required>');
            const newPasswordLabel = $('<label for="password">New Password:</label>');
            const newPasswordInput = $('<input type="password" id="password" name="new_password" required>');
            const newRePasswordLabel = $('<label for="password"> Re-Enter Password:</label>');
            const newRePasswordInput = $('<input type="password" id="password" name="new_re_password" required>');
            const passwordInputContainer = $('<div>');

            const saveButton = $('<button class = "saveButton" type="submit" name = "edit_password"> Save</button>');
            passwordInputContainer.append(passwordLabel, passwordInput, newPasswordLabel, newPasswordInput, newRePasswordLabel, newRePasswordInput);

            passwordHolder.hide();
            passwordButtonSpan.hide();

            passwordHolder.replaceWith(passwordInputContainer);
            passwordButtonSpan.replaceWith(saveButton);

        });


        $('.editButton').click(function () {


            const fullNameSpan = $('.fullName');
            const dobSpan = $('.dateOfBirth');
            const phoneNumberSpan = $('.phoneNumber');
            const firstName = $(this).data('firstname');
            const surname = $(this).data('surname');
            const editButton = $('.editButton');

            const newContainer = $('<div>');


            // Create new input fields
            const firstNameInput = $('<input type="text" id="firstName" name = "new_first_name" maxlength="100" placeholder="First Name: ' + (firstName || '') + '">');
            const surnameInput = $('<input type="text" id="surname" name = "new_surname" maxlength="100" placeholder="Surname: ' + (surname || '') + '">');

            const dobInput = $('<input type="date" id="dob" name = "new_dob" pattern="\d{2}/\d{2}/\d{4}" placeholder="' + dobSpan.text().split(':')[1].trim() + '">');
            const phoneNumberInput = $('<input type="tel" id="phoneNumber" name = "new_phone_number"  maxlength="11" placeholder="Phone Number: ' + phoneNumberSpan.text().split(':')[1].trim() + '">');
            const saveButton = $('<button class = "saveButton" type="submit" name = "edit_account_details"> Save</button>');
            // place first and surname inputs into a container to replace the fullname span
            newContainer.append(firstNameInput, surnameInput)
            // Hide old spans
            fullNameSpan.hide();
            dobSpan.hide()
            phoneNumberSpan.hide()

            // Replace old spans with new inputs
            fullNameSpan.replaceWith(newContainer);
            dobSpan.replaceWith(dobInput);
            editButton.replaceWith(saveButton);
            phoneNumberSpan.replaceWith(phoneNumberInput);
            // etc.
        });

    });


</script>
</html>

