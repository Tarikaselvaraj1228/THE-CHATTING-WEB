<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetching mutual friends (both accepted requests)
$query = "
    SELECT u.id, u.username 
    FROM users u 
    INNER JOIN friend_requests fr ON (fr.sender_id = u.id OR fr.receiver_id = u.id)
    WHERE (fr.sender_id = ? OR fr.receiver_id = ?) 
    AND fr.status = 'accepted' 
    AND u.id != ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$friends = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friend List</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="friendlist-container">
            <h2>Your Friends</h2>
            <?php if ($friends->num_rows > 0): ?>
                <ul class="friend-list">
                    <?php while ($friend = $friends->fetch_assoc()): ?>
                        <li>
                            <a href="chat.php?friend_id=<?php echo $friend['id']; ?>" class="friend-item">
                                <span class="friend-name"><?php echo htmlspecialchars($friend['username']); ?></span>
                                <button class="chat-btn">Chat</button>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No friends to display. Start sending requests!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

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
        width: 90%;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    header h1 {
        font-size: 24px;
        color: #333;
    }

    .logout-btn {
        background-color: #e74c3c;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
    }

    .logout-btn:hover {
        background-color: #c0392b;
    }

    .friendlist-container {
        text-align: center;
    }

    .friend-list {
        list-style: none;
        padding: 0;
    }

    .friend-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 10px 0;
        background-color: #ecf0f1;
        padding: 15px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .friend-list li:hover {
        background-color: #bdc3c7;
    }

    .friend-name {
        font-size: 18px;
        font-weight: bold;
    }

    .chat-btn {
        background-color: #3498db;
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .chat-btn:hover {
        background-color: #2980b9;
    }
</style>
