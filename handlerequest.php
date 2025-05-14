<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action']; // 'accept' or 'decline'

    // Retrieve the friend request
    $stmt = $conn->prepare("SELECT * FROM friend_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();

    if ($request && $request['receiver_id'] == $user_id) {
        if ($action == 'accept') {
            $stmt = $conn->prepare("UPDATE friend_requests SET status = 'accepted' WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            echo "Request Accepted!";
        } elseif ($action == 'decline') {
            $stmt = $conn->prepare("UPDATE friend_requests SET status = 'declined' WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            echo "Request Declined!";
        }
    } else {
        echo "Invalid request!";
    }
    exit();
}

// Fetch the pending friend requests
$stmt = $conn->prepare("SELECT fr.id, u.username FROM friend_requests fr JOIN users u ON u.id = fr.sender_id WHERE fr.receiver_id = ? AND fr.status = 'pending'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$requests = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handle Friend Requests</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Friend Requests</h2>
        <?php if ($requests->num_rows > 0): ?>
            <ul>
                <?php while ($request = $requests->fetch_assoc()): ?>
                    <li>
                        <span><?php echo $request['username']; ?></span>
                        <button class="accept-btn" data-id="<?php echo $request['id']; ?>">Accept</button>
                        <button class="decline-btn" data-id="<?php echo $request['id']; ?>">Decline</button>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No pending friend requests.</p>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function() {
            $('.accept-btn').click(function() {
                var requestId = $(this).data('id');
                handleRequest(requestId, 'accept');
            });

            $('.decline-btn').click(function() {
                var requestId = $(this).data('id');
                handleRequest(requestId, 'decline');
            });

            function handleRequest(requestId, action) {
                $.ajax({
                    type: 'POST',
                    url: 'handlerequest.php',
                    data: { 
                        request_id: requestId,
                        action: action 
                    },
                    success: function(response) {
                        alert(response);
                        location.reload();  // Reload page to update the request list
                    },
                    error: function() {
                        alert("An error occurred. Please try again.");
                    }
                });
            }
        });
    </script>
</body>
</html>
