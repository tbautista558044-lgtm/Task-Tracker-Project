<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // MySQL username
define('DB_PASS', '');           // MySQL password
define('DB_NAME', 'task_tracker');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
