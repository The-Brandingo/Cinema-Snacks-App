<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/overwrite.css">
    <link rel="stylesheet" href="../CSS/nav-bar.css">
    <link rel="stylesheet" href="../CSS/main-styles.css">
    <script src="../JavaScript/popout-toggler.js" defer></script>
    <title>Login Page</title>

</head>
<body>

<?php require('../PHP_Handling/create-nav-bar.php'); // sets the nav bar

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== "") { // check if the user is logged in and if so redirect them away from the login page
    header("Location:../Pages/cart.php");
}
?>

<div class="login-container">
    <h2>Login</h2>
    <?php if (isset($_SESSION['login_error'])) {
        echo '<p style="color: red;">' . $_SESSION['login_error'] . '</p>';//
        unset($_SESSION['login_error']);
    } ?>
    <form action="../PHP_Handling/login_handler.php" method="post"> <!-- -->
        <div class="form-group">
            <label for="email">Email:</label> <!-- the below line just stops the email field being cleared upon error-->
            <input type="email" id="email" name="email"
                   value="<?php echo isset($_SESSION['email_attempt']) ? $_SESSION['email_attempt'] : ''; ?>">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="login-button" name="try_login">Login</button>
        <button class="create-account-button" onclick="location.href='create_account.php'">Create
            Account
        </button>
        <!-- MUST BE 'try_login' this identifies the button being pressed to the functions that's been set up-->
    </form>
</div>

</body>
</html>

