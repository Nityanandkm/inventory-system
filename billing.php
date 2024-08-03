<?php
session_start();
include 'config.php';
include 'functions.php';
require 'libs/fpdf186/fpdf.php';



if (!isset($_SESSION['username'])) {
    echo "Please login first to access this page.";
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ename = $_POST['ename'];
    $buyer_gstin = $_POST['gstin'];
    $item_names = $_POST['item_name'];
    $amounts = $_POST['amount'];
    $gsts = $_POST['gst'];
    $quantities = $_POST['quantity'];

    if (!preg_match('/^[A-Z0-9]{15}$/', $buyer_gstin)) {
        echo "<script>
            alert('Invalid GSTIN Number.');
            window.location.href = 'billing.php';
        </script>";
        exit;
    }

    // Get the next invoice number
    $stmt = $pdo->query("SELECT MAX(id) as invoice_number FROM billing");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $next_invoice_number = $result['invoice_number'] ? $result['invoice_number'] + 1 : 1;

    // Generate PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Tax Invoice', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);

    $pdf->Rect(5, 5, $pdf->GetPageWidth() - 10, $pdf->GetPageHeight() - 10);

    // Company Information
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(10, 20);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell(90, 30, '', 1, 1, 'L', true);
    $pdf->SetXY(10, 20);
    $pdf->MultiCell(90, 4, "SRI RAM COMPUTERS\n# Kurbet Complex, College Road, Kalloli - 591224\nTq: Mudalgi, Dist: Belagavi, Karnataka\nPhone: 8618421247\nEmail: sriramcomputers96@gmail.com", 0, 'L');

    // Invoice Details
    $pdf->SetXY(105, 20);
    $pdf->SetFillColor(255, 255, 255);
    $invoice_box_width = 95;
    $invoice_box_height = 20;
    $pdf->Cell($invoice_box_width, $invoice_box_height, '', 1, 0, 'L', true);
    $pdf->SetXY(105, 20);
    $pdf->SetFont('Arial', '', 8);
    $pdf->MultiCell($invoice_box_width, 4,
        'Invoice #: ' . $next_invoice_number . "\n" .
        'Date: ' . date('d-m-Y') . "\n" .
        'Place of Supply: Karnataka',
        0, 'L');

    // Move to the next line after Invoice Details
    $pdf->Ln(2);

    // Buyer Information
    $buyer_box_width = 95;
    $buyer_box_height = 18;
    $y_pos = $pdf->GetY(); // Store the current Y position
    $pdf->SetXY(105, $y_pos); // Set X to 105 and Y to the current position after Invoice Details
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell($buyer_box_width, $buyer_box_height, '', 1, 1, 'L', true);
    // Position for the content inside the box
    $pdf->SetXY(105, $y_pos + 1); // Set Y position inside the box
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($buyer_box_width, 4, 'Bill To:', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 8);
    // Use MultiCell for the buyer details to ensure they fit within the box
    $pdf->SetXY(105, $pdf->GetY()); // Set the position for the buyer details
    $pdf->MultiCell($buyer_box_width, 4,
        'Customer Name: ' .$ename . "\n" .
        'GSTIN Number: ' . $buyer_gstin . "\n" .
        'State: Karnataka',
        0, 'L');
    // Ensure there's no overlap with other content
    $pdf->Ln(2);

  // Set widths for each column to match the width of invoice details and buyer information
$col1_width = 10; // Width of Sl No column
$col2_width = 90; // Width of Item column
$col3_width = 25; // Width of Amount column
$col4_width = 20; // Width of GST column
$col5_width = 20; // Width of Quantity column
$col6_width = 25; // Width of Total column

// Item Details Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($col1_width, 6, 'Sl No', 1, 0, 'C');
$pdf->Cell($col2_width, 6, 'Item', 1, 0, 'C');
$pdf->Cell($col3_width, 6, 'Amount', 1, 0, 'C');
$pdf->Cell($col4_width, 6, 'GST', 1, 0, 'C');
$pdf->Cell($col5_width, 6, 'Quantity', 1, 0, 'C');
$pdf->Cell($col6_width, 6, 'Total', 1, 1, 'C');

// Item Details
$total_amount = 0;
$total_quantity = 0;
$total_gst_amount = 0;
$pdf->SetFont('Arial', '', 8);
for ($i = 0; $i < count($item_names); $i++) {
    $item_name = $item_names[$i];
    $amount = $amounts[$i];
    $gst = $gsts[$i];
    $quantity = $quantities[$i];
    $gst_amount = ($amount * $gst) / 100;
    $total = ($amount * $quantity) + $gst_amount;
    $total_amount += $total;
    $total_quantity += $quantity;
    $total_gst_amount += $gst_amount;

    $pdf->Cell($col1_width, 6, $i + 1, 1, 0, 'C');
    $pdf->Cell($col2_width, 6, $item_name, 1);
    $pdf->Cell($col3_width, 6, "\u{20B9}" . number_format($amount, 2), 1);
    $pdf->Cell($col4_width, 6, $gst . '%', 1);
    $pdf->Cell($col5_width, 6, $quantity, 1);
    $pdf->Cell($col6_width, 6, '&#8377;' . number_format($total, 2), 1, 1);
}

// Totals
$pdf->Ln(2); // Move to the next line before adding totals

// Set font for headers
$pdf->SetFont('Arial', 'B', 10);

// Add header cells with extra empty cells to move columns to the right
$pdf->Cell(10, 6, '', 0, 0, 'C'); // Empty cell for Sl No
$pdf->Cell(90, 6, '', 0, 0, 'C'); // Empty cell for Item (increased width to match item box)

// Add empty cells to push totals to the right (Adjust the number as needed)
$pdf->Cell(20, 6, '', 0, 0, 'C'); // Empty cell to move Total GST to the right
$pdf->Cell(25, 6, 'Total GST', 1, 0, 'C'); // Total GST label
$pdf->Cell(20, 6, 'Total Quantity', 1, 0, 'C'); // Total Quantity label
$pdf->Cell(25, 6, 'Total Amount', 1, 1, 'C'); // Total Amount label

// Set font for data cells
$pdf->SetFont('Arial', '', 8);

// Add data cells with extra empty cells to move values to the right (Adjust the number as needed)
$pdf->Cell(10, 6, '', 0, 0, 'C'); // Empty cell for Sl No
$pdf->Cell(90, 6, '', 0, 0, 'C'); // Empty cell for Item (increased width to match item box)

// Add empty cells to push totals to the right (Adjust the number as needed)
$pdf->Cell(20, 6, '', 0, 0, 'C'); // Empty cell to move Total GST value to the right
$pdf->Cell(25, 6, '&#x20B9;;' . number_format($total_gst_amount, 2), 1, 0, 'R'); // Total GST value
$pdf->Cell(20, 6, $total_quantity, 1, 0, 'R'); // Total Quantity value
$pdf->Cell(25, 6, '&#x20B9;' . number_format($total_amount, 2), 1, 1, 'R'); // Total Amount value


// Invoice Amount in Words and Payment Mode
$amount_in_words = numberToWords($total_amount);
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);

// Invoice Amount in Words
$pdf->Cell(50, 8, 'Invoice Amount In Words:', 0, 0); // Adjust the width as needed
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(90, 8, $amount_in_words, 0, 0); // Adjust the width as needed

$pdf->Ln(7);
// Payment Mode
$currentY = $pdf->GetY();
$pdf->SetXY(120, $currentY); // Set the X position to the left as needed
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 8, 'Payment Mode:', 0, 0);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(30, 8, 'Credit', 0, 1); // Adjust the width as needed


    // Bank Details
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(120, $pdf->GetY()); // Move the text to the right side
$pdf->Cell(0, 10, 'Company\'s Bank Details:', 0, 1);
$pdf->SetFont('Arial', '', 10);

    // Create a box for bank details
    $bank_box_width = 80;
    $bank_box_height = 40;
    $pdf->SetXY(120, $pdf->GetY()); // Set X to 120 to move the box to the right side
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell($bank_box_width, $bank_box_height, '', 1, 0, 'L', true);

    // Position for the content inside the box
    $pdf->SetXY(125, $pdf->GetY() + 5); // Adjust the Y position to align within the box
    $pdf->MultiCell($bank_box_width - 5, 8, 'Bank Name: CANARA BANK', 0, 'L');
    $pdf->SetXY(125, $pdf->GetY());
    $pdf->MultiCell($bank_box_width - 5, 8, 'Bank Account No. : 05802200018637', 0, 'L');
    $pdf->SetXY(125, $pdf->GetY());
    $pdf->MultiCell($bank_box_width - 5, 8, 'Bank IFSC Code: CNRB0002763', 0, 'L');

    // Terms and Conditions
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 8, 'Terms and Conditions:', 0, 1);
    $pdf->SetFont('Arial', '', 8);
    $pdf->MultiCell(0, 8, 'Goods once sold cannot be taken back or exchanged. If payment is not made within the due date, interest @18% p.a. will be charged.');

    

    // Check available space on the first page
    $available_space = $pdf->GetPageHeight() - $pdf->GetY() - 30; // 30 units for bottom margin
    if ($available_space < 20) {
        // Not enough space, add a new page
        $pdf->AddPage();
    }

    // Authorized Signatory
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY($pdf->GetPageWidth() - 40, $pdf->GetY() + 20); // Add a small offset to place below the last printed element
    $pdf->Cell(0, 8, 'For, SRI RAM COMPUTERS', 0, 1, 'R');
    $pdf->Cell(0, 8, 'Authorized Signatory', 0, 1, 'R');

    // Output PDF to a file
    $pdf_filename = 'invoices/invoice_' . $next_invoice_number . '.pdf';
    if (!file_exists('invoices')) {
        mkdir('invoices', 0777, true);
    }
    $pdf->Output($pdf_filename, 'F');

    // Insert invoice details into the database
$stmt = $pdo->prepare("INSERT INTO billing (ename, item_name, amount, gst, quantity, pdf_path, gstin) VALUES (:ename, :item_name, :amount, :gst, :quantity, :pdf_filename, :gstin)");

// Loop through each item and insert into the database
for ($i = 0; $i < count($item_names); $i++) {
    $stmt->execute([
        ':ename' => $ename,
        ':item_name' => $item_names[$i],
        ':amount' => $amounts[$i],
        ':gst' => $gsts[$i],
        ':quantity' => $quantities[$i],
        ':pdf_filename' => $pdf_filename,
        ':gstin' => $buyer_gstin // Make sure $buyer_gstin is set correctly
    ]);
}

    // Redirect with success message
    echo "<script>
        alert('Invoice generated and saved successfully.');
        window.location.href = 'billing.php';
    </script>";
    exit;
}
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing</title>
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
        .billing-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            text-align: center;
        }
        .billing-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .billing-container input[type="text"],
        .billing-container input[type="number"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .billing-container input[type="button"],
        .billing-container input[type="submit"],
        .billing-container a {
            background-color: #008CBA;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 0 10px;
            text-decoration: none;
            display: inline-block;
        }
        .billing-container input[type="button"]:hover,
        .billing-container input[type="submit"]:hover,
        .billing-container a:hover {
            background-color: #007BB5;
        }
        .button-group {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="billing-container">
        <h2>Billing</h2>
        <form action="billing.php" method="post" id="billing-form">
            <input type="text" name="ename" placeholder="Customer Name" required><br>
            <input type="text" name="gstin" placeholder="GSTIN Number" required><br>
            <div id="item-container">
                <div class="item">
                    <input type="text" name="item_name[]" placeholder="Item Name" required><br>
                    <input type="text" name="amount[]" placeholder="Amount" required><br>
                    <input type="text" name="gst[]" placeholder="GST (%)" required><br>
                    <input type="number" name="quantity[]" placeholder="Quantity" required><br>
                    <input type="text" name="total[]" placeholder="Total" readonly><br>
                </div>
            </div>
            <div class="button-group">
                <input type="button" value="Add Item" onclick="addItem()">
                <input type="submit" value="Generate Invoice">
                <a href="index.php">Home</a>
            </div>
        </form>
    </div>
    <script>
    function addItem() {
        var itemContainer = document.getElementById('item-container');
        var newItem = document.createElement('div');
        newItem.classList.add('item');
        newItem.innerHTML = `
            <input type="text" name="item_name[]" placeholder="Item Name" required><br>
            <input type="text" name="amount[]" placeholder="Amount" required><br>
            <input type="text" name="gst[]" placeholder="GST (%)" required><br>
            <input type="number" name="quantity[]" placeholder="Quantity" required><br>
            <input type="text" name="total[]" placeholder="Total" readonly><br>
            <input type="button" value="Remove Item" class="remove-item">
        `;
        itemContainer.appendChild(newItem);
    }

    document.querySelector('form').addEventListener('input', function(event) {
        if (event.target.name.startsWith('amount') || event.target.name.startsWith('gst') || event.target.name.startsWith('quantity')) {
            var itemDiv = event.target.closest('.item');
            var amount = parseFloat(itemDiv.querySelector('input[name="amount[]"]').value) || 0;
            var gst = parseFloat(itemDiv.querySelector('input[name="gst[]"]').value) || 0;
            var quantity = parseFloat(itemDiv.querySelector('input[name="quantity[]"]').value) || 0;

            var total = (amount * quantity) * (1 + gst / 100);
            itemDiv.querySelector('input[name="total[]"]').value = total.toFixed(2);
        }
    });

    document.querySelector('form').addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-item')) {
            event.target.closest('.item').remove();
        }
    });

    document.getElementById('billing-form').addEventListener('submit', function(event) {
        var gstin = document.querySelector('input[name="gstin"]').value;
        var gstinPattern = /^[A-Z0-9]{15}$/;
        
        if (!gstinPattern.test(gstin)) {
            alert('Invalid GSTIN Number.');
            event.preventDefault();
        }
    });
    </script>
</body>
</html>
