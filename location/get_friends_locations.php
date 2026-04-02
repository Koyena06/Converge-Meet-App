<?php
session_start();
require_once '../config/db.php';

// Set header to JSON so the JavaScript fetch() can read it
header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "Not authenticated"]);
    exit();
}

$user_id = $_SESSION["user_id"];

/**
 * SQL BREAKDOWN:
 * 1. We select user details (id, name).
 * 2. We JOIN the locations table.
 * 3. We use a Subquery in the WHERE clause to ensure we only get the 
 * LATEST location (MAX updated_at) for each specific user.
 * 4. We JOIN the friends table to ensure we only see 'accepted' connections.
 */
$sql = "SELECT 
            u.id, 
            u.name, 
            l.latitude, 
            l.longitude, 
            l.updated_at as last_seen
        FROM users u
        INNER JOIN locations l ON u.id = l.user_id
        INNER JOIN friends f ON (f.sender_id = u.id OR f.receiver_id = u.id)
        WHERE (f.sender_id = ? OR f.receiver_id = ?) 
          AND f.status = 'accepted' 
          AND u.id != ?
          AND l.updated_at = (
              SELECT MAX(updated_at) 
              FROM locations 
              WHERE user_id = u.id
          )";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$friends = [];

while ($row = $result->fetch_assoc()) {
    // Format the timestamp for better readability in the map popup
    $row['last_seen'] = date('h:i A, M d', strtotime($row['last_seen']));
    $friends[] = $row;
}

// Return the array as a JSON string
echo json_encode($friends);

$stmt->close();
$conn->close();
?>