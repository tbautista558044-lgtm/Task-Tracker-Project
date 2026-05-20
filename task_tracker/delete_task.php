<?php

session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// DELETE: Remove the task — AND verify it belongs to the current user (security!)
$stmt = mysqli_prepare($conn,
    "DELETE FROM tasks WHERE id = ? AND user_id = ?"
);
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);

// Return to dashboard whether it worked or not
header("Location: dashboard.php");
exit;
?>
