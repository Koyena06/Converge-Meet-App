<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/db.php';

$user_id = $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $place_id = $_POST["place"];
    $event_time = $_POST["time"];
    $description = $_POST["description"];

    // Using Prepared Statements for security
    $stmt = $conn->prepare("INSERT INTO events (creator_id, place_id, event_time, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $place_id, $event_time, $description);

    if ($stmt->execute()) {
        $message = "Success! Your event has been created.";
    } else {
        $message = "Error: Could not create event.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Event | Converge</title>
    <link rel="stylesheet" href="../style1.css">
</head>
<body>

<?php 
// Header consistent with dashboard.php
require_once '../includes/header.php'; 
?>

<main class="dashboard-page">

    <div class="hero-content" style="text-align: center; margin-bottom: 30px;">
        <h1 class="page-title">Plan a <em>Meetup</em></h1>
        <p class="page-subtitle">Set the time and place to connect with others.</p>
    </div>

    <div style="max-width: 600px; margin: 0 auto;">
        <div class="dash-card">
            
            <?php if(!empty($message)): ?>
                <div style="background: #e1f5fe; color: #0288d1; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; border: 1px solid #b3e5fc;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">Place ID</label>
                    <input type="number" name="place" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                    <small style="color: #888;">Enter the ID of the location where the event will be held.</small>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">Event Date & Time</label>
                    <input type="datetime-local" name="time" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">Description</label>
                    <textarea name="description" rows="4" placeholder="What's the plan? (e.g. Coffee and coding, afternoon hike...)" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;"></textarea>
                </div>
                
                <div style="display: flex; gap: 15px; align-items: center;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 14px;">Create Event</button>
                    <a href="../user/dashboard.php" class="btn btn-outline" style="text-decoration: none; color: #666; border: 1px solid #ccc; padding: 12px 20px; border-radius: 8px;">Cancel</a>
                </div>
            
            </form>
        </div>
    </div>

</main>

<?php require_once '../includes/footer.php'; ?>

</body>
</html>