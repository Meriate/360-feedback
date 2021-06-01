<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$Companyname = $cpassword = $confirm_cpassword = $aantalusers  = "";
$Companyname_err = $cpassword_err = $confirm_cpassword_err = $aantalusers_err = "";

$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    ///// Create Company account ///////
    // Validate Companyname
    if (empty(trim($_POST["Companyname"]))) {
        $Companyname_err = "Please enter a Company name.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM bedrijven where Companyname = ?";

        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_Companyname);

            // Set parameters
            $param_Companyname = trim($_POST["Companyname"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $Companyname_err = "This Companyname is already taken.";
                } else {
                    $Companyname = trim($_POST["Companyname"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["cpassword"]))) {
        $cpassword_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["cpassword"])) < 6) {
        $cpassword_err = "Password must have atleast 6 characters.";
    } else {
        $cpassword = trim($_POST["cpassword"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_cpassword"]))) {
        $confirm_cpassword_err = "Please confirm password.";
    } else {
        $confirm_cpassword = trim($_POST["confirm_cpassword"]);
        if (empty($cpassword_err) && ($cpassword != $confirm_cpassword)) {
            $confirm_cpassword_err = "Password did not match.";
        }
    }

    if (empty(trim($_POST["aantalusers"]))) {
        $aantalusers_err = "Please enter a max number of users.";
    } else {
        $aantalusers = trim($_POST["aantalusers"]);
    }




    ///// Create Manager account ///////
        // Validate email
    if (empty(trim($_POST["email"]))) {
        $username_err = "Please enter a E-mail Adres.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";

        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["email"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This E-mail Adres is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    ///// Check input errors before inserting in database /////////
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($Companyname_err) && empty($cpassword_err) && empty($confirm_cpassword_err) && empty($aantalusers_err)) {

        // Prepare an insert statement
        $sql_company = "INSERT INTO bedrijven (Companyname, max_aantal_users) VALUES (?,?); ";
        $sql_companylogin = "INSERT INTO users (username, password, licensie) VALUES (?, ?,99);";
        $sql_manager = "INSERT INTO users (username, password, email, licensie) VALUES (?,?,?,2);";


        if (($stmt_company = mysqli_prepare($con, $sql_company)) && ($stmt_manager = mysqli_prepare($con, $sql_manager)) && ($stmt_companylogin = mysqli_prepare($con, $sql_companylogin))) {
            // Bind variables to the prepared statement as parameters

            mysqli_stmt_bind_param($stmt_manager, "sss", $param_username, $param_password,$param_email);
            mysqli_stmt_bind_param($stmt_company, "si", $param_Companyname, $param_aantalusers);
            mysqli_stmt_bind_param($stmt_companylogin, "ss", $param_Companyname, $param_cpassword);

            // Set parameters
            $param_Companyname = $Companyname;
            $param_cpassword = password_hash($cpassword, PASSWORD_DEFAULT); // Creates a password hash
            $param_aantalusers = $aantalusers;

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_email = $email;
            //echo "INSERT INTO bedrijven (Companyname, password, max_aantal_users) VALUES ('".$param_Companyname."','".$param_cpassword."',".$param_aantalusers.");";
            // Attempt to execute the prepared statement
            if ((mysqli_stmt_execute($stmt_manager)) && (mysqli_stmt_execute($stmt_company)) && (mysqli_stmt_execute($stmt_companylogin))) {
                // Close statement
                mysqli_stmt_close($stmt_company);
                mysqli_stmt_close($stmt_manager);
                mysqli_stmt_close($stmt_companylogin);


                $queryResult = mysqli_query($con, "SELECT * FROM bedrijven where Companyname = '" . $param_Companyname . "'");
                $row = mysqli_fetch_assoc($queryResult);
                mysqli_query($con, "UPDATE users SET bedrijfs_id = " . $row['id'] . " where username = '" . $param_Companyname . "' or username = '" . $param_username . "';");

                // Redirect to login page
                header("location: logout.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
    }

    // Close connection
    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Sign Up</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <style type="text/css">
            body{ font: 14px sans-serif; }
            .wrapper{ width: 350px; padding: 20px; }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <h2>Sign Up</h2>
            <p>Please fill this form to create an account.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h3> Company Account </h3>
                <div class="form-group <?php echo (!empty($Companyname_err)) ? 'has-error' : ''; ?>">
                    <label>Company Name</label>
                    <input type="text" name="Companyname" class="form-control" value="<?php echo $Companyname; ?>">
                    <span class="help-block"><?php echo $Companyname_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($cpassword_err)) ? 'has-error' : ''; ?>">
                    <label>Company Password</label>
                    <input type="password" name="cpassword" class="form-control" value="<?php echo $cpassword; ?>">
                    <span class="help-block"><?php echo $cpassword_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_cpassword_err)) ? 'has-error' : ''; ?>">
                    <label>Confirm Company Password</label>
                    <input type="password" name="confirm_cpassword" class="form-control" value="<?php echo $confirm_cpassword; ?>">
                    <span class="help-block"><?php echo $confirm_cpassword_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_aantalusers_err)) ? 'has-error' : ''; ?>">
                    <label>Max Aantal Users</label>
                    <input type="aantalusers" name="aantalusers" class="form-control" onkeypress='validate(event)' value=" <?php echo $aantalusers; ?>">
                    <span class="help-block"><?php echo $aantalusers_err; ?></span>
                </div>

                <h3> Manager Account </h3>
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label>E-mail Addres</label>
                    <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                    <span class="help-block"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                    <span class="help-block"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group">

                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-default" value="Reset">
                </div>
                <a  href="login.php" class="btn btn-primary">Back</a>
            </form>

        </div>    
    </body>
    <script>
        function validate(evt) {
            var theEvent = evt || window.event;

            // Handle paste
            if (theEvent.type === 'paste') {
                key = event.clipboardData.getData('text/plain');
            } else {
                // Handle key press
                var key = theEvent.keyCode || theEvent.which;
                key = String.fromCharCode(key);
            }
            var regex = /[0-9]|\./;
            if (!regex.test(key)) {
                theEvent.returnValue = false;
                if (theEvent.preventDefault)
                    theEvent.preventDefault();
            }
        }
    </script>
</html>


