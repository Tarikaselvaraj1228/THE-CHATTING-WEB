<?php
$servername = "localhost";  // Hostname (localhost if local)
$username = "root";         // Database username (use your own username if different)
$password = "";             // Database password (use your own password if different)
$dbname = "chatapp";        // Database name (use your own database name)

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Uncomment this line for debugging purposes:
// echo "Connected successfully";
?>
