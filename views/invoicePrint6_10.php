<?php
// views/billing/invoicePrint.php
// No sidebar, pure invoice layout
require_once '../models/billingModel6_10.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$bill = getBillResultById((int) $id);

if (!$bill) {
    die("Invoice not found.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #
        <?php echo $bill['BillID']; ?> - Meditrust
    </title>
    <link rel="stylesheet" href="../assets/style_admindashboard6_10.css">
    <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
    <link rel="stylesheet" href="../assets/style_lab6_10.css">
    <script src="../assets/sidebar6_10.js"></script>
    <style>
        body {
            background: #525659;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .invoice-box {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .company-info h1 {
            margin: 0;
            color: var(--primary);
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details h2 {
            margin: 0;
            color: #333;
        }

        .bill-to {
            margin-bottom: 30px;
        }

        .bill-to h3 {
            margin: 0 0 10px 0;
            color: var(--primary);
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .invoice-table th {
            background: #f8f9fa;
            border-bottom: 2px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .invoice-table td {
            border-bottom: 1px solid #eee;
            padding: 12px;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            width: 300px;
            margin-left: auto;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .totals-row.grand {
            font-size: 1.2em;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 5px;
        }

        .stamp {
            position: absolute;
            top: 200px;
            right: 100px;
            border: 3px solid #28a745;
            color: #28a745;
            font-weight: bold;
            font-size: 2em;
            padding: 10px 20px;
            transform: rotate(-15deg);
            opacity: 0.8;
            border-radius: 10px;
        }

        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 30px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            font-size: 1.1em;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .invoice-box {
                box-shadow: none;
                padding: 0;
            }

            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="invoice-box">

        <div class="stamp">PAID</div>

        <div class="header">
            <div class="company-info">
                <h1>MediTrust Hospital</h1>
                <p>123 Healthcare Ave, Medical District<br>Dhaka, Bangladesh</p>
            </div>
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <p><strong>Invoice #:</strong>
                    <?php echo str_pad($bill['BillID'], 6, '0', STR_PAD_LEFT); ?>
                </p>
                <p><strong>Date:</strong>
                    <?php echo date('M d, Y', strtotime($bill['BillDate'])); ?>
                </p>
                <p><strong>Payment:</strong>
                    <?php echo htmlspecialchars($bill['PaymentMethod']); ?>
                </p>
            </div>
        </div>

        <div class="bill-to">
            <h3>Bill To:</h3>
            <p><strong>
                    <?php echo htmlspecialchars($bill['PatientName']); ?>
                </strong></p>
            <p>Patient ID:
                <?php echo $bill['PatientID']; ?>
            </p>
            <p>Contact:
                <?php echo htmlspecialchars($bill['Contact']); ?>
            </p>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Service / Description</th>
                    <th class="text-right">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bill['Items'] as $item) { ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($item['ServiceName']); ?>
                        </td>
                        <td class="text-right">৳
                            <?php echo number_format($item['Price'], 2); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>৳
                    <?php echo number_format($bill['SubTotal'], 2); ?>
                </span>
            </div>
            <div class="totals-row">
                <span>Discount:</span>
                <span>-৳
                    <?php echo number_format($bill['Discount'], 2); ?>
                </span>
            </div>
            <div class="totals-row">
                <span>VAT:</span>
                <span>+৳
                    <?php echo number_format($bill['VAT'], 2); ?>
                </span>
            </div>
            <div class="totals-row grand">
                <span>Total Paid:</span>
                <span>৳
                    <?php echo number_format($bill['GrandTotal'], 2); ?>
                </span>
            </div>
        </div>

        <br><br><br>
        <p style="text-align: center; color: #777; font-size: 0.9em;">
            Thank you for choosing MediTrust Hospital.<br>
            This is a computer generated invoice and does not require signature.
        </p>

    </div>

    <button class="print-btn" onclick="window.print()">Print Invoice</button>

</body>

</html>