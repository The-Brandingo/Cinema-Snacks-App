<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/overwrite.css">
    <link rel="stylesheet" href="../CSS/main-styles.css">
    <link rel="stylesheet" href="../CSS/nav-bar.css">
    <link rel="stylesheet" href="../CSS/create-account.css">
    <script src="../JavaScript/popout-toggler.js" defer></script>
    <title>Create an account</title>

</head>
<body>

<?php require('../PHP_Handling/create-nav-bar.php'); // sets the nav bar

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== "") { // check if the user is logged in and if so redirect them away from the login page
    header("Location:../Pages/cart.php");
}
?>

<div class="create-account-container">
    <h2>Create Account</h2>

    <form action="../PHP_Handling/create_account_handler.php" method="post">
        <div class="form-group-inline">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" maxlength="100" name="first_name"
                       value="<?php echo isset($_SESSION['first_name_attempt']) ? $_SESSION['first_name_attempt'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="surname">Surname:</label>
                <input type="text" id="surname" maxlength="100" name="surname"
                       value="<?php echo isset($_SESSION['surname_attempt']) ? $_SESSION['surname_attempt'] : ''; ?>">
            </div>
        </div>
        <?php if (isset($_SESSION['account_creation_error'])) {
            echo '<p style="color: red;">' . $_SESSION['account_creation_error'] . '<a href="login.php" style="color: red;" >login instead</a></p>';//
            unset($_SESSION['account_creation_error']);
        } ?>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email"
                   value="<?php echo isset($_SESSION['email_attempt']) ? $_SESSION['email_attempt'] : ''; ?>">
        </div>
        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <div class="phone">
                <input type="tel" maxlength="11" id="phone" name="phone"
                       value="<?php echo isset($_SESSION['phone_attempt']) ? $_SESSION['phone_attempt'] : ''; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob"
                   value="<?php echo isset($_SESSION['dob_attempt']) ? $_SESSION['dob_attempt'] : ''; ?>">
        </div>
        <div class="form-group-inline">
            <div class="form-group">
                <?php if (isset($_SESSION['password_no_match'])) {
                    echo '<p style="color: red;">' . $_SESSION['password_no_match'] . '</p>';//
                    unset($_SESSION['password_no_match']);
                } ?>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="re_password">Re-Enter Password:</label>
                <input type="password" id="re_password" name="re_password" required>
            </div>
            <button type="submit" class="create-account-button" id="create-page-button" name="create_account">Create
                Account
            </button>
    </form>

</div>


</body>
</html>

