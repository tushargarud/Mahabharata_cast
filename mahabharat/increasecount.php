<?php

require('connection.php');

function clean_input($inputStr)
{
    $inputStr = trim($inputStr);
    $inputStr = stripslashes($inputStr);
    $inputStr = htmlspecialchars($inputStr);
    return $inputStr;
}

if (!empty($_POST["character"]) && !empty($_POST["actor"])) {

    $character = clean_input($_POST["character"]);
    $actor = clean_input($_POST["actor"]);

    $stmt = $conn->prepare("UPDATE votes SET vote_count=vote_count+1 WHERE char_id=? and a_id=?");
    $stmt->bind_param("ss", $character, $actor);

    if ($stmt->execute())
        echo 'count increased successfully';
    else
        echo 'error: ' . $conn->error;

    $stmt->close();
}

$conn->close();

?>