<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === false) {
    header('header:login.php');
}elseif ( $_SESSION["licensie"] != 99) {
    header('header:permissiondenied.php');

}


require_once '../config/config.php';

$queryResult = mysqli_query($con, "SELECT 1 FROM users where bedrijfs_id = " . $_SESSION['bedrijfs_id']." AND licensie = 1");
$numresults = mysqli_num_rows ($queryResult);

$queryResult = mysqli_query($con,"SELECT max_aantal_users FROM bedrijven where Companyname = '" . $_SESSION['username'] . "'");
$max_users = mysqli_fetch_assoc($queryResult)['max_aantal_users'];

if($numresults < $max_users){
    header('Location:register.php');
}else{
    header('Location:errormaxusers.php');
}

?>
