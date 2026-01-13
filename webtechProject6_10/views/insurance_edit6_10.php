<?php
// views/insurance_edit6_10.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../models/insuranceModel6_10.php';
$id = $_GET['id'];
$record = getInsuranceRecordById($id);
$patients = getPatientList();

if (!$record) {
    echo "Record not found";
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Insurance</title>
    <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
    <link rel="stylesheet" href="../assets/style_lab6_10.css">
    <script src="../assets/sidebar6_10.js"></script>
    <style>
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            max-width: 800px;
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

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .btn-submit {
            background: #386D44;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }
    </style>
    <script>
        function validateForm() {
            let pid = document.getElementById('patient').value;
            let company = document.getElementById('company').value;
            let policy = document.getElementById('policy').value;
            let vdate = document.getElementById('vdate').value;

            if (pid == "") { alert("Select a patient"); return false; }
            if (company == "") { alert("Enter company name"); return false; }
            if (policy == "") { alert("Enter policy ID"); return false; }
            if (vdate == "") { alert("Select validity date"); return false; }
            return true;
        }
    </script>
</head>

<body>
    <?php include 'layoutAdmin6_10.php'; ?>

    <div class="main-content">
        <div class="container-fluid">

            <div class="form-card">
                <h2 style="color:#2c3e50; margin-top:0;">Edit Insurance Record</h2>

                <form action="../controllers/insuranceUpdate6_10.php" method="post" onsubmit="return validateForm()">
                    <input type="hidden" name="id" value="<?= $record['InsuranceID'] ?>">

                    <div class="form-group">
                        <label>Patient</label>
                        <select name="patient_id" id="patient">
                            <?php foreach ($patients as $p) {
                                $sel = ($p['PatientID'] == $record['PatientID']) ? 'selected' : '';
                                ?>
                                <option value="<?= $p['PatientID'] ?>" <?= $sel ?>><?= $p['Name'] ?> (ID:
                                    <?= $p['PatientID'] ?>)</option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Insurance Company</label>
                        <input type="text" name="insurance_company" id="company" value="<?= $record['Company'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Policy ID</label>
                        <input type="text" name="policy_id" id="policy" value="<?= $record['PolicyID'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Validity Date</label>
                        <input type="date" name="validity_date" id="vdate" value="<?= $record['ValidityDate'] ?>">
                    </div>

                    <div style="margin-top:20px;">
                        <input type="submit" value="Update Record" class="btn-submit">
                        <a href="insurance_create6_10.php"
                            style="margin-left:15px; color:#555; text-decoration:none;">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</body>

</html>