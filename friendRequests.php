<?php
session_start();
include('includes/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action.']);
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Handle sending a friend request
if (isset($_POST['action']) && $_POST['action'] === 'send') {
    $receiver_id = $_POST['receiver_id'];

    // Check if the receiver is not the same as the sender
    if ($receiver_id == $user_id) {
        echo json_encode(['success' => false, 'message' => 'You cannot send a friend request to yourself.']);
        exit();
    }

    // Check if a friend request already exists
    $query = "SELECT * FROM friend_requests WHERE sender_id = ? AND receiver_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $receiver_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Friend request already sent.']);
        exit();
    }

    // Insert the friend request into the database
    $query = "INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES (?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $receiver_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Friend request sent successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send friend request.']);
    }

    $stmt->close();
}

// Handle accepting a friend request
if (isset($_POST['action']) && $_POST['action'] === 'accept') {
    $request_id = $_POST['request_id'];

    // Update the status to accepted
    $query = "UPDATE friend_requests SET status = 'accepted' WHERE request_id = ? AND receiver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $request_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Friend request accepted.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to accept friend request.']);
    }

    $stmt->close();
}

// Handle rejecting a friend request
if (isset($_POST['action']) && $_POST['action'] === 'reject') {
    $request_id = $_POST['request_id'];

    // Update the status to rejected
    $query = "UPDATE friend_requests SET status = 'rejected' WHERE request_id = ? AND receiver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $request_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Friend request rejected.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reject friend request.']);
    }

    $stmt->close();
}

// Handle retrieving friend requests (for the logged-in user)
if (isset($_POST['action']) && $_POST['action'] === 'get') {
    // Get the friend requests where the logged-in user is the receiver
    $query = "SELECT fr.request_id, u.username, u.user_id, fr.status
              FROM friend_requests fr
              JOIN users u ON fr.sender_id = u.user_id
              WHERE fr.receiver_id = ? AND fr.status = 'pending'";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }

    echo json_encode(['success' => true, 'requests' => $requests]);

    $stmt->close();
}

$conn->close();
?>
