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
    $item_name = $_POST['item_name'];
    $model = $_POST['model'];
    $purchase_date = $_POST['purchase_date'];
    $invoice_no = $_POST['invoice_no'];
    $submodel = $_POST['submodel'];

    $stmt = $pdo->prepare("INSERT INTO stock_details (item_name, model, purchase_date, invoice_no, submodel) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$item_name, $model, $purchase_date, $invoice_no, $submodel])) {
        $message = "<p style='color: green;'>Stock details added!</p>";
    } else {
        $message = "<p style='color: red;'>Error: Could not add stock details.</p>";
    }
}
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
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .stock-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .stock-container input[type="text"],
        .stock-container input[type="date"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .stock-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .stock-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="stock-container">
        <h2>Stock Details</h2>
        <div class="message"><?php echo $message; ?></div>
        <form action="stock_details.php" method="post">
            <input type="text" name="item_name" placeholder="Item Name" required><br>
            <input type="text" name="model" placeholder="Model" required><br>
            <input type="date" name="purchase_date" placeholder="Purchase Date" required><br>
            <input type="text" name="invoice_no" placeholder="Invoice No" required><br>
            <input type="text" name="submodel" placeholder="Submodel" required><br>
            <input type="submit" value="Add Stock Details">
        </form>
    </div>
</body>
</html>
