<?php
foreach (glob("../PHP_Handling/functions/*.php") as $filename) {
    include_once $filename;
}
session_start();
if (isset($_POST['process-order'])) {
    $cvv = $_POST['cvv'];
    $seat = $_POST['seat_number'];
    $row = $_POST['row'];
    $screen = $_POST['screen_number'];
    $customer = $_SESSION['user_id'];
    $card = $_POST['selected-card'];
    $connection = getConnection();
    $seat = $seat . $row;

    // Start transaction
    mysqli_begin_transaction($connection);

    try {
        // Insert into food_order_bridge
        $food_order = "INSERT INTO food_order_bridge (Food_ID, Quantity)
                       SELECT Item_ID, Quantity
                       FROM shopping_cart
                       WHERE Item_Type = 'food' AND Customer_ID = $customer ";
        mysqli_query($connection, $food_order);
        $food_order_id = mysqli_insert_id($connection);

        // Insert into drink_order_bridge
        $drink_order = "INSERT INTO drink_order_bridge (Drink_ID, Quantity)
                        SELECT Item_ID, Quantity
                        FROM shopping_cart
                        WHERE Item_Type = 'drink' AND Customer_ID = $customer ";
        mysqli_query($connection, $drink_order);
        $drink_order_id = mysqli_insert_id($connection);

        // Insert into customer_order
        $customer_order_query = "INSERT INTO customer_order (Food_Order_ID, Drink_Order_ID, Seat_Number, Screen_Number, Customer_ID) 
                                 VALUES ($food_order_id, $drink_order_id, '$seat', $screen, $customer)";
        mysqli_query($connection, $customer_order_query);

        $clear_cart_query = "DELETE FROM shopping_cart WHERE Customer_ID = $customer ";
        mysqli_query($connection, $clear_cart_query);

        // Commit the transaction
        mysqli_commit($connection);


        // Close the database connection
        mysqli_close($connection);
        header("Location:../Pages/thank_you.php");
        exit();
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        mysqli_rollback($connection);
        header("Location:../Pages/checkout.php");
    }
}