<?php
session_start();
foreach (glob("../PHP_Handling/functions/*.php") as $filename) {
    include_once $filename;
}


if (isset($_POST['add_card'])) {
    $card_number = $_POST['card_number'];
    $card_name = $_POST['payment_name'];
    $expiry = $_POST['expiry'];
    $cvv = $_POST['cvv'];
    $post_code = $_POST['post_code'];
    echo $expiry;
    $parsedDate = DateTime::createFromFormat('m/y', $expiry);

// Check if parsing is successful
    if ($parsedDate !== false) {
        // Format the date as 'YYYY-MM-DD'
        $formattedDate = $parsedDate->format('Y-m-d');

        // Output the result
        echo $formattedDate;
    } else {
        // Handle parsing error
        echo 'Invalid date format';
    }
    addCard($card_name, $card_number, $cvv, $formattedDate, $post_code);

} elseif (isset($_POST['delete_card'])) {
    $payment_id = $_POST['payment_id'];
    deleteCard($payment_id);
}