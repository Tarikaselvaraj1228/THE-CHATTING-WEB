<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle accept/decline request
if (isset($_POST['action'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    if ($action == 'accept') {
        $status = 'accepted';
    } elseif ($action == 'decline') {
        $status = 'declined';
    }

    $stmt = $conn->prepare("UPDATE friend_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $request_id);
    $stmt->execute();
    header("Location: getfriendrequest.php");
    exit();
}

// Fetch incoming friend requests for the logged-in user
$stmt = $conn->prepare("SELECT fr.id, u.username, fr.status FROM friend_requests fr JOIN users u ON fr.sender_id = u.id WHERE fr.receiver_id = ? AND fr.status = 'pending'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friend Requests</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Friend Requests</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td>
                                <form action="getfriendrequest.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="accept" class="btn-accept">Accept</button>
                                </form>
                                <form action="getfriendrequest.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="decline" class="btn-decline">Decline</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No friend requests found.</p>
        <?php endif; ?>
    </div>

    <footer>
        <a href="home.php" class="btn-back">Back to Home</a>
    </footer>
</body>
</html>

<style>
    body {
         font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #74ebd5, #ACB6E5);
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 800px;
        margin: 50px auto;
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f4f4f4;
    }

    td {
        background-color: #fff;
    }

    button {
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-accept {
        background-color: #4CAF50;
        color: white;
        margin-right: 10px;
    }

    .btn-decline {
        background-color: #f44336;
        color: white;
    }

    .btn-accept:hover {
        background-color: #45a049;
    }

    .btn-decline:hover {
        background-color: #e53935;
    }

    footer {
        text-align: center;
        margin-top: 30px;
    }

    .btn-back {
        background-color: #2196F3;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
    }

    .btn-back:hover {
        background-color: #0b7dda;
    }
</style>
