<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome - <?php echo htmlspecialchars($_SESSION['username']); ?></title>
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
      background: white;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      text-align: center;
      max-width: 400px;
      width: 90%;}

        h1 {
            margin-bottom: 30px;
            color: #333;
        }
        .btn-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .btn {
            padding: 15px;
            border: none;
            background-color:#6C63FF;
            color: white;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
      opacity: 0.9;
    }


        .logout-btn {
            margin-top: 30px;
            background-color: #e74c3c;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h1>
    
    <div class="btn-container">
        <a class="btn" href="givefriendrequest.php">Give Friend Request 🔍</a>
        <a class="btn" href="getfriendrequest.php">Get Friend Requests 📥</a>
        <a class="btn" href="friendlist.php">Friend List & Chat 💬</a>
    </div>

    <a class="btn logout-btn" href="logout.php">Logout 🚪</a>
</div>

</body>
</html>
