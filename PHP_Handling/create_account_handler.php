<?php session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
foreach (glob("functions/*.php") as $filename) {
    include_once $filename;
}

$database = Database::getInstance();
$connection = $database->getConnection();

if (isset($_POST['create_account'])) { // this looks for the form submitting
    $firstName = $_POST['first_name'];
    $surname = $_POST['surname'];
    $dateOfBirth = $_POST['dob'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone'];
    $password = $_POST['password'];
    $reenterPassword = $_POST['re_password'];

    $check_email_not_taken = "SELECT * FROM customer WHERE Email=?;";

    $check_email_statement = $connection->prepare($check_email_not_taken);
    $check_email_statement->bind_param('s', $email);
    $check_email_statement->execute();
    $email_taken_RESULT = $check_email_statement->get_result();
    $check_email_statement->close();

    if ($password !== $reenterPassword) {
        keep_attempts($firstName, $surname, $dateOfBirth, $email, $phoneNumber);
        password_mismatch();
        exit();
    }

    if ($email_taken_RESULT->num_rows > 0) { // Email is already taken, handle accordingly
        keep_attempts($firstName, $surname, $dateOfBirth, $email, $phoneNumber);
        signup_error("Email already exists... ");

    } else { //handle successful account creation
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $new_account_query = "INSERT INTO customer (First_Name, Surname, Date_of_Birth, Email, Phone_Number, Password) 
                              VALUES (?, ?, ?, ?, ?, ?)";
        $new_account_statement = $connection->prepare($new_account_query);
        $new_account_statement->bind_param('ssssss', $firstName, $surname, $dateOfBirth, $email, $phoneNumber, $hashed_password);
        unset_attempts();
        if ($new_account_statement->execute()) {  // Account creation successful, handle accordingly
            $_SESSION['account_creation_success'] = "Account created successfully";
            set_session(login_verification($email));
            unset_attempts();
            header("Location:../Pages/menu.php");
        } else { // Account creation failed, handle accordingly
            keep_attempts($firstName, $surname, $dateOfBirth, $email, $phoneNumber);
            signup_error("Error occurred please try again");
        }
        exit();

    }
}

?>