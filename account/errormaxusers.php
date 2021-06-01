<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === false) {
    header('header:login.php');
}elseif ( $_SESSION["licensie"] != 99) {
    header('header:permissiondenied.php');
    
}
?>

<html lang="en">
<head>
<title>To many users</title>
</head>
<body>
<h1>ERROR: To many users</h1>
<p><?php echo $_SESSION['username'] ?> heeft het maximaal aantal users. u kunt zich helaas niet registreren</p>
</body>
</html>
