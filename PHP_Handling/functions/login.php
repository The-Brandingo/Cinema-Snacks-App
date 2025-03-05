<?php

function login_verification($email)
{
    $account_statement_query = "SELECT * FROM customer
    WHERE Email = ? ;"; // query here must leave email as ? and handled later
    $database = Database::getInstance();
    $connection = $database->getConnection();
    $account_statement = $connection->prepare($account_statement_query); // these 3 lines are essential for handling querying sensitive info
    $account_statement->bind_param('s', $email); // the email / pass you want to query must be sent here
    $account_statement->execute(); //essential
    $find_account_result = $account_statement->get_result(); //this is to set a variable for the result of attempting to log in, if the result comes back as 0 then no matches have ben found
    $account_statement->close();

    return $find_account_result;

}

function get_hashed_password($email)
{
    $database = Database::getInstance();
    $connection = $database->getConnection();
    $account_statement_query = "SELECT Password FROM customer WHERE Email = ?;";
    $account_statement = $connection->prepare($account_statement_query);
    $account_statement->bind_param('s', $email);
    $account_statement->execute();
    $account_result = $account_statement->get_result();
    $account_statement->close();

    if ($account_result->num_rows > 0) {
        $user = $account_result->fetch_assoc();
        return $user['Password'];
    }

    return false;
}

function get_email_from_id($customer_id)
{
    $database = Database::getInstance();
    $connection = $database->getConnection();
    $query = "SELECT
        customer.Email
        FROM customer
        WHERE Customer_ID = $customer_id";
    $email = $connection->query($query);

    return $email->fetch_assoc();
}


function verify_password($password, $hashedPasswordFromDatabase)
{
    return password_verify($password, $hashedPasswordFromDatabase);
}

function set_session($account_result) // function to set the actual web browser session to the user so they are logged in (kind of the real login function)
{
    $user = $account_result->fetch_assoc();
    $_SESSION['user_id'] = $user['Customer_ID']; // stores information we need to know the user is logged in, $_SESSION is a special variable that exists when we did start_session(); at the top.
    $_SESSION['user_name'] = $user['First_Name'] . ' ' . $user['Surname']; // storing the customer_id and name lets me customize what they can see when theyre logged in
}

?>