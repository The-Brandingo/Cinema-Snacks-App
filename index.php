<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Everyman Menu</title>
    <link rel="stylesheet" href="CSS/main-styles.css">
    <link rel="stylesheet" href="CSS/nav-bar.css">
    <link rel="stylesheet" href="CSS/overwrite.css">
    <script src="JavaScript/popout-toggler.js" defer></script>
</head>
<body>
<?php include('../PHP_Handling/create-nav-bar.php'); ?>

<?php
// PHP code to fetch and display snacks data from the database
foreach (glob("../PHP_Handling/functions/*.php") as $filename) {
    include_once $filename;
}

$database = Database::getInstance();
$connection = $database->getConnection();
$food_items = "SELECT * FROM food_items";

$food_menu = $connection->query($food_items);

if ($food_menu->num_rows > 0) {
    $tables = []; // Array to store separate tables

    while ($row = $food_menu->fetch_assoc()) {
        $type = $row["Type"];
        if (!isset($tables[$type])) {
            $tables[$type] = [];
        }
        $tables[$type][] = $row;
    }
    echo '<div class="menu-container" >';

    // Display tables
    foreach ($tables as $type => $table) {
        echo '<div class="white_plain_container" style = "margin:0;padding:0;margin-top:5%;"> ';
        echo "<h2>$type</h2>";
        echo "<table class = \"menu-table\">";
        echo "<tr><th></th><th></th><th></th></tr>";

        foreach ($table as $row) {
            echo "<tr>";
            echo "<td>" . $row["Food_Name"] . "</td>";
            $price = format_price($row["Price"]);
            echo "<td>£$price </td>";
            echo "<td>";
            echo "<form action='PHP_Handling/cart_updater.php' method='post'>";
            echo "<input type=\"hidden\" name=\"added_item_type\" value=\"food\">";
            echo "<button type='submit' name='added_item_to_cart' value='" . $row["Food_ID"] . "'>Add</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo '</div> ';
    }
} else {
    echo "No snacks available.";
}
$drink_items = "SELECT * FROM drink_items";
$drink_menu = $connection->query($drink_items);
if ($drink_menu->num_rows > 0) {
    $tables = []; // Array to store separate tables
    while ($row = $drink_menu->fetch_assoc()) {
        $type = $row["Type"];
        if (!isset($tables[$type])) {
            $tables[$type] = [];
        }
        $tables[$type][] = $row;
    }

    // Display tables
    foreach ($tables as $type => $table) {
        echo '<div class="white_plain_container" style = "margin:0;padding:0;margin-top:5%;" > ';
        echo "<h2>$type</h2>";
        echo "<table class = \"menu-table\">";
        echo "<tr><th></th><th></th><th></th></tr>";

        foreach ($table as $row) {
            echo "<tr>";
            echo "<td>" . $row["Drink_Name"] . "</td>";
            $price = format_price($row["Price"]);
            echo "<td>£$price </td>";
            echo "<td>";
            echo "<form id='myForm' action='PHP_Handling/cart_updater.php' method='post'>";
            echo "<input type=\"hidden\" id=\"added_item_type\" name=\"added_item_type\" value=\"drink\">";
            echo "<button type='submit' name='added_item_to_cart' value='" . $row["Drink_ID"] . "'>Add</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo '</div> ';

    }

} else {
    echo "No snacks available.";
}
echo '</div>';

?>

<!-- The popup container
/* <div id="popup" class="popup">
    <span class="close" onclick="hidePopup()">&times;</span>
    <p>Added to Basket!</p>
</div>

<hr>

 ?php
    // Database configuration

    $user = "username";
    $pass = "password";
    $db = "cinema_snacks_app";

    $db = new mysqli('localhost', $user, $pass, $db);

    // Check the connection
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    } else {
        echo "<p>Database connection successful.</p>";
    }

    // You can now perform database operations using $conn.

    // Don't forget to close the database connection when you're done.
    $db->close();
    ?> -->

<!-- <script>
    function submitForm(event) {
        // Prevent default form submission
        event.preventDefault();

        // Get form data
        let itemType = document.getElementById('added_item_type').value;

        // Create a FormData object
        let formData = new FormData();
        formData.append('added_item_type', itemType);

        // Send a fetch request
        fetch('../PHP_Handling/cart_updater.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                showPopup();
            })
            .catch((error) => {
                console.error('Error:', error);
            });
    }
</script>
<script src="../JavaScript/popup.js"></script>-->
</body>
</html>


