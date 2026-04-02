<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/db.php';

$userId = $_SESSION['user_id'];

// 1. My confirmed friends (Joining with 'friends' table as per your recent update)
$stmtFriends = $conn->prepare("
    SELECT u.id, u.name as username, u.email
    FROM users u
    JOIN friends f ON
        ((f.sender_id = ? AND f.receiver_id = u.id) OR
         (f.receiver_id = ? AND f.sender_id = u.id))
        AND f.status = 'accepted'
");
$stmtFriends->bind_param("ii", $userId, $userId);
$stmtFriends->execute();
$friends = $stmtFriends->get_result()->fetch_all(MYSQLI_ASSOC);

// 2. Pending incoming requests (Notice we alias 'f.id' as 'request_id')
$stmtPending = $conn->prepare("
    SELECT f.id as request_id, u.id as user_id, u.name as username
    FROM users u
    JOIN friends f ON f.sender_id = u.id
    WHERE f.receiver_id = ? AND f.status = 'pending'
");
$stmtPending->bind_param("i", $userId);
$stmtPending->execute();
$pending = $stmtPending->get_result()->fetch_all(MYSQLI_ASSOC);

// 3. Suggestions (People you aren't friends with yet)
// This query looks for people with shared interests OR just other users in the system
$stmtSuggest = $conn->prepare("
    SELECT u.id, u.name as username 
    FROM users u 
    WHERE u.id != ? 
    AND u.id NOT IN (
        SELECT sender_id FROM friends WHERE receiver_id = ?
        UNION
        SELECT receiver_id FROM friends WHERE sender_id = ?
    )
    ORDER BY (
        SELECT COUNT(*) FROM user_interests ui1 
        JOIN user_interests ui2 ON ui1.interest_id = ui2.interest_id 
        WHERE ui1.user_id = u.id AND ui2.user_id = ?
    ) DESC
    LIMIT 8
");
$stmtSuggest->bind_param("iiii", $userId, $userId, $userId, $userId);
$stmtSuggest->execute();
$suggestions = $stmtSuggest->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Friends | Converge</title>
    <link rel="stylesheet" href="../style1.css">
</head>
<body>

<?php require_once '../includes/header.php'; ?>

<main class="dashboard-page">

    <?php if (!empty($pending)): ?>
    <section style="margin-bottom:48px;">
        <h2 class="page-title" style="font-size:28px;">Pending <em>Requests</em></h2>
        <div class="dashboard-grid"> 
            <?php foreach ($pending as $p): ?>
            <div class="dash-card" style="text-align: center;">
                <div class="profile-placeholder" style="width:60px; height:60px; font-size:1.5rem; margin: 0 auto 15px;">
                    <span class="avatar-letter"><?= strtoupper(substr($p['username'], 0, 1)) ?></span>
                </div>
                <div class="friend-name" style="font-weight: bold; margin-bottom: 15px;"><?= htmlspecialchars($p['username']) ?></div>
                <div style="display:flex; gap:10px; justify-content:center;">
                    <a href="accept_request.php?request_id=<?= $p['request_id'] ?>" class="btn btn-primary" style="padding: 8px 15px; font-size: 0.8rem;">Accept</a>
                    <a href="decline_request.php?request_id=<?= $p['request_id'] ?>" class="btn btn-outline" style="padding: 8px 15px; font-size: 0.8rem;">Decline</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section style="margin-bottom:48px;">
        <h1 class="page-title">My <em>Friends</em></h1>
        <div class="dashboard-grid">
            <?php if (empty($friends)): ?>
                <div class="dash-card" style="text-align: center; padding: 40px; grid-column: 1 / -1;">
                    <p style="color:#666; font-style:italic;">You haven't connected with anyone yet. Check the suggestions below!</p>
                </div>
            <?php else: ?>
                <?php foreach ($friends as $f): ?>
                <div class="dash-card" style="text-align: center;">
                    <div class="profile-placeholder" style="width:60px; height:60px; font-size:1.5rem; margin: 0 auto 15px; background: #d63031;">
                        <span class="avatar-letter"><?= strtoupper(substr($f['username'], 0, 1)) ?></span>
                    </div>
                    <div class="friend-name" style="font-weight: bold;"><?= htmlspecialchars($f['username']) ?></div>
                    <p style="font-size: 0.8rem; color: #888; margin-bottom: 15px;"><?= htmlspecialchars($f['email']) ?></p>
                    <div style="display:flex; flex-direction: column; gap:8px;">
                        <a href="../location/map.php" class="btn btn-gold" style="font-size: 0.8rem;">View on Map</a>
                        <a href="../events/create_event.php?with=<?= $f['id'] ?>" class="btn btn-primary" style="font-size: 0.8rem;">Plan Meetup</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section>
        <h2 class="page-title" style="font-size:28px;">People You May <em>Know</em></h2>
        <p class="page-subtitle" style="margin-bottom:24px;">Connect with others in the community.</p>
        <div class="dashboard-grid">
            <?php if (empty($suggestions)): ?>
                <p style="color:#888; padding-left: 10px;">No new suggestions at the moment.</p>
            <?php else: ?>
                <?php foreach ($suggestions as $s): ?>
                <div class="dash-card" style="text-align: center;">
                    <div class="profile-placeholder" style="width:60px; height:60px; font-size:1.5rem; margin: 0 auto 15px; background: #e1b12c;">
                        <span class="avatar-letter"><?= strtoupper(substr($s['username'], 0, 1)) ?></span>
                    </div>
                    <div class="friend-name" style="font-weight: bold; margin-bottom: 15px;"><?= htmlspecialchars($s['username']) ?></div>
                    <a href="send_request.php?to=<?= $s['id'] ?>" class="btn btn-primary" style="width: 100%; font-size: 0.8rem;">
                        + Connect
                    </a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php require_once '../includes/footer.php'; ?>

</body>
</html>