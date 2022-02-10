<?php

function OpenConn()
{
    $servername = "localhost";
    $username = "root";
    $password = "Q1w2e3r4!@";
    $db = "fishgame";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $db);
    mysqli_query($conn, "set names utf8");

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    echo "Connected successfully";

    return $conn;
}

?>