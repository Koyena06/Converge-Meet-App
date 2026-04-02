<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION["user_id"])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch all events joined with places to get the name instead of just ID
$sql = "SELECT e.*, p.place_name, p.category 
        FROM events e 
        LEFT JOIN places p ON e.place_id = p.place_id 
        ORDER BY e.event_time ASC";
$result = $conn->query($sql);

// Helper function to check if the participants table exists to prevent Fatal Errors
$table_exists = $conn->query("SHOW TABLES LIKE 'event_participants'")->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upcoming Events | Converge</title>
    <link rel="stylesheet" href="../style1.css">
</head>
<body>

<?php require_once '../includes/header.php'; ?>

<main class="dashboard-page">
    <div class="hero-content" style="text-align: center; margin-bottom: 40px;">
        <h1 class="page-title">Upcoming <em>Events</em></h1>
        <p class="page-subtitle">Discover meetups happening in your community.</p>
    </div>

    <div class="dashboard-grid">
        <?php while($row = $result->fetch_assoc()): 
            $event_id = $row["id"];
            
            // Logic to check if user joined (Only runs if the table actually exists)
            $is_joined = false;
            if ($table_exists) {
                $check = $conn->query("SELECT * FROM event_participants WHERE event_id='$event_id' AND user_id='$user_id'");
                $is_joined = ($check && $check->num_rows > 0);
            }
        ?>
        
        <div class="dash-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <span style="background: #fdf2f2; color: #d63031; padding: 4px 12px; border-radius: 15px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase;">
                    <?php echo htmlspecialchars($row['category'] ?? 'General'); ?>
                </span>
                <small style="color: #888;"><?php echo date('M d, Y', strtotime($row["event_time"])); ?></small>
            </div>

            <h3 style="margin-bottom: 10px;"><?php echo htmlspecialchars(substr($row["description"], 0, 40)); ?>...</h3>
            
            <p style="font-size: 0.9rem; color: #666; margin-bottom: 20px;">
                <strong>Time:</strong> <?php echo date('h:i A', strtotime($row["event_time"])); ?><br>
                <strong>Location:</strong> <?php echo htmlspecialchars($row["place_name"] ?? 'Unknown Venue'); ?>
            </p>

            <?php if($is_joined): ?>
                <a href="join_events.php?event_id=<?php echo $event_id; ?>" class="btn btn-gold" style="display: block; text-align: center; font-size: 0.8rem; text-decoration: none;">
                    View Details (Joined)
                </a>
            <?php else: ?>
                <a href="join_events.php?event_id=<?php echo $event_id; ?>" class="btn btn-primary" style="display: block; text-align: center; font-size: 0.8rem; text-decoration: none;">
                    Join Event
                </a>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>