<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank you for your order</title>
    <link rel="stylesheet" href="../CSS/main-styles.css">
    <link rel="stylesheet" href="../CSS/nav-bar.css">
    <link rel="stylesheet" href="../CSS/overwrite.css">
    <script src="../JavaScript/popout-toggler.js" defer></script>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
<?php include('../PHP_Handling/create-nav-bar.php');

foreach (glob("../PHP_Handling/functions/*.php") as $filename) {
    include_once $filename;
} ?>

<div id="centered-container">
    <div class="white_plain_container" id="thank-you">
        <p style="font-size: 35px">Thank you for your order!</p>
        <p style="font-size: 25px">We hope you enjoy your film, thank you for choosing Everyman Cinemas</p>
        <!-- Add your content here -->
    </div>
</div>

</body>
</html>


