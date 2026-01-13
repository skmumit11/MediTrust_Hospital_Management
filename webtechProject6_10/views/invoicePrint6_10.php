<?php
// views/invoicePrint6_10.php
require_once '../models/billingModel6_10.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$bill = getBillResultById((int) $id);

if (!$bill) {
    die("Invoice not found.");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Invoice #<?= $bill['BillID'] ?></title>
    <link rel="stylesheet" href="../assets/style_simple6_10.css">
    <style>
        .invoice-box {
            border: 1px solid #ccc;
            padding: 20px;
            width: 600px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }

        .details {
            margin-bottom: 20px;
        }

        .totals {
            float: right;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <h1>MediTrust Hospital</h1>
            <p>Invoice #<?= str_pad($bill['BillID'], 6, '0', STR_PAD_LEFT) ?></p>
            <p>Date: <?= $bill['BillDate'] ?></p>
        </div>

        <div class="details">
            <strong>Bill To:</strong><br>
            <?= $bill['PatientName'] ?><br>
            ID: <?= $bill['PatientID'] ?><br>
            Contact: <?= $bill['Contact'] ?>
        </div>

        <table>
            <tr>
                <th>Service</th>
                <th>Price (BDT)</th>
            </tr>
            <?php foreach ($bill['Items'] as $item) { ?>
                <tr>
                    <td><?= $item['ServiceName'] ?></td>
                    <td><?= number_format($item['Price'], 2) ?></td>
                </tr>
            <?php } ?>
        </table>

        <div class="totals">
            <p>Subtotal: <?= number_format($bill['SubTotal'], 2) ?></p>
            <p>Discount: -<?= number_format($bill['Discount'], 2) ?></p>
            <p>VAT: +<?= number_format($bill['VAT'], 2) ?></p>
            <h3>Total: <?= number_format($bill['GrandTotal'], 2) ?></h3>
        </div>
        <div style="clear: both;"></div>

        <br>
        <button onclick="window.print()">Print</button>
        <a href="generateBill6_10.php">Back</a>
    </div>
</body>

</html>