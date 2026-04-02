<?php
/* displays the details of the user who is already logged-in */
session_start();

// Auth check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

include("../config/db.php");

$user_id = $_SESSION["user_id"];

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch user interests (Assuming a table named user_interests)
$interest_sql = "
    SELECT i.interest_name 
    FROM user_interests ui
    JOIN interests i ON ui.interest_id = i.id 
    WHERE ui.user_id = ?
";
$i_stmt = $conn->prepare($interest_sql);
$i_stmt->bind_param("i", $user_id);
$i_stmt->execute();
$interests_result = $i_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Converge</title>
    <link rel="stylesheet" href="../style1.css">
</head>
<body>

<?php 
// Header consistent with dashboard.php
require_once '../includes/header.php'; 
?>

<main class="dashboard-page">
    
    <div class="hero-content" style="text-align: center; margin-bottom: 40px;">
        
                <div class="profile-placeholder" style="width:60px; height:40px; font-size:1.5rem; margin: 0 auto 15px; background: #d63031;">
                 <span class="avatar-letter"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>    
                
                </div>
            
        
        <h1 class="page-title"><em><?php echo htmlspecialchars($user['name']); ?></em></h1>
        <p class="page-subtitle">Member since <?php echo date('F Y', strtotime($user['created_at'] ?? 'now')); ?></p>
    </div>

    <div class="dashboard-grid">
        
        <div class="dash-card">
            <h2 class="section-title" style="font-size: 1.5rem; margin-top: 0;">Personal Details</h2>
            <div class="info-group">
                <label style="font-weight: bold; color: #888; font-size: 0.8rem; text-transform: uppercase;">Email Address</label>
                <p style="margin-bottom: 15px;"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="info-group">
                <label style="font-weight: bold; color: #888; font-size: 0.8rem; text-transform: uppercase;">Bio</label>
                <p><?php echo htmlspecialchars($user['bio'] ?? 'No bio added yet.'); ?></p>
            </div>
            <div style="margin-top: 20px;">
                <a href="edit_profile.php" class="btn btn-primary" style="padding: 10px 20px; font-size: 0.9rem;">Edit Profile</a>
            </div>
        </div>

        <div class="dash-card">
            <h2 class="section-title" style="font-size: 1.5rem; margin-top: 0;">My Interests</h2>
            <div class="interests-tags" style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php if ($interests_result->num_rows > 0): ?>
                    <?php while($row = $interests_result->fetch_assoc()): ?>
                        <span style="background: #fdf2f2; color: #d63031; padding: 5px 15px; border-radius: 20px; border: 1px solid #fab1a0; font-size: 0.9rem;">
                            <?php echo htmlspecialchars($row['interest_name']); ?>
                        </span>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No interests selected.</p>
                <?php endif; ?>
            </div>
            <div style="margin-top: 20px;">
                <a href="interests.php" class="btn btn-gold" style="padding: 10px 20px; font-size: 0.9rem;">Manage Interests</a>
            </div>
        </div>

    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

</main>

<?php require_once '../includes/footer.php'; ?>

</body>
</html>