<?php
session_start();
include 'config.php';
include 'functions.php';
require 'libs/fpdf186/fpdf.php'; // Include the FPDF library

if (!isset($_SESSION['username'])) {
    echo "Please login first to access this page.";
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $customer_contact = $_POST['customer_contact'];
    $customer_email = $_POST['customer_email'];
    $quotation_date = $_POST['quotation_date'];
    $items = $_POST['items'];

    // Insert data into the quotations table
    $query = "INSERT INTO quotations (customer_name, customer_contact, customer_email, quotation_date)
              VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$customer_name, $customer_contact, $customer_email, $quotation_date]);
    $quotation_id = $pdo->lastInsertId(); // Get the last inserted ID

    // Prepare to insert items into the quotation_items table
    $item_query = "INSERT INTO quotation_items (quotation_id, description, unit_price, quantity, total, amount, gst)
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    $item_stmt = $pdo->prepare($item_query);

    // Generate PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 10, 'Sales Quotation', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 9);

    // Add logo on the left side
    $pdf->Image('logos/jsrtech.jpg', 10, 10, 30, 20); // Adjust the path, x, y, width, and height as needed

    // Set x-coordinate for company information to align it to the right
    $pdf->SetXY($pdf->GetPageWidth() - 100, 10); // Adjust x-coordinate for positioning
    $pdf->SetFont('Arial', '', 9); // Set font
    $pdf->Cell(0, 5, 'SRI RAM COMPUTERS', 0, 1, 'R');
    $pdf->SetX($pdf->GetPageWidth() - 100); // Set x-coordinate again to align subsequent lines
    $pdf->Cell(0, 5, 'Kurbet Complex, College Road, Kalloli', 0, 1, 'R');
    $pdf->SetX($pdf->GetPageWidth() - 100); // Set x-coordinate again
    $pdf->Cell(0, 5, 'Kalloli, Karnataka, 591224', 0, 1, 'R');
    $pdf->SetX($pdf->GetPageWidth() - 100); // Set x-coordinate again
    $pdf->Cell(0, 5, 'Phone: 9740755162', 0, 1, 'R');
    $pdf->SetX($pdf->GetPageWidth() - 100); // Set x-coordinate again
    $pdf->Cell(0, 5, 'Email: anandjani@gmail.com', 0, 1, 'R');

    // Draw a horizontal line below the company information
    $pdf->SetLineWidth(0.5);
    $pdf->Line(10, 36, $pdf->GetPageWidth() - 10, 36); // Adjust the width to cover the page

    // Add some space below the horizontal line
    $pdf->Ln(2);

    // Quote Details
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(50, 5, 'Quote # :' . $quotation_id, 0, 0, 'L');
    $pdf->Cell(0, 5, 'Date :' . $quotation_date, 0, 1, 'R');
    $pdf->Ln(3);

    $pdf->MultiCell(0, 5, 'To,' . "\n" . 'The Manager,' . "\n" . $customer_name, 0, 'L');
    $pdf->Ln(3);

    $pdf->MultiCell(0, 5, 'Dear Sir,' . "\n" . 'Subject: Quotation for ' . implode(', ', array_column($items, 'description')) . ' regarding.' . "\n" . 'We thank for your kind response shown during our visit to your office.' . "\n" . 'With reference to the above cited subject and discussions. We are pleased to offer below mentioned prices' . "\n" . 'towards the supply item/items. Kindly confirm your order for the same at the earliest', 0, 'L');
    $pdf->Ln(3);

    // Table Header
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(10, 6, 'Sl No', 1, 0, 'C');
    $pdf->Cell(70, 6, 'Item Description', 1, 0, 'C');
    $pdf->Cell(20, 6, 'Unit Price', 1, 0, 'C');
    $pdf->Cell(20, 6, 'Quantity', 1, 0, 'C');
    $pdf->Cell(20, 6, 'Amount', 1, 0, 'C');
    $pdf->Cell(20, 6, 'GST%', 1, 0, 'C');
    $pdf->Cell(30, 6, 'Total', 1, 1, 'C');

    // Table Body
    $pdf->SetFont('Arial', '', 9);
    $total_amount = 0;
    foreach ($items as $index => $item) {
        $item_description = $item['description'];
        $unit_price = $item['unit_price'];
        $quantity = $item['quantity'];
        $gst = $item['gst'];
        $amount = $unit_price * $quantity;
        $total = $amount + ($amount * $gst / 100);
        $total_amount += $total;

        // Insert item into database
        $item_stmt->execute([$quotation_id, $item_description, $unit_price, $quantity, $total, $amount, $gst]);

        $pdf->Cell(10, 6, $index + 1, 1, 0, 'C');
        $pdf->Cell(70, 6, $item_description, 1, 0, 'L');
        $pdf->Cell(20, 6, number_format($unit_price, 2), 1, 0, 'R');
        $pdf->Cell(20, 6, $quantity, 1, 0, 'C');
        $pdf->Cell(20, 6, number_format($amount, 2), 1, 0, 'R');
        $pdf->Cell(20, 6, number_format($gst, 2) . '%', 1, 0, 'R');
        $pdf->Cell(30, 6, number_format($total, 2), 1, 1, 'R');
    }

    // Total Amount
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(140, 6, 'Total Amount', 1, 0, 'R');
    $pdf->Cell(30, 6, number_format($total_amount, 2), 1, 1, 'R');
    
    // Terms and Conditions
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 5, 'Terms and Conditions:', 0, 'L');
    $pdf->SetFont('Arial', '', 8);
    $pdf->MultiCell(0, 4, 'Taxes: GST Extra as shown above', 0, 'L');
    $pdf->MultiCell(0, 4, 'Order to be released on "SRI RAM COMPUTERS, Kalloli"', 0, 'L');
    $pdf->MultiCell(0, 4, 'Delivery: Within 15 days from the order date', 0, 'L');
    $pdf->MultiCell(0, 4, 'Payment: 100% against replacement of Spares', 0, 'L');
    $pdf->MultiCell(0, 4, 'Warranty: As mentioned on product', 0, 'L');
    $pdf->Ln(5);
    $pdf->MultiCell(0, 4, 'Please be in touch with the undersigned for any more details. Thanking and assuring you of our best services at all times ahead.', 0, 'L');
    $pdf->Ln(3);
    $pdf->MultiCell(0, 4, 'For Your SRI RAM COMPUTERS', 0, 'L');
    $pdf->MultiCell(0, 4, 'Authorized Signatory', 0, 'L');

    // Save the PDF to a file
    $pdf_filename = 'quotations/quotation_' . $quotation_id . '.pdf';
    $pdf->Output('F', $pdf_filename);

    // Provide a download link
    echo "<script>
        alert('Quotation generated successfully.');
        window.location.href = '$pdf_filename';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Quotation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-group input,
        .form-group textarea {
            width: calc(100% - 22px);
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
            color: #333;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #007BFF;
            outline: none;
        }
        .form-group button {
            padding: 12px 24px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .item input {
            margin-bottom: 8px;
        }
        .item label {
            display: inline-block;
            width: 130px;
            font-weight: normal;
            color: #555;
        }
        .item input[type="number"] {
            width: calc(100% - 22px);
        }
        button {
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button[type="button"] {
            background-color: #6c757d;
            color: white;
        }
        button[type="button"]:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Quotation</h1>
        <form method="POST" action="quoatation.php">
            <div class="form-group">
                <label for="customer_name">Customer Name:</label>
                <input type="text" id="customer_name" name="customer_name" required>
            </div>

            <div class="form-group">
                <label for="customer_contact">Customer Contact:</label>
                <input type="text" id="customer_contact" name="customer_contact" required>
            </div>

            <div class="form-group">
                <label for="customer_email">Customer Email:</label>
                <input type="email" id="customer_email" name="customer_email" required>
            </div>

            <div class="form-group">
                <label for="quotation_date">Quotation Date:</label>
                <input type="date" id="quotation_date" name="quotation_date" required>
            </div>

            <div id="item-container">
                <div class="form-group item">
                    <label for="description[]">Description:</label>
                    <input type="text" name="items[0][description]" required><br>

                    <label for="unit_price[]">Unit Price:</label>
                    <input type="number" name="items[0][unit_price]" step="0.01" required oninput="calculateAmount(0)"><br>

                    <label for="quantity[]">Quantity:</label>
                    <input type="number" name="items[0][quantity]" required oninput="calculateAmount(0)"><br>

                    <label for="gst[]">GST (%):</label>
                    <input type="number" name="items[0][gst]" step="0.01" required oninput="calculateAmount(0)"><br>

                    <label for="amount[]">Amount:</label>
                    <input type="number" name="items[0][amount]" step="0.01" readonly><br>
                </div>
            </div>

            <button type="button" onclick="addItem()">Add Item</button>
            <button type="submit">Generate Quotation</button>
        </form>
    </div>

    <script>
        let itemIndex = 1;

        function addItem() {
            const itemContainer = document.getElementById('item-container');
            const newItemDiv = document.createElement('div');
            newItemDiv.classList.add('item');
            newItemDiv.innerHTML = `
                <label>Description:</label>
                <input type="text" name="items[${itemIndex}][description]" required><br>

                <label>Unit Price:</label>
                <input type="number" name="items[${itemIndex}][unit_price]" step="0.01" required oninput="calculateAmount(${itemIndex})"><br>

                <label>Quantity:</label>
                <input type="number" name="items[${itemIndex}][quantity]" required oninput="calculateAmount(${itemIndex})"><br>

                <label>GST (%):</label>
                <input type="number" name="items[${itemIndex}][gst]" step="0.01" required oninput="calculateAmount(${itemIndex})"><br>

                <label>Amount:</label>
                <input type="number" name="items[${itemIndex}][amount]" step="0.01" readonly><br>
            `;
            itemContainer.appendChild(newItemDiv);
            itemIndex++;
        }

        function calculateAmount(index) {
            const unitPrice = document.querySelector(`input[name="items[${index}][unit_price]"]`).value;
            const quantity = document.querySelector(`input[name="items[${index}][quantity]"]`).value;
            const gst = document.querySelector(`input[name="items[${index}][gst]"]`).value;
            const amountInput = document.querySelector(`input[name="items[${index}][amount]"]`);

            if (unitPrice && quantity && gst) {
                const amount = parseFloat(unitPrice) * parseFloat(quantity);
                const totalAmount = amount + (amount * parseFloat(gst) / 100);
                amountInput.value = totalAmount.toFixed(2);
            } else {
                amountInput.value = '';
            }
        }
    </script>
</body>
</html>
