<?php
// Backend logic
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $searchUsername = trim($_POST['search_username']);
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $searchUsername, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($receiver_id);
        $stmt->fetch();

        // Check if request already sent
        $check = $conn->prepare("SELECT * FROM friend_requests WHERE sender_id = ? AND receiver_id = ?");
        $check->bind_param("ii", $_SESSION['user_id'], $receiver_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows == 0) {
            $insert = $conn->prepare("INSERT INTO friend_requests (sender_id, receiver_id) VALUES (?, ?)");
            $insert->bind_param("ii", $_SESSION['user_id'], $receiver_id);
            if ($insert->execute()) {
                $message = "Friend request sent!";
            } else {
                $message = "Failed to send request.";
            }
        } else {
            $message = "Friend request already sent.";
        }
    } else {
        $message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Friend Request</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #74ebd5, #ACB6E5);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

        .container {
            width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            border: none;
            color: white;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #45a049;
        }

        .message {
            text-align: center;
            color: #0066cc;
            margin-top: 15px;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
        }

        .back-link a {
            color: #555;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Give Friend Request</h2>
        <form method="POST" action="">
            <input type="text" name="search_username" placeholder="Enter username to search" required>
            <button type="submit">Send Friend Request</button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <div class="back-link">
            <a href="home.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>
