<?php
ob_start();

function getDatabase()
{
    static $database = null;

    // Debugging: Log when the function is called
    error_log("getDatabase() called");

    if ($database === null) {
        try {
            // Attempt to get an instance of the Database
            $database = Database::getInstance();

            // Debugging: Log successful database connection
            error_log("Database connection successfully established");
        } catch (Exception $e) {
            // Debugging: Log any exceptions thrown during the database connection
            error_log("Database connection error: " . $e->getMessage());
        }
    } else {
        // Debugging: Log when using the existing database instance
        error_log("Using existing database instance");
    }

    // Debugging: Log the database instance status
    if ($database) {
        error_log("Database instance is available");
    } else {
        error_log("Database instance is not available");
    }

    return $database;
}


function getConnection()
{
    static $connection = null;
    if ($connection === null) {
        $connection = getDatabase()->getConnection();
    }
    return $connection;
}


function getCartID()
{
    if (isset($_SESSION['user_id'])) {
        // If user is logged in, return user_id from session
        return $_SESSION['user_id'];
    } elseif (isset($_COOKIE['browser_id'])) {
        // If browser_id cookie is set, return it
        return $_COOKIE['browser_id'];
    } else {
        // If neither user_id nor browser_id is set, generate a new identifier
        $new_identifier = uniqid();
        setcookie('browser_id', $new_identifier, 2147483647, '/');
        return $new_identifier;
    }
}

function handleDuplicates()
{
    $connection = getConnection();

    $sql = "
SELECT
    Item_ID,
    Item_Type,
    COALESCE(Customer_ID, Browser_ID) AS Common_ID,
    SUM(Quantity) AS Total_Quantity
FROM
    shopping_cart
GROUP BY
    Item_ID,
    Item_Type,
    Common_ID
HAVING
    COUNT(*) > 1;
";

    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $totalQuantity = min($row['Total_Quantity'], 50);

            // Update the 'master' row with the new quantity
            $updateSql = "
        UPDATE shopping_cart
        SET Quantity = {$totalQuantity}
        WHERE
            Item_ID = '{$row['Item_ID']}'
            AND Item_Type = '{$row['Item_Type']}'
            AND (
                (Customer_ID IS NOT NULL AND Customer_ID = '{$row['Common_ID']}')
                OR
                (Customer_ID IS NULL AND Browser_ID = '{$row['Common_ID']}')
            )
        LIMIT 1;
        ";

            $connection->query($updateSql);

            // Delete the duplicate rows
            $deleteSql = "
        DELETE FROM shopping_cart
        WHERE
            Item_ID = '{$row['Item_ID']}'
            AND Item_Type = '{$row['Item_Type']}'
            AND (
                (Customer_ID IS NOT NULL AND Customer_ID = '{$row['Common_ID']}')
                OR
                (Customer_ID IS NULL AND Browser_ID = '{$row['Common_ID']}')
            )
            AND Quantity <> {$totalQuantity};
        ";

            $connection->query($deleteSql);
        }


    }

}

function updateCart($item_id, $item_type, $quantity)
{
    $connection = getConnection();
    $cart_id = getCartID();
    $table_entry = "Browser_ID";
    if (isset($_SESSION['user_id'])) {
        $table_entry = "Customer_ID";
    }
    if ($table_entry == "Browser_ID") {
        $query = "
    UPDATE shopping_cart
    SET shopping_cart.Quantity = $quantity
    WHERE shopping_cart.Browser_ID = '$cart_id'
      AND shopping_cart.Item_ID = $item_id
      AND shopping_cart.Item_Type = '$item_type'";
    } else {
        $query = "
    UPDATE shopping_cart
    SET shopping_cart.Quantity = $quantity
    WHERE shopping_cart.Customer_ID = '$cart_id'
      AND shopping_cart.Item_ID = $item_id
      AND shopping_cart.Item_Type = '$item_type'";
    }

    $connection->query($query);
    echo $table_entry;
    echo $query;
    header("Location: ../Pages/cart.php");

}

function deleteCartItem($item_id, $item_type)
{

    $connection = getConnection();
    $cart_id = getCartID();
    $table_entry = "Browser_ID";
    if (isset($_SESSION['user_id'])) {
        $table_entry = "Customer_ID";
    }

    if ($table_entry == "Browser_ID") {
        $query = "DELETE FROM shopping_cart WHERE Item_ID = $item_id AND Item_Type = '$item_type' AND Browser_ID = '$cart_id' ";
        $connection->query($query);
    } else {
        $query = "DELETE FROM shopping_cart WHERE Item_ID = $item_id AND Item_Type = '$item_type' AND Customer_ID = $cart_id ";
        $connection->query($query);
    }

    header("Location: ../Pages/cart.php");
    exit();

}


function addToCart($item_id, $item_type)
{
    $connection = getConnection();
    $cart_id = getCartID();

    $table_entry = "Browser_ID";
    if (isset($_SESSION['user_id'])) {
        $table_entry = "Customer_ID";
    }
    if ($table_entry == "Browser_ID") {
        $query = "INSERT INTO shopping_cart (Item_ID, Quantity, Item_Type, Browser_ID) VALUES ($item_id, 1, '$item_type', '$cart_id' )";
    } else {
        $query = "INSERT INTO shopping_cart (Item_ID, Quantity, Item_Type, Customer_ID) VALUES ($item_id, 1, '$item_type', $cart_id)";
    }
    $connection->query($query);
    header("Location: ../Pages/index.php");
    exit();

}

function getCartItems($item_type)
{
    $connection = getConnection();
    $cart_id = getCartID();
    //handleDuplicates();
    // if (isset($_COOKIE['browser_id']) && !isset($_SESSION['user_id'])) {
    $table_entry = "Browser_ID";
    if (isset($_SESSION['user_id'])) {
        $table_entry = "Customer_ID";
    }

    if ($item_type == "food") {
        $cartquery = "SELECT
        shopping_cart.Item_ID, 
        shopping_cart.Quantity,
        shopping_cart.Browser_ID,
        shopping_cart.Item_Type,
        food_items.Food_Name,
        food_items.Type,
        food_items.Price,
        food_items.Halal,
        food_items.Vegan,
        food_items.Vegetarian,
        food_items.Gluten_Free
        FROM shopping_cart
        INNER JOIN food_items ON shopping_cart.Item_ID = food_items.Food_ID
        WHERE shopping_cart.Item_Type = 'food'  AND (shopping_cart.Browser_ID = '$cart_id' or shopping_cart.Customer_ID = '$cart_id' )";
    } elseif ($item_type == "drink") {
        $cartquery = "SELECT
        shopping_cart.Item_ID, 
        shopping_cart.Quantity,
        shopping_cart.Browser_ID,
        shopping_cart.Item_Type,
        drink_items.Drink_Name,
        drink_items.Type,
        drink_items.Price
        FROM shopping_cart
        INNER JOIN drink_items ON shopping_cart.Item_ID = drink_items.Drink_ID
        WHERE shopping_cart.Item_Type = 'drink' AND (shopping_cart.Browser_ID = '$cart_id' or shopping_cart.Customer_ID = '$cart_id' )";
    }
    /*
if ($item_type == "food" and $table_entry == "Customer_ID") {
$cartquery = "SELECT
shopping_cart.Item_ID,
shopping_cart.Quantity,
shopping_cart.Customer_ID,
shopping_cart.Item_Type,
food_items.Food_Name,
food_items.Price
FROM shopping_cart
INNER JOIN food_items ON shopping_cart.Item_ID = food_items.Food_ID
WHERE shopping_cart.Item_Type = 'food' AND shopping_cart.Customer_ID = $user_id ";
} elseif ($item_type == "drink" and $table_entry == "Customer_ID") {
$cartquery = "SELECT
shopping_cart.Item_ID,
shopping_cart.Quantity,
shopping_cart.Customer_ID,
shopping_cart.Item_Type,
drink_items.Drink_Name,
drink_items.Price
FROM shopping_cart
INNER JOIN drink_items ON shopping_cart.Item_ID = drink_items.Drink_ID
WHERE shopping_cart.Item_Type = 'drink' AND shopping_cart.Customer_ID = $user_id ";
}
*/


    return $connection->query($cartquery);
}

function getCartItemCount()
{

    $totalItems = 0;
    $user_food_in_cart = getCartItems('food');
    $user_drink_in_cart = getCartItems('drink');

    while ($row = $user_food_in_cart->fetch_assoc()) {
        $totalItems += $row["Quantity"];
    }

    while ($row = $user_drink_in_cart->fetch_assoc()) {
        $totalItems += $row["Quantity"];
    }

    return $totalItems;
}


function getCartTotal()
{

    $user_food_in_cart = getCartItems('food');
    $user_drink_in_cart = getCartItems('drink');

    $totalPrice = 0.00;
    // Calculate total price for food items
    while ($row = $user_food_in_cart->fetch_assoc()) {
        $quantity = $row["Quantity"];
        $price = $row["Price"];
        $totalPrice += $quantity * $price;
    }
    // Calculate total price for drink items
    while ($row = $user_drink_in_cart->fetch_assoc()) {
        $quantity = $row["Quantity"];
        $price = $row["Price"];
        $totalPrice += $quantity * $price;
    }
    return format_price($totalPrice);
}

function tempCartSetup()
{
    $cart_id = getCartId();
    $connection = getConnection();
    $query = null;
    if (isset($_COOKIE['browser_id']) and !isset($_SESSION['user_id']) !== null) {
        $query = "INSERT INTO shopping_cart (Item_ID, Quantity, Item_Type, Browser_ID) VALUES(3, 3, 'food', '$cart_id'), (1, 3, 'drink', '$cart_id')";
    } else if (isset($_SESSION['user_id'])) {
        $query = "INSERT INTO shopping_cart (Item_ID, Quantity, Item_Type, Customer_ID) VALUES(3, 3, 'food', $cart_id),(1, 3, 'drink', $cart_id)";
    }
    $connection->query($query);
}

function cartAccountTransfer($customer_id, $browser_id)
{
    $connection = getConnection();
    $query = "UPDATE shopping_cart SET Customer_ID = ? WHERE Browser_ID = ? AND Customer_ID IS NULL";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("is", $customer_id, $browser_id);
    $stmt->execute();
    $stmt->close();

}


?>