<?php
session_start();
require 'db.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $searchUsername = $_POST['searchUsername'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $searchUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $error = "No user found with that username!";
    } else {
        $row = $result->fetch_assoc();
        $friend_id = $row['id'];

        // Check if a friend request has already been sent or accepted
        $checkRequest = $conn->prepare("SELECT * FROM friend_requests WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
        $checkRequest->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
        $checkRequest->execute();
        $existingRequest = $checkRequest->get_result()->fetch_assoc();

        if ($existingRequest) {
            $error = "You have already sent a request or are already friends!";
        } else {
            // Send friend request
            $sendRequest = $conn->prepare("INSERT INTO friend_requests (sender_id, receiver_id) VALUES (?, ?)");
            $sendRequest->bind_param("ii", $user_id, $friend_id);
            if ($sendRequest->execute()) {
                $success = "Friend request sent to $searchUsername!";
            } else {
                $error = "Failed to send friend request.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Friend Request</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(200deg, #74ebd5,#ACB6E5);
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .input-field {
            width: 100%;
            padding: 10px;
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            color: red;
        }
        .success-message {
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Send Friend Request</h2>
    <form method="POST" action="givefriendrequest.php">
        <input type="text" name="searchUsername" class="input-field" placeholder="Enter username to search" required>
        <button type="submit" class="button">Search</button>
    </form>

    <?php if (isset($error)): ?>
        <p class="message"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <p class="success-message"><?php echo $success; ?></p>
    <?php endif; ?>
    
    <a href="home.php" style="text-align: center; display: block; margin-top: 20px;">Back to Home</a>
</div>

</body>
</html>
