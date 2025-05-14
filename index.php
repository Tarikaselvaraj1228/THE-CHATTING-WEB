<?php
// index.php: Welcome screen with login & registration options
session_start();

// Redirect to chat if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: chat.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Welcome to ChatApp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
      width: 90%;
    }
    h1 {
      color: #333;
      margin-bottom: 30px;
    }
    .btn {
      display: block;
      width: 100%;
      padding: 15px;
      margin: 10px 0;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s ease;
    }
    .btn-login {
      background-color: #6C63FF;
      color: white;
    }
    .btn-register {
      background-color: #ffffff;
      color: #6C63FF;
      border: 2px solid #6C63FF;
    }
    .btn:hover {
      opacity: 0.9;
    }

  </style>
</head>
<body>
  <div class="container">
    <h1>👋 Welcome to ChatApp</h1>
    <form action="login.php" method="get">
      <button type="submit" class="btn btn-login">Login</button>
    </form>
    <form action="register.php" method="get">
      <button type="submit" class="btn btn-register">Register</button>
    </form>
  </div>

</body>
</html>
