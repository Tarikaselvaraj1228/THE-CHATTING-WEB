<?php
session_start();
date_default_timezone_set('Asia/Kolkata'); // ✅ Set timezone here
require 'db.php';

// rest of your code...
if (!isset($_SESSION['user_id']) || !isset($_GET['friend_id'])) {
    exit();
}

$user_id = $_SESSION['user_id'];
$friend_id = intval($_GET['friend_id']);

// Fetch all messages between the two users
$query = "SELECT * FROM messages 
          WHERE (user_id = ? AND friend_id = ?) 
          OR (user_id = ? AND friend_id = ?) 
          ORDER BY timestamp ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
$stmt->execute();
$messages = $stmt->get_result();

// Display each message with alignment and timestamp
while ($row = $messages->fetch_assoc()) {
    $msg = htmlspecialchars($row['message']);
    $time = date('h:i A', strtotime($row['timestamp']));
    
    if ($row['user_id'] == $user_id) {
        // Sent message (right aligned)
        echo "<div class='message sent'>
                <div>$msg</div>
                <span class='time'>$time</span>
              </div>";
    } else {
        // Received message (left aligned)
        echo "<div class='message received'>
                <div>$msg</div>
                <span class='time'>$time</span>
              </div>";
    }
}
?>
