<?php

session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// UPDATE: Set task status back to 'pending'
$stmt = mysqli_prepare($conn,
    "UPDATE tasks SET status = 'pending' WHERE id = ? AND user_id = ?"
);
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);

header("Location: dashboard.php");
exit;
?>
