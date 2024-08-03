<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    echo "Please login first to access this page.";
    header("Location: login.php");
    exit;
}

// Fetch stock details
$stmt = $pdo->prepare("SELECT item_name, model, COUNT(*) AS quantity FROM stock_details GROUP BY item_name, model");
$stmt->execute();
$stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Details</title>
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
        .stock-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            text-align: center;
        }
        .stock-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="stock-container">
        <h2>Stock Details</h2>
        <table>
            <tr>
                <th>Item Name</th>
                <th>Model</th>
                <th>Quantity</th>
            </tr>
            <?php foreach ($stocks as $stock): ?>
            <tr>
                <td><?php echo htmlspecialchars($stock['item_name']); ?></td>
                <td><?php echo htmlspecialchars($stock['model']); ?></td>
                <td><?php echo htmlspecialchars($stock['quantity']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
