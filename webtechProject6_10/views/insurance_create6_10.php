<?php
// views/insurance_create6_10.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../models/insuranceModel6_10.php';
$patients = getPatientList();
$records = getAllInsuranceRecords();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Insurance Entry</title>
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

        .list-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f8f9fa;
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
                <h2 style="color:#2c3e50; margin-top:0;">Insurance Entry <span
                        style="font-size:0.6em; background:#e0f2f1; color:#00695c; padding:2px 8px; border-radius:10px;">ADMIN</span>
                </h2>

                <form action="../controllers/insuranceCreate6_10.php" method="post" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label>Select Patient</label>
                        <select name="patient_id" id="patient">
                            <option value="">-- Choose Patient --</option>
                            <?php foreach ($patients as $p) { ?>
                                <option value="<?= $p['PatientID'] ?>"><?= $p['Name'] ?> (ID: <?= $p['PatientID'] ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Insurance Company</label>
                        <input type="text" name="insurance_company" id="company" placeholder="e.g. Allianz, MetLife">
                    </div>

                    <div class="form-group">
                        <label>Policy ID</label>
                        <input type="text" name="policy_id" id="policy" placeholder="Policy Number">
                    </div>

                    <div class="form-group">
                        <label>Validity Date</label>
                        <input type="date" name="validity_date" id="vdate">
                    </div>

                    <div style="margin-top:20px;">
                        <input type="submit" value="Save Record" class="btn-submit">
                        <a href="admindashboard6_10.php"
                            style="margin-left:15px; color:#555; text-decoration:none;">Cancel</a>
                    </div>
                </form>
            </div>

            <div class="list-card">
                <h3 style="margin-top:0;">Existing Records</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Company</th>
                            <th>Policy ID</th>
                            <th>Validity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r) { ?>
                            <tr>
                                <td><?= $r['PatientName'] ?></td>
                                <td><?= $r['Company'] ?></td>
                                <td><?= $r['PolicyID'] ?></td>
                                <td><?= $r['ValidityDate'] ?></td>
                                <td>
                                    <a href="insurance_edit6_10.php?id=<?= $r['InsuranceID'] ?>"
                                        style="color:#3498db; margin-right:10px;">Edit</a>
                                    <a href="../controllers/insuranceDelete6_10.php?id=<?= $r['InsuranceID'] ?>"
                                        style="color:#e74c3c;" onclick="return confirm('Delete?');">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</body>

</html>