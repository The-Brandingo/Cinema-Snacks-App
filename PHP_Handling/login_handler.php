<?php session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
foreach (glob("../PHP_Handling/functions/*.php") as $filename) {
    include_once $filename;
}

$database = Database::getInstance();
$connection = $database->getConnection();

if (isset($_POST['try_login'])) { // this looks for the form submitting
    $email = $_POST['email']; //because our login button and input fields are wrapped in a form whatever was in email or password is submitted with the form when 'login' is pressed
    $password = $_POST['password']; //I set variables here of what the form submitted.

    $find_account = login_verification($email);
    if ($find_account) {
        if (verify_password($password, get_hashed_password($email))) {// Login successful, handle accordingly
            set_session($find_account);
            cartAccountTransfer($_SESSION['user_id'], $_COOKIE['browser_id']);
            header("Location:../Pages/cart.php");
        } else {
            login_error($email);
        }
    } else { // this is what to do if the user puts in the wrong log info
        login_error($email);
    }

    exit();
}
if (isset($_POST['try_logout'])) {
    session_unset(); // unsets the session variables that confirm what user you are
    header("Location:../Pages/login.php");//reloads the page finishing the log out
}

?>