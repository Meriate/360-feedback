<?php
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === false) {
    header('header:login.php');
} elseif ($_SESSION["licensie"] != 99) {
    header('header:permissiondenied.php');
}
// Include config file
require_once "../config/config.php";
// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = $firstname = $lastname = "";
$username_err = $password_err = $confirm_password_err = $email_err = $firstname_err = $lastname_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["inputFirstName"]))) {
        $firstname_err = "Please enter your First Name";
    } else {
        $firstname = trim($_POST["inputFirstName"]);
    }

    if (empty(trim($_POST["inputLastName"]))) {
        $lastname_err = "Please enter your Last Name";
    } else {
        $lastname = trim($_POST["inputLastName"]);
    }

    // Validate email
    if (empty(trim($_POST["inputEmailAddress"]))) {
        $email_err = "Please enter a E-mail Adres.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";

        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Set parameters
            $param_email = trim($_POST["inputEmailAddress"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "This E-mail Adres is already taken.";
                } else {
                    $email = trim($_POST["inputEmailAddress"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate username
    if (empty(trim($_POST["inputUsername"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["inputUsername"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["inputUsername"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["inputPassword"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["inputPassword"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["inputPassword"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["inputConfirmPassword"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["inputConfirmPassword"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {


        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password,bedrijfs_id,email,firstname, lastname,licensie,Aantal_keer_beoordeelt) VALUES (?,?,?,?,?,?,1,0)";

        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssisss", $param_username, $param_password, $param_bedrijfs_id, $param_email, $param_firstname, $param_lastname);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_bedrijfs_id = $_POST['bedrijfs_id'];
            $param_firstname = $firstname;
            $param_lastname = $lastname;

            $param_email = $email;
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {

                // Redirect to login page
                header("location: login.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($con);
} else {
    $bedrijfs_id = $_SESSION['bedrijfs_id'];
}
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Page Title - SB Admin</title>
        <?php include "../assets/lib/libraries_head.php";?>
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-light">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-7">
                                <div class="card shadow-md border-0 rounded-lg mt-5">
                                    <div class="card-body">
                                      <h3 class="text-center my-4">Create Account</h3>
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input class="form-control py-4" id="inputFirstName" name="inputFirstName" type="text" placeholder="Enter first name" />
                                                        <span class="help-block"><?php echo $firstname_err; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input class="form-control py-4" id="inputLastName" name="inputLastName" type="text" placeholder="Enter last name" />
                                                        <span class="help-block"><?php echo $lastname_err; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <input class="form-control py-4" id="inputUsername" name="inputUsername" type="text" placeholder="Enter username" />
                                                <span class="help-block"><?php echo $username_err; ?></span>
                                            </div>
                                            <div class="form-group">
                                                <input class="form-control py-4" id="inputEmailAddress" name="inputEmailAddress" type="email" aria-describedby="emailHelp" placeholder="Enter email address" />
                                                <span class="help-block"><?php echo $email_err; ?></span>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input class="form-control py-4" id="inputPassword" name="inputPassword" type="password" placeholder="Enter password" />
                                                        <span class="help-block"><?php echo $password_err; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <input class="form-control py-4" id="inputConfirmPassword" name="inputConfirmPassword" type="password" placeholder="Confirm password" />
                                                        <span class="help-block"><?php echo $confirm_password_err; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="bedrijfs_id" class="btn btn-primary" value="<?php echo $bedrijfs_id ?>">
                                            <div class="form-group mt-4 mb-0"><input type="submit" class="btn btn-lg btn-primary btn-block" value="Create Account"></div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center">
                                        <div class="small"><a href="login.php">Have an account? Go to login</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Meriate</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
