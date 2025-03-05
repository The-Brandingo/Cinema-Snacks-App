<?php
function keep_attempts($firstName, $surname, $dateOfBirth, $email, $phoneNumber)
{
    $_SESSION['first_name_attempt'] = $firstName;
    $_SESSION['surname_attempt'] = $surname;
    $_SESSION['dob_attempt'] = $dateOfBirth;
    $_SESSION['email_attempt'] = $email;
    $_SESSION['phone_attempt'] = $phoneNumber;
}

function unset_attempts()
{
    unset($_SESSION['first_name_attempt']);
    unset($_SESSION['surname_attempt']);
    unset($_SESSION['dob_attempt']);
    unset($_SESSION['email_attempt']);
    unset($_SESSION['phone_attempt']);

}

?>