<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch accepted friends
$friends_query = "
    SELECT u.id, u.name, u.email
    FROM friends f
    JOIN users u ON (
        CASE 
            WHEN f.sender_id = ? THEN f.receiver_id = u.id
            ELSE f.sender_id = u.id
        END
    )
    WHERE (f.sender_id = ? OR f.receiver_id = ?)
      AND f.status = 'accepted'
";
$stmt = mysqli_prepare($conn, $friends_query);
mysqli_stmt_bind_param($stmt, "iii", $user_id, $user_id, $user_id);
mysqli_stmt_execute($stmt);
$friends_result = mysqli_stmt_get_result($stmt);
$friends = mysqli_fetch_all($friends_result, MYSQLI_ASSOC);

// Fetch pending incoming requests (others sent to me)
$pending_query = "
    SELECT u.id, u.name, u.email, f.id AS request_id
    FROM friends f
    JOIN users u ON f.sender_id = u.id
    WHERE f.receiver_id = ? AND f.status = 'pending'
";
$stmt2 = mysqli_prepare($conn, $pending_query);
mysqli_stmt_bind_param($stmt2, "i", $user_id);
mysqli_stmt_execute($stmt2);
$pending_result = mysqli_stmt_get_result($stmt2);
$pending_requests = mysqli_fetch_all($pending_result, MYSQLI_ASSOC);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Friends</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>My Friends (<?= count($friends) ?>)</h2>

    <?php if (empty($friends)): ?>
        <p>You have no friends yet. Start sending requests!</p>
    <?php else: ?>
        <ul>
            <?php foreach ($friends as $friend): ?>
                <li>
                    <strong><?= htmlspecialchars($friend['name']) ?></strong>
                    (<?= htmlspecialchars($friend['email']) ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr>
    <h2>Pending Friend Requests (<?= count($pending_requests) ?>)</h2>

    <?php if (empty($pending_requests)): ?>
        <p>No pending requests.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($pending_requests as $req): ?>
                <li>
                    <strong><?= htmlspecialchars($req['name']) ?></strong>
                    (<?= htmlspecialchars($req['email']) ?>)
                    &nbsp;
                    <a href="accept_request.php?request_id=<?= $req['request_id'] ?>">
                        <button>Accept</button>
                    </a>
                    <a href="decline_request.php?request_id=<?= $req['request_id'] ?>">
                        <button>Decline</button>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <br>
    <a href="../user/dashboard.php">← Back to Dashboard</a>
</body>
</html>
