<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "Please login first to access this page.";
    header("Location: login.php");
    exit;
}

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer to prevent SQL injection

    // Prepare SQL statement to fetch the PDF from the database
    $stmt = $pdo->prepare("SELECT pdf FROM billing WHERE id = ?");
    $stmt->execute([$id]);
    $pdf_data = $stmt->fetchColumn();

    if ($pdf_data) {
        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="billing_report_' . $id . '.pdf"');
        echo $pdf_data;
        exit;
    } else {
        echo "PDF not found.";
    }
} else {
    echo "Invalid request.";
}
?>
