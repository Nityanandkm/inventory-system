<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged Out</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .message-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .message-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .message-container p {
            margin-bottom: 20px;
            color: #555;
        }
        .message-container a {
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
            background-color: #e8f5e9;
            padding: 10px 20px;
            border-radius: 4px;
        }
        .message-container a:hover {
            background-color: #c8e6c9;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h2>Logged Out</h2>
        <p>You have been successfully logged out.</p>
        <a href="index.php">Return to Home</a>
    </div>
</body>
</html>
