<?php
$servername = "127.0.0.1";
$username = "bryan";
$password = "3g52Y2Cmp(R/Ne5b";
$dbname = "listo";

$connection = new mysqli($servername, $username, $password, $dbname);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
