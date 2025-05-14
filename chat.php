<?php 
session_start(); 
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get list of friends (accepted requests)
$query = "SELECT u.id, u.username 
          FROM users u 
          JOIN friend_requests fr 
          ON (fr.sender_id = u.id OR fr.receiver_id = u.id) 
          WHERE (fr.sender_id = ? OR fr.receiver_id = ?) 
          AND fr.status = 'accepted' 
          AND u.id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$friends = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chat</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #74ebd5, #ACB6E5);
      padding: 20px;
    }
    .chat-container {
      display: flex;
      justify-content: space-between;
    }
    .friends-list {
      width: 25%;
      background-color: #fff;
      padding: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .friend {
      padding: 10px;
      cursor: pointer;
      margin: 5px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .chat-box {
      width: 70%;
      background-color: #fff;
      padding: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
    }
    .messages {
      flex: 1;
      height: 400px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
    }
    .message {
      margin: 5px 0;
      padding: 10px;
      border-radius: 10px;
      max-width: 60%;
      position: relative;
    }
    .sent {
      align-self: flex-end;
      background-color: #d1f7c4;
    }
    .received {
      align-self: flex-start;
      background-color: #f1f1f1;
    }
    .time {
      font-size: 12px;
      color: #555;
      display: block;
      margin-top: 5px;
      text-align: right;
    }
    .send-message {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }
    .send-message input {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .send-message button {
      padding: 10px 15px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<div class="chat-container">
  <div class="friends-list">
    <h3>Friends</h3>
    <?php while ($friend = $friends->fetch_assoc()): ?>
      <div class="friend" onclick="startChat(<?php echo $friend['id']; ?>)">
        <?php echo htmlspecialchars($friend['username']); ?>
      </div>
    <?php endwhile; ?>
  </div>

  <div class="chat-box">
    <h3>Chat</h3>
    <div class="messages" id="messages"></div>

    <div class="send-message">
      <input type="text" id="messageInput" placeholder="Type a message...">
      <button onclick="sendMessage()">Send</button>
    </div>
  </div>
</div>

<script>
let currentFriendId = null;

function startChat(friendId) {
  currentFriendId = friendId;
  loadMessages(friendId);
}

function loadMessages(friendId) {
  $.ajax({
    url: 'getMessages.php',
    method: 'GET',
    data: { friend_id: friendId },
    success: function(response) {
      $('#messages').html(response);
      $('#messages').scrollTop($('#messages')[0].scrollHeight);
    }
  });
}

function sendMessage() {
  const message = $('#messageInput').val();
  if (message.trim() === '' || currentFriendId === null) return;

  $.ajax({
    url: 'sendMessage.php',
    method: 'POST',
    data: { message: message, friend_id: currentFriendId },
    success: function() {
      $('#messageInput').val('');
      loadMessages(currentFriendId); // Reload messages after sending
    }
  });
}

// Auto-refresh messages every 2 seconds
setInterval(function() {
  if (currentFriendId !== null) {
    loadMessages(currentFriendId);
  }
}, 2000);
</script>

</body>
</html>
