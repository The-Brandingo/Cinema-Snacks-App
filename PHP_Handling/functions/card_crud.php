<?php

function getCards()
{
    $connection = getConnection();
    $customer = $_SESSION['user_id'];
    $query = "SELECT 
    payment_customer_bridge.Payment_ID,
    payment_customer_bridge.Customer_ID,
    payment_info.Payment_ID,
    payment_info.Payment_Name,
    payment_info.Card_Number,
    payment_info.Expiry
    FROM payment_customer_bridge
    INNER JOIN payment_info ON payment_customer_bridge.Payment_ID = payment_info.Payment_ID
    WHERE payment_customer_bridge.Customer_ID = ? ";

    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $customer);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;

}

function addCard($card_name, $card_number, $cvv, $expiry, $post_code)
{
    echo 'adding card';
    $connection = getConnection();
    $customer = $_SESSION['user_id'];
    $query = "INSERT INTO payment_info (Payment_Name, Card_Number, Expiry, Security_Code, Post_Code) VALUES (?,?,?,?,?) ";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sssss", $card_name, $card_number, $expiry, $cvv, $post_code);
    $success = $stmt->execute();
    $lastInsertedID = $connection->insert_id;
    $result = $stmt->get_result();
    $stmt->close();

    if (!$success) {
        echo 'error';
    } else {
        $query = "INSERT INTO payment_customer_bridge (Payment_ID, Customer_ID) VALUES (?,?) ";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ss", $lastInsertedID, $customer);
        $success = $stmt->execute();
        $stmt->close();
        header("Location:../Pages/checkout.php");
        exit();
    }

}

function deleteCard($payment_id)
{
    if ($payment_id) {
        $connection = getConnection();

        $sql = "DELETE FROM payment_info WHERE Payment_ID = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $payment_id);
        $delete_card_success = $stmt->execute();
        $stmt->close();
        if ($delete_card_success) {
            $sql = "DELETE FROM payment_customer_bridge WHERE Payment_ID = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $payment_id);
            $delete_bridge_entry = $stmt->execute();
            $stmt->close();
            if ($delete_bridge_entry) {
                echo 'success';
            } else {
                echo 'error';
            }
        } else {
            echo 'error deleting card';
        }
    } else {
        echo 'Payment ID could not be found';
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();

}