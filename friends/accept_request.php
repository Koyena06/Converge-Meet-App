<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id    = $_SESSION['user_id'];
$request_id = isset($_GET['request_id']) ? (int) $_GET['request_id'] : 0;

if ($request_id <= 0) {
    die("Invalid request ID.");
}

// Only the receiver (logged-in user) is allowed to accept
$update_query = "
    UPDATE friends
    SET status = 'accepted'
    WHERE id = ? AND receiver_id = ? AND status = 'pending'
";
$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, "ii", $request_id, $user_id);

if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
    mysqli_close($conn);
    header("Location: friends.php?msg=request_accepted");
    exit();
} else {
    mysqli_close($conn);
    die("Could not accept request. It may not exist or you are not the receiver.");
}
?>


?php
