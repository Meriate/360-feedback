<?php
session_start();


require_once 'config/config.php';

$beoordelaar_id = $_SESSION['id'];
$bedrijfs_id = $_SESSION['bedrijfs_id'];
$survey_id = $_POST['survey_id'];

foreach ($_POST as $key => $value) {
    if ($key == 'survey_id') {
        continue;
    }
    $array_value = explode("_", $key);
    $beoordeelde_id = $array_value[2];
    $Attribute_id = $array_value[1];


    $sql = "INSERT INTO vragenlijst ("
            . " Beoordelaar_id,"
            . " Beoordeelde_id,"
            . " survey_id,"
            . " Attribute_id,"
            . " Antwoord,"
            . " bedrijfs_id)"
            . " VALUES ("
            . $beoordelaar_id . ","
            . $beoordeelde_id . ","
            . $survey_id . ","
            . $Attribute_id . ","
            . $value . ","
            . $bedrijfs_id . ")";
    mysqli_query($con, $sql);
}
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <meta charset="UTF-8">
    <h2> Bedankt voor het invullen van de vragen</h2>
    <title></title>
</head>
<body>
    <form action="selectsurvey.php">
        <input type="submit" value="Go to surveys" />
    </form>

</body>
</html>
