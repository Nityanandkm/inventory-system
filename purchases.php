<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    echo "Please login first to access this page.";
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['scanned_copy'])) {
    $bill_no = $_POST['bill_no'];
    $name = $_POST['name'];
    $scanned_copy = file_get_contents($_FILES['scanned_copy']['tmp_name']);

    $stmt = $pdo->prepare("INSERT INTO purchases (bill_no, name, scanned_copy) VALUES (?, ?, ?)");
    if ($stmt->execute([$bill_no, $name, $scanned_copy])) {
        $message = "<p style='color: green;'>Purchase entry added!</p>";
    } else {
        $message = "<p style='color: red;'>Error: Could not add purchase entry.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchases</title>
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
        .purchase-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .purchase-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .purchase-container input[type="text"],
        .purchase-container input[type="file"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .purchase-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .purchase-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="purchase-container">
        <h2>Purchases</h2>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST") { echo $message; } ?>
        <form action="purchases.php" method="post" enctype="multipart/form-data">
            <input type="text" name="bill_no" placeholder="Bill No" required><br>
            <input type="text" name="name" placeholder="Name" required><br>
            <input type="file" name="scanned_copy" required><br>
            <input type="submit" value="Add Purchase">
        </form>
    </div>
</body>
</html>
