<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/db.php';

$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['name'] ?? 'User');

/* FRIEND COUNT */
$stmt1 = $conn->prepare("
    SELECT COUNT(*) 
    FROM friends 
    WHERE (sender_id = ? OR receiver_id = ?) 
    AND status = 'accepted'
");
$stmt1->bind_param("ii", $user_id, $user_id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$friends_count = $result1->fetch_row()[0];

/* EVENT COUNT (Events created by the user) */
$stmt2 = $conn->prepare("
    SELECT COUNT(*) 
    FROM events 
    WHERE creator_id = ?
");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$events_count = $result2->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Converge</title>
    <link rel="stylesheet" href="../style1.css">
    <style>
        a.dash-card {
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        a.dash-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            background-color: #fdfdfd;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #d63031;
            display: block;
        }
    </style>
</head>
<body>

<?php require_once '../includes/header.php'; ?>

<main class="dashboard-page">

    <div class="hero-content" style="padding-top: 40px; text-align: center;">
        <h1 class="page-title">
            Hello, <em><?php echo $username; ?></em>
        </h1>
        <p class="page-subtitle">
            Welcome back to your community hub.
        </p>
    </div>

    <div class="dashboard-grid">
        <div class="dash-card" style="text-align: center;">
            <span class="stat-number"><?php echo $friends_count; ?></span>
            <p style="font-weight: bold; margin-top: 10px;">Friends</p>
            <p style="font-size: 0.85rem; color: #666;">Connected members</p>
        </div>

        <div class="dash-card" style="text-align: center;">
            <span class="stat-number"><?php echo $events_count; ?></span>
            <p style="font-weight: bold; margin-top: 10px;">My Events</p>
            <p style="font-size: 0.85rem; color: #666;">Meetups organized by you</p>
        </div>
    </div>

    <hr style="border: 0; border-top: 1px solid #eee; margin: 40px 0;">

    <h2 class="section-title" style="text-align: center; margin-bottom: 30px;">Quick Access</h2>

    <div class="dashboard-grid">
        <a href="../events/create_event.php" class="dash-card" style="border: 2px dashed #d63031;">
            <h3> Create Event</h3>
            <p>Host a new meetup</p>
        </a>

        <a href="../events/view_events.php" class="dash-card">
            <h3> All Events</h3>
            <p>Browse upcoming meetups</p>
        </a>

        <a href="../friends/friends.php" class="dash-card">
            <h3> Find Friends</h3>
            <p>See suggestions & requests</p>
        </a>

        <a href="../location/map.php" class="dash-card">
            <h3> View Map</h3>
            <p>Track live connections</p>
        </a>

        <a href="../places/suggest_places.php" class="dash-card">
            <h3> Discover Places</h3>
            <p>Top spots in Bhubaneswar</p>
        </a>

        <a href="../user/profile.php" class="dash-card">
            <h3> My Profile</h3>
            <p>Update your details</p>
        </a>
    </div>

</main>

<?php require_once '../includes/footer.php'; ?>

</body>
</html>