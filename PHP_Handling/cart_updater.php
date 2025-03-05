<?php
session_start();
foreach (glob("functions/*.php") as $filename) {
    include_once $filename;
}

if (isset($_POST['item_id'], $_POST['item_type'], $_POST['Quantity'])) {
    $quantity = $_POST['Quantity'];
    $item_id = $_POST['item_id'];
    $item_type = $_POST['item_type'];
    $browser_id = $_COOKIE['browser_id'];
    updateCart($item_id, $item_type, $quantity);
} elseif (isset($_POST['delete_item_from_cart'], $_POST['delete_item_type'])) {
    $item_id = $_POST['delete_item_from_cart'];
    $item_type = $_POST['delete_item_type'];
    deleteCartItem($item_id, $item_type);
} elseif (isset($_POST['added_item_to_cart'])) {
    $item_id = $_POST['added_item_to_cart'];
    $item_type = $_POST['added_item_type'];
    addToCart($item_id, $item_type);
}
echo $_COOKIE['browser_id'];

?>