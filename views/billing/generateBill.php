<?php
// views/billing/generateBill.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../models/billingModel.php';
require_once __DIR__ . '/../../models/patientModel.php'; 
// require_once __DIR__ . '/../../middleware/accessControl.php'; // Removed as file is being deleted

// Role requirement removed as per request
// requireRole(['Cashier']);

// Initialize Data
$serviceList = getServiceList();
$patients = [];
// Fetch all patients for dropdown (this function exists in patientModel or similar? 
// Actually, earlier we used `getPatientList` in insuranceModel. Let's assume we can reuse that logic or duplicate it if needed.
// Checking patientModel.php content earlier: it had `searchPatients` and `getPatientById`.
// Checking insuranceModel.php: it had `getPatientList`. I'll assume we can use `insuranceModel` logic here or just query safely.
// For safety, I'll inline a simple patient fetch here or use insuranceModel's if accessible.
require_once __DIR__ . '/../../models/insuranceModel.php';
$patients = getPatientList();

$error = isset($_SESSION['bill_error']) ? $_SESSION['bill_error'] : '';
unset($_SESSION['bill_error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Generate Bill - Meditrust</title>
    <!-- Reuse Lab Styles for consistency -->
    <link rel="stylesheet" href="../../assets/style_layoutUser.css">
    <link rel="stylesheet" href="../../assets/style_lab.css">
    <script src="../../assets/sidebar.js"></script>
    <style>
        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .service-card {
            background: #f8f9fa;
            border: 1px solid #e1e4e8;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .service-card:hover {
            border-color: var(--primary);
            background: #eef2ff;
        }

        .service-card label {
            cursor: pointer;
            display: block;
            width: 100%;
        }

        .calc-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .calc-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 20px;
        }

        body {
            padding-bottom: 50px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin 0.3s;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar Include (Using relative path fix) -->
    <?php include '../layoutAdmin_6_10.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="card">
                <h2>Generate Bill <span class="role-badge">(Cashier)</span></h2>

                <?php if ($error) { ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php } ?>

                <form action="../../controllers/billingController.php" method="post">
                    <!-- Patient Selector -->
                    <div class="form-group">
                        <label>Select Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <option value="">-- Choose Patient --</option>
                            <?php foreach ($patients as $p) { ?>
                                <option value="<?php echo $p['PatientID']; ?>">
                                    <?php echo htmlspecialchars($p['Name']); ?> (ID:
                                    <?php echo $p['PatientID']; ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Services List -->
                    <div class="form-group">
                        <label>Select Services</label>
                        <div class="service-grid">
                            <?php foreach ($serviceList as $idx => $svc) { ?>
                                <div class="service-card">
                                    <label>
                                        <input type="checkbox" name="services[]"
                                            value="<?php echo htmlspecialchars($svc['name'] . '|' . $svc['price']); ?>"
                                            onchange="calculateTotal()">
                                        <strong>
                                            <?php echo htmlspecialchars($svc['name']); ?>
                                        </strong><br>
                                        <span class="text-muted">৳
                                            <?php echo number_format($svc['price'], 2); ?>
                                        </span>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Calculation Section -->
                    <div class="calc-box">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Discount Amount (৳)</label>
                                <input type="number" name="discount" id="discount" class="form-control" value="0"
                                    step="0.01" oninput="calculateTotal()">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>VAT Percentage (%)</label>
                                <input type="number" name="vat" id="vat" class="form-control" value="15" step="1"
                                    oninput="calculateTotal()">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option>Cash</option>
                                <option>Credit Card</option>
                                <option>Mobile Money</option>
                                <option>Insurance</option>
                            </select>
                        </div>

                        <hr>
                        <div class="calc-row"><span>Subtotal:</span> <strong id="lblSubtotal">৳0.00</strong></div>
                        <div class="calc-row"><span>Discount:</span> <span id="lblDiscount"
                                class="text-danger">-৳0.00</span></div>
                        <div class="calc-row"><span>VAT:</span> <span id="lblVat">৳0.00</span></div>
                        <div class="calc-row" style="font-size: 1.2em;"><span>Grand Total:</span> <strong id="lblTotal"
                                class="text-success">৳0.00</strong></div>
                    </div>

                    <div class="form-actions" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.1em;">Print
                            Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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

            document.getElementById('lblSubtotal').innerText = '৳' + subtotal.toFixed(2);
            document.getElementById('lblDiscount').innerText = '-৳' + discount.toFixed(2);
            document.getElementById('lblVat').innerText = '৳' + vatAmount.toFixed(2);
            document.getElementById('lblTotal').innerText = '৳' + grandTotal.toFixed(2);
        }
    </script>

</body>

</html>