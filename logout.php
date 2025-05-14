<?php
// Logout logic
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="logout-container">
        <h2>You have been logged out successfully</h2>
        <p><a href="index.php">Click here to log in again</a></p>
    </div>
</body>
</html>

