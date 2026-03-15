<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$sender_id   = $_SESSION['user_id'];
$receiver_id = isset($_GET['to']) ? (int) $_GET['to'] : 0;

// Basic validation
if ($receiver_id <= 0 || $receiver_id === $sender_id) {
    die("Invalid request.");
}

// Check if a request already exists in either direction
$check_query = "
    SELECT id FROM friends
    WHERE (sender_id = ? AND receiver_id = ?)
       OR (sender_id = ? AND receiver_id = ?)
";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_close($conn);
    die("Friend request already sent or you are already friends.");
}
mysqli_stmt_close($stmt);

// Insert the new pending request
$insert_query = "
    INSERT INTO friends (sender_id, receiver_id, status, created_at)
    VALUES (?, ?, 'pending', NOW())
";
$stmt2 = mysqli_prepare($conn, $insert_query);
mysqli_stmt_bind_param($stmt2, "ii", $sender_id, $receiver_id);

if (mysqli_stmt_execute($stmt2)) {
    mysqli_close($conn);
    header("Location: friends.php?msg=request_sent");
    exit();
} else {
    mysqli_close($conn);
    die("Error sending request: " . mysqli_error($conn));
}
?>
