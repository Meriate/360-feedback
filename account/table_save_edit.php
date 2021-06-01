<?php
session_start();



require_once "../config/config_app.php";

$Databasecolumns = array('Productnaam' => 'title',
                          'EAN' => 'ean',
                          'Voorraad' => 'stock',
                          'Prijs' => '_wcj_purchase_price',
                          'Categorie' => 'productcategorieen');


$userid = $_SESSION['id'];
$ean = $_POST['EAN'];

$sql = "UPDATE Products SET ";

foreach($_POST as $column=>$value){
  if (array_key_exists($column, $Databasecolumns)) {
    $sql .= $Databasecolumns[$column].' = "'.$value.'", ';
  }
}
$sql = substr($sql, 0, -2);
$sql .= " WHERE ean = '".$ean."'";
if($userid != 0){
  $sql .= "  AND author_id = '".$userid."'";
}
$_SESSION['sql'] = $sql;
mysqli_query($link_app,$sql);
 ?>
