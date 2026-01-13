<?php
// views/generateBill6_10.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../models/billingModel6_10.php';

// Reuse the simple fetching function we added to billingModel
$serviceList = getServiceList();
$patients = getPatientListSimple();

$error = isset($_SESSION['bill_error']) ? $_SESSION['bill_error'] : '';
unset($_SESSION['bill_error']);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Generate Bill</title>
    <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
    <link rel="stylesheet" href="../assets/style_lab6_10.css">
    <script src="../assets/sidebar6_10.js"></script>
    <style>
        .bill-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            max-width: 900px;
            margin: 20px auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-group select,
        .form-group input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .service-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #eee;
        }

        .total-section {
            background: #e8f5e9;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: right;
        }

        .btn-submit {
            background: #386D44;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }

        .btn-submit:hover {
            background: #2e5a36;
        }
    </style>
    <script>
        function validateForm() {
            let patient = document.getElementById('patient').value;
            let checks = document.querySelectorAll('input[name="services[]"]:checked');

            if (patient == "") {
                alert("Please select a patient!");
                return false;
            }
            if (checks.length == 0) {
                alert("Select at least one service!");
                return false;
            }
            return true;
        }

        function calculateTotal() {
            let subtotal = 0;
            const checkboxes = document.querySelectorAll('input[name="services[]"]:checked');

            checkboxes.forEach((cb) => {
                const parts = cb.value.split('|');
                if (parts.length === 2) {
                    subtotal += parseFloat(parts[1]);
                }
            });

            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const vatPercent = parseFloat(document.getElementById('vat').value) || 0;

            let taxable = subtotal - discount;
            if (taxable < 0) taxable = 0;

            const vatAmount = taxable * (vatPercent / 100);
            const grandTotal = taxable + vatAmount;

            document.getElementById('lblTotal').innerText = grandTotal.toFixed(2);
        }
    </script>
</head>

<body>
    <?php include 'layoutAdmin6_10.php'; ?>

    <div class="main-content">
        <div class="container-fluid">

            <div class="bill-card">
                <h2 style="color:#2c3e50; margin-top:0; border-bottom:2px solid #eee; padding-bottom:15px;">Generate New
                    Bill</h2>

                <?php if ($error)
                    echo "<div style='color:red; background:#ffebee; padding:10px; border-radius:6px; margin-bottom:15px;'>$error</div>"; ?>

                <form action="../controllers/billingController6_10.php?action=generate" method="post"
                    onsubmit="return validateForm()">

                    <div class="form-group">
                        <label>Select Patient:</label>
                        <select name="patient_id" id="patient">
                            <option value="">-- Choose Patient --</option>
                            <?php foreach ($patients as $p) { ?>
                                <option value="<?= $p['PatientID'] ?>"><?= $p['Name'] ?> (ID: <?= $p['PatientID'] ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Select Services:</label>
                        <div class="services-grid">
                            <?php foreach ($serviceList as $s) { ?>
                                <div class="service-item">
                                    <label
                                        style="display:flex; align-items:center; cursor:pointer; font-weight:normal; margin:0;">
                                        <input type="checkbox" name="services[]"
                                            value="<?= $s['ServiceID'] . '|' . $s['Price'] ?>" onclick="calculateTotal()"
                                            style="margin-right:10px;">
                                        <div>
                                            <div style="font-weight:600;"><?= $s['ServiceName'] ?></div>
                                            <div style="color:#666; font-size:0.9em;">$<?= number_format($s['Price'], 2) ?>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div style="display:flex; gap:20px;">
                        <div class="form-group" style="flex:1;">
                            <label>Discount Amount:</label>
                            <input type="number" step="0.01" name="discount" id="discount" value="0"
                                oninput="calculateTotal()">
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>VAT %:</label>
                            <input type="number" step="0.01" name="vat" id="vat" value="0" oninput="calculateTotal()">
                        </div>
                    </div>

                    <div class="total-section">
                        <span style="font-size:1.2em; color:#555; margin-right:10px;">Grand Total:</span>
                        <span style="font-size:2em; font-weight:bold; color:#2c3e50;">$<span
                                id="lblTotal">0.00</span></span>
                    </div>

                    <div style="text-align:right; margin-top:20px;">
                        <a href="admindashboard6_10.php"
                            style="margin-right:15px; color:#666; text-decoration:none;">Cancel</a>
                        <button type="submit" class="btn-submit">Generate Invoice</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</body>

</html>