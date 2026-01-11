<?php
// controllers/billingController6_10.php
session_start();
require_once '../models/billingModel6_10.php';

// Auth Check
if (!isset($_SESSION['username'])) {
    header("Location: ../views/login6_10.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = $_POST['patient_id'];
    $services = $_POST['services']; // Array of "Name|Price"
    $discount = $_POST['discount'];
    $vatPercent = $_POST['vat'];
    $payMethod = $_POST['payment_method'];

    if ($patientId == "" || count($services) == 0) {
        $_SESSION['bill_error'] = "Null value!";
        header("Location: ../views/generateBill6_10.php");
    } else {
        // Calculate Totals
        $subTotal = 0;
        $lineItems = [];

        foreach ($services as $srvStr) {
            // format matches value="Name|Price"
            $parts = explode('|', $srvStr);
            if (count($parts) === 2) {
                $name = $parts[0];
                $price = $parts[1]; // Implicit cast is fine in loose PHP, or basic cast
                $subTotal = $subTotal + $price;
                $lineItems[] = ['name' => $name, 'price' => $price];
            }
        }

        $vatAmount = ($subTotal - $discount) * ($vatPercent / 100);
        if ($vatAmount < 0)
            $vatAmount = 0;

        $grandTotal = ($subTotal - $discount) + $vatAmount;
        if ($grandTotal < 0)
            $grandTotal = 0;

        // Save to DB
        $billId = createBill($patientId, $subTotal, $discount, $vatAmount, $grandTotal, $payMethod);

        if ($billId > 0) {
            foreach ($lineItems as $item) {
                addBillItem($billId, $item['name'], $item['price']);
            }
            // Redirect to Print Invoice
            header("Location: ../views/invoicePrint6_10.php?id=" . $billId);
        } else {
            $_SESSION['bill_error'] = "Failed to generate bill.";
            header("Location: ../views/generateBill6_10.php");
        }
    }
} else {
    // GET request
    header("Location: ../views/generateBill6_10.php");
}
?>