<?php

$host = "zephyr.proxy.rlwy.net";
$username = "root";
$password = "smjieEUqhrlmzNVzUcVlvheACZioTrdr";
$database = "railway";
$port = 31539;

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>

