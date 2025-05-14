<?php
session_start();
date_default_timezone_set('Asia/Kolkata'); //  Set timezone here
require 'db.php';

// rest of your code...
if (!isset($_SESSION['user_id']) || !isset($_POST['message']) || !isset($_POST['friend_id'])) {
    exit();
}

$user_id = $_SESSION['user_id'];
$friend_id = intval($_POST['friend_id']);
$message = trim($_POST['message']);
$timestamp = date('Y-m-d H:i:s');

if ($message === '') exit();

// Insert message into database
$query = "INSERT INTO messages (user_id, friend_id, message, timestamp) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiss", $user_id, $friend_id, $message, $timestamp);
$stmt->execute();
?>
