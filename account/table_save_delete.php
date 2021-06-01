<?php
session_start();

$tablename = 'Products';

require_once "../config/config_app.php";
require_once "../products_files/table_parameters.php";

$userid = $_SESSION['id'];
$ean = $_POST['EAN'];

$sql = "DELETE FROM ".$tablename." WHERE ean = '".$ean."' AND author_id = '".$userid."'";

mysqli_query($link_app,$sql);



 ?>
