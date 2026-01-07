<?php
// controllers/billingController.php
session_start();
require_once '../models/billingModel6_10.php';

// Auth Check (Cashier/Admin)
if (!isset($_SESSION['user'])) {
    header("Location: ../views/login6_10.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = isset($_POST['patient_id']) ? $_POST['patient_id'] : '';
    $services = isset($_POST['services']) ? $_POST['services'] : []; // Array of "Name|Price"
    $discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0;
    $vatPercent = isset($_POST['vat']) ? floatval($_POST['vat']) : 0;
    $payMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'Cash';

    if (!$patientId || empty($services)) {
        $_SESSION['bill_error'] = "Please select a patient and at least one service.";
        header("Location: ../views/billing/generateBill6_10.php");
        exit();
    }

    // Calculate Totals
    $subTotal = 0;
    $lineItems = [];

    foreach ($services as $srvStr) {
        // format matches value="Name|Price"
        $parts = explode('|', $srvStr);
        if (count($parts) === 2) {
            $name = $parts[0];
            $price = floatval($parts[1]);
            $subTotal += $price;
            $lineItems[] = ['name' => $name, 'price' => $price];
        }
    }

    $vatAmount = ($subTotal - $discount) * ($vatPercent / 100);
    if ($vatAmount < 0)
        $vatAmount = 0; // if discount > subtotal

    $grandTotal = ($subTotal - $discount) + $vatAmount;
    if ($grandTotal < 0)
        $grandTotal = 0;

    // Save to DB
    $billId = createBill((int) $patientId, $subTotal, $discount, $vatAmount, $grandTotal, $payMethod);

    if ($billId) {
        foreach ($lineItems as $item) {
            addBillItem($billId, $item['name'], $item['price']);
        }

        // Redirect to Print Invoice
        header("Location: ../views/invoicePrint6_10.php?id=" . $billId);
        exit();
    } else {
        $_SESSION['bill_error'] = "Failed to generate bill.";
        header("Location: ../views/generateBill6_10.php");
        exit();
    }
} else {
    // GET request
    header("Location: ../views/generateBill6_10.php");
}
