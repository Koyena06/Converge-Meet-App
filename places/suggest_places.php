<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$suggest_query = "
    SELECT 
        p.id,
        p.place_name,
        p.category,
        p.lat,
        p.lng,
        COUNT(ui.interest_id) AS match_count
    FROM places p
    JOIN interests i   ON LOWER(p.category) = LOWER(i.interest_name)
    JOIN user_interests ui ON ui.interest_id = i.id
    WHERE ui.user_id = ?
    GROUP BY p.id, p.place_name, p.category, p.lat, p.lng
    ORDER BY match_count DESC
";

$stmt = mysqli_prepare($conn, $suggest_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result    = mysqli_stmt_get_result($stmt);
$suggested = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suggested Meetup Places</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Suggested Places Based on Your Interests</h2>

    <?php if (empty($suggested)): ?>
        <p>No suggestions found. Try adding more interests on your 
           <a href="../user/interests.php">interests page</a>.
        </p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Place Name</th>
                    <th>Category</th>
                    <th>Matches</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suggested as $index => $place): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($place['place_name']) ?></td>
                        <td><?= htmlspecialchars($place['category']) ?></td>
                        <td><?= (int) $place['match_count'] ?> interest(s)</td>
                        <td>
                            <a href="place_details.php?place_id=<?= $place['id'] ?>">
                                View Details
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <br>
    <a href="../user/dashboard.php">Back to Dashboard</a>
</body>
</html>
