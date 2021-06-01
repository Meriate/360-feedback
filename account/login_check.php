<?php
// Include config file
require_once "../config/config.php";
// Initialize the session


// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // check licensie

    if ($_SESSION["licensie"] == 99) {
        $_SESSION = array();
        session_destroy();
    } elseif ($_SESSION["licensie"] == 1) {
        header('location:../selectsurvey.php');
    } elseif ($_SESSION["licensie"] == 2) {
        header('location:../manage.php');
    } else {
        header('location:logout.php');
    }
}




// Define variables and initialize with empty values
$userkey = $password = "";
$userkey_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["inputUserkey"]))) {
        $userkey_err = "Please enter email or username.";
    } else {
        $userkey = trim($_POST["inputUserkey"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["inputPassword"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["inputPassword"]);
    }

    // Validate credentials
    if (empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password,email, licensie,bedrijfs_id FROM users WHERE email = ? OR username = ?";

        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_email,$param_username);

            // Set parameters
            $param_email = $userkey;
            $param_username = $userkey;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password,$email, $licensie,$bedrijfs_id);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;
                            $_SESSION["username"] = $username;
                            $_SESSION["licensie"] = $licensie;
                            $_SESSION["bedrijfs_id"] = $bedrijfs_id;

                            // Redirect user to welcome page
                            if ($_SESSION["licensie"] == 1) {
                                header('location:../selectsurvey.php');
                            } elseif ($_SESSION["licensie"] == 2) {
                                header('location:../manage.php');
                            } elseif ($_SESSION["licensie"] == 99) {
                                header('location:checkregister.php');
                            } else {
                                header('location:logout.php');
                            }
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $email_err = "No account found with this email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($con);
}
?>
