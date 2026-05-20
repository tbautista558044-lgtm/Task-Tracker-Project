<?php

$SERVER_NAME = getenv('SERVER_NAME');
$USERNAME = getenv('USERNAME');
$PASSWORD = getenv('PASSWORD');
$DB_NAME = getenv('DB_NAME');

$conn = mysqli_connect(SERVER_NAME, USERNAME, PASSWORD, DB_NAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
