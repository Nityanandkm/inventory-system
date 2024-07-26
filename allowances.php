<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    echo "Please login first to access this page.";
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $travel_from = $_POST['travel_from'];
    $travel_to = $_POST['travel_to'];
    $investment = $_POST['investment'];
    $amount = $_POST['amount'];
    $total = $_POST['total'];

    $stmt = $pdo->prepare("INSERT INTO allowances (date, travel_from, travel_to, investment, amount, total) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$date, $travel_from, $travel_to, $investment, $amount, $total])) {
        $message = "<p style='color: green;'>Allowance entry added!</p>";
    } else {
        $message = "<p style='color: red;'>Error: Could not add allowance entry.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Allowances</title>
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
        .allowance-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .allowance-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .allowance-container input[type="text"],
        .allowance-container input[type="date"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .allowance-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .allowance-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="allowance-container">
        <h2>Allowances</h2>
        <div class="message"><?php echo $message; ?></div>
        <form action="allowances.php" method="post">
            <input type="date" name="date" placeholder="Date" required><br>
            <input type="text" name="travel_from" placeholder="Travel From" required><br>
            <input type="text" name="travel_to" placeholder="Travel To" required><br>
            <input type="text" name="investment" placeholder="Investment" required><br>
            <input type="text" name="amount" placeholder="Amount" required><br>
            <input type="text" name="total" placeholder="Total" required><br>
            <input type="submit" value="Add Allowance">
        </form>
    </div>
</body>
</html>
