<?php

require('connection.php');

function clean_input($inputStr)
{
    $inputStr = trim($inputStr);
    $inputStr = stripslashes($inputStr);
    $inputStr = htmlspecialchars($inputStr);
    return $inputStr;
}

if (!empty($_POST["name"]) && !empty($_POST["image"]) && !empty($_POST["character"]) ) {

    $name = clean_input($_POST["name"]);
    $image = $_POST["image"];
    $character = clean_input($_POST["character"]);

    $stmt = $conn->prepare("SELECT INCREASE_NEW_ACTOR_COUNT(?,?,?)");
    $stmt->bind_param("sss", $name, $image,$character);

    if ($stmt->execute())
        echo 'Count incremented successfully';
    else
        echo 'error: ' . $conn->error;

    $stmt->close();
}

$conn->close();

?>