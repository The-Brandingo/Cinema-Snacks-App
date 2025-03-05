<?php
function login_error($email)
{
    $_SESSION['login_error'] = "Invalid email or password"; // setting the error reason/message here
    $_SESSION['email_attempt'] = $email; // sets the email to their last attempt so it doesn't clear the input box
    header("Location:../Pages/login.php"); //redirects the user to the login page again after they fail to log in
}

function signup_error($message)
{
    $_SESSION['account_creation_error'] = $message;
    header("Location:../Pages/create_account.php");
}

function password_mismatch()
{
    $_SESSION['password_no_match'] = "Passwords do not match";
    header("Location:../Pages/create_account.php");
}

function new_password_mismatch()
{
    $_SESSION['new_password_no_match'] = "Passwords do not match";
    header("Location:../Pages/account_overview.php");

}

function account_edit_error($message)
{
    $_SESSION['account_detail_error'] = $message;

}

function edit_email_error()
{
    $_SESSION['account_edit_error'] = "Email already taken";
    header("Location:../Pages/account_overview.php");
}

?>