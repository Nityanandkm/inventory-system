<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 15px 0;
            text-align: center;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            text-align: center;
        }
        nav ul li {
            display: inline;
            margin: 0 15px;
        }
        nav ul li a {
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
        }
        nav ul li a:hover {
            color: #45a049;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .auth-buttons {
            text-align: right;
            margin: 20px 0;
        }
        .auth-buttons a {
            text-decoration: none;
            color: white;
            background-color: #4CAF50;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
        }
        .auth-buttons a.logout {
            background-color: #f44336;
        }
        .auth-buttons a:hover {
            background-color: #45a049;
        }
        .auth-buttons a.logout:hover {
            background-color: #e53935;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to JSRC IT Solutions</h1>
        <div class="auth-buttons">
            <?php if (isset($_SESSION['username'])): ?>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                <a href="logout.php" class="logout">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </header>
    <div class="container">
        <nav>
            <ul>
                <li><a href="billing.php">Billing</a></li>
                <li><a href="stock_details.php">Stock Details</a></li>
                <li><a href="purchases.php">Purchases</a></li>
                <li><a href="sales.php">Sales</a></li>
                <li><a href="allowances.php">Allowances</a></li>
                <li><a href="stock.php">Track Stocks</a></li>
                <li><a href="quoatation.php">Sales Quoatation</a></li>


            </ul>
        </nav>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> JSRC IT Solutions | Mobile:9740755162 | All Rights Reserved</p>
    </footer>
</body>
</html>
