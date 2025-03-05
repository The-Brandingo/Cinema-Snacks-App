<?php session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
foreach (glob("functions/*.php") as $filename) {
    include_once $filename;
}

$database = Database::getInstance();
$connection = $database->getConnection();
if (isset($_POST['edit_email'])) {

    $email = $_POST['new_email'];
    $password = $_POST['password'];

    $customer = $_SESSION['user_id'];

    $customer_email = get_email_from_id($customer);
    $customer_email = $customer_email['Email'];

    $hashed_password = get_hashed_password($customer_email);
    if (verify_password($password, $hashed_password)) {

        $check_email_not_taken = "SELECT * FROM customer WHERE Email=?;";
        $check_email_statement = $connection->prepare($check_email_not_taken);
        $check_email_statement->bind_param('s', $email);
        $check_email_statement->execute();
        $email_taken_RESULT = $check_email_statement->get_result();
        $check_email_statement->close();

        if ($email_taken_RESULT->num_rows > 0) { // Email is already taken, handle accordingly
            edit_email_error();
        } elseif (!empty($email)) {

            $sql = "UPDATE customer SET Email = ? WHERE Customer_ID = ?";
            $new_email_statement = $connection->prepare($sql);
            $new_email_statement->bind_param('si', $email, $customer);
            $new_email_statement->execute();
            $new_email_statement->close();
            header("Location:../Pages/account_overview.php");

        }
    } else {
        password_mismatch();
    }


} elseif (isset($_POST['edit_password'])) {
    echo "hello";
    $password = $_POST['password'];
    $newPassword = $_POST['new_password'];
    $rePassword = $_POST['new_re_password'];
    $customer = $_SESSION['user_id'];

    $customer_email = get_email_from_id($customer);
    $customer_email = $customer_email['Email'];

    $hashed_password = get_hashed_password($customer_email);

    if ($newPassword !== $rePassword) {
        new_password_mismatch();
        exit();
    }

    if (verify_password($password, $hashed_password)) {
        $hashed_new_pass = password_hash($newPassword, PASSWORD_DEFAULT);
        $new_password_query = "UPDATE customer SET Password = ? WHERE Customer_ID = ?";
        $new_password_statement = $connection->prepare($new_password_query);
        $new_password_statement->bind_param('si', $hashed_new_pass, $customer);
        $new_password_statement->execute();
        $new_password_statement->close();
        header("Location:../Pages/account_overview.php");
    } else {
        new_password_mismatch();
    }

} elseif (isset($_POST['edit_account_details'])) { // this looks for the form submitting
    $dateOfBirth = $_POST['new_dob'];

    $phoneNumber = $_POST['new_phone_number'];
    $firstName = $_POST['new_first_name'];
    $surname = $_POST['new_surname'];

    if (!empty($firstName) or !empty($surname) or !empty($dateOfBirth) or !empty($phoneNumber)) {


        $customer = $_SESSION['user_id'];
// Start building the SQL query
        $sql = "UPDATE customer SET ";


        if (!empty($dateOfBirth)) {
            $sql .= "Date_of_Birth = '$dateOfBirth', ";
        }
        if (!empty($phoneNumber)) {
            $sql .= "Phone_Number = $phoneNumber, ";
        }
        $updateSessionSurname = false;
        if (!empty($surname)) {
            $sql .= "Surname = '$surname', ";
            $updateSessionSurname = true;
        }
        $updateSessionFirstName = false;
        if (!empty($firstName)) {
            $sql .= "First_Name = '$firstName', ";
            $updateSessionFirstName = true;
        }

// Remove the trailing comma and space if any columns were added
        $sql = rtrim($sql, ', ');

// Add the WHERE clause to identify the row you want to update
        $sql .= " WHERE Customer_ID = $customer";

        if ($connection->query($sql) === TRUE) {
            if ($updateSessionFirstName or $updateSessionSurname) {
                $name = $_SESSION['user_name'];
                $name_parts = explode(' ', $name, 2);
                $name0 = $name_parts[0];
                $name1 = isset($name_parts[1]) ? $name_parts[1] : '';

                if ($updateSessionSurname and !$updateSessionFirstName) {
                    $_SESSION['user_name'] = $name0 . ' ' . $surname; // storing the customer_id and name lets me customize what they can see when theyre logged in
                } elseif ($updateSessionFirstName and !$updateSessionSurname) {
                    $_SESSION['user_name'] = $firstName . ' ' . $name1; // storing the customer_id and name lets me customize what they can see when theyre logged in
                } elseif ($updateSessionFirstName and $updateSessionSurname) {
                    $_SESSION['user_name'] = $firstName . ' ' . $surname; // storing the customer_id and name lets me customize what they can see when theyre logged in
                }
            }

        } else {
            echo "Error updating record: " . $connection->error;
        }
        $connection->close();
    }
    // Execute your query
    header("Location:../Pages/account_overview.php");


}
?>