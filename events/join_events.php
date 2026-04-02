<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION["user_id"])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user = $_SESSION["user_id"];

if(!isset($_GET["event_id"])){
    header("Location: view_events.php");
    exit();
}

$event_id = mysqli_real_escape_string($conn, $_GET["event_id"]);

// --- HANDLE JOIN ACTION ---
if(isset($_POST['confirm_join'])) {
    $check = $conn->query("SELECT * FROM event_participants WHERE event_id='$event_id' AND user_id='$current_user'");

    if($check === false){
        die("Query error: " . $conn->error);
    }

    if($check->num_rows == 0){
        $sql_join = "INSERT INTO event_participants (event_id, user_id) VALUES ('$event_id', '$current_user')";
        if($conn->query($sql_join)){
            header("Location: view_events.php?joined=success");
            exit();
        }
    } else {
        header("Location: view_events.php?joined=already");
        exit();
    }
}

// --- FETCH EVENT & PLACE DETAILS ---
$sql = "SELECT e.*, p.place_name, p.category, p.lat, p.lng 
        FROM events e 
        JOIN places p ON e.place_id = p.place_id 
        WHERE e.id = '$event_id'";

$res = $conn->query($sql);

// Guard: if query failed or event not found
if($res === false){
    die("Event query error: " . $conn->error);
}

$event = $res->fetch_assoc();

if(!$event){
    header("Location: view_events.php");
    exit();
}

// Check if user is already a participant
$check_status = $conn->query("SELECT * FROM event_participants WHERE event_id='$event_id' AND user_id='$current_user'");

if($check_status === false){
    die("Status query error: " . $conn->error);
}

$already_joined = ($check_status->num_rows > 0);

// Set coordinates for the map
$map_coords = ($event['lat'] && $event['lng']) ? $event['lat'] . "," . $event['lng'] : "0,0";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($event['place_name']); ?> | Converge</title>
    <link rel="stylesheet" href="../style1.css">
</head>
<body>

<?php require_once '../includes/header.php'; ?>

<main class="dashboard-page">
    <div style="max-width: 900px; margin: 0 auto; padding: 20px;">
        
        <a href="view_events.php" style="text-decoration: none; color: #d63031; font-weight: bold; display: inline-block; margin-bottom: 20px;">
            ← Back to Events
        </a>
        
        <div class="dash-card" style="padding: 0; overflow: hidden; border-radius: 15px;">
            
            <div style="width: 100%; height: 400px; background: #eee;">
                <iframe 
                    width="100%" 
                    height="100%" 
                    frameborder="0" 
                    style="border:0" 
                    src="https://maps.google.com/maps?q=<?php echo $map_coords; ?>&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                    allowfullscreen>
                </iframe>
            </div>

            <div style="padding: 40px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <div>
                        <span style="background: #fff5f5; color: #d63031; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; border: 1px solid #fed7d7;">
                            <?php echo htmlspecialchars($event['category']); ?>
                        </span>
                        <h1 style="margin: 10px 0 5px 0; font-size: 2.2rem;">
                            <?php echo htmlspecialchars($event['place_name']); ?>
                        </h1>
                        <p style="color: #666; font-size: 1rem;">📍 Venue Location ID: #<?php echo $event['place_id']; ?></p>
                    </div>
                    
                    <div style="text-align: right; background: #fafafa; padding: 15px; border-radius: 10px; border: 1px solid #eee;">
                        <div style="font-size: 1.4rem; font-weight: bold; color: #333;">
                            <?php echo date('h:i A', strtotime($event['event_time'])); ?>
                        </div>
                        <div style="color: #d63031; font-weight: 600;">
                            <?php echo date('D, M d Y', strtotime($event['event_time'])); ?>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 40px;">
                    <h3 style="margin-bottom: 15px; color: #333;">About this Meetup</h3>
                    <p style="font-size: 1.1rem; line-height: 1.8; color: #555;">
                        <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                    </p>
                </div>

                <?php if($already_joined): ?>
                    <div style="background: #f0fff4; color: #276749; padding: 20px; border-radius: 10px; text-align: center; font-weight: bold; border: 1px solid #c6f6d5;">
                        You're on the guest list! See you there.
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <button type="submit" name="confirm_join" class="btn btn-primary" style="width: 100%; padding: 20px; font-size: 1.2rem; border-radius: 10px;">
                            Join this Event
                        </button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>

</body>
</html>