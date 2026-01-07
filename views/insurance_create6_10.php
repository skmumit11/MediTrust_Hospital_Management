<?php
// views/insurance_create.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent "undefined variable" warnings if opened directly
if (!isset($patients) || !is_array($patients)) {
    require_once '../models/insuranceModel6_10.php';
    $patients = getPatientList();
    $records = getAllInsuranceRecords();
}

if (!isset($records)) {
    $records = [];
}

$errors = isset($_SESSION['insurance_errors']) ? $_SESSION['insurance_errors'] : [];
$success = isset($_SESSION['insurance_success']) ? $_SESSION['insurance_success'] : '';
$old = isset($_SESSION['insurance_old']) ? $_SESSION['insurance_old'] : [];

// Clear flash messages
unset($_SESSION['insurance_errors']);
unset($_SESSION['insurance_success']);
unset($_SESSION['insurance_old']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Insurance Record Entry - Meditrust</title>
    <!-- Admin Sidebar Styles -->
    <link rel="stylesheet" href="../assets/style_admindashboard6_10.css">
    <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
    <link rel="stylesheet" href="../assets/style_lab6_10.css">
    <script src="../assets/sidebar6_10.js"></script>

    <style>
        /* Specific override to fix sidebar overlap if needed */
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

    <!-- Include Sidebar -->
    <?php include 'layoutAdmin6_10.php'; ?>

    <div class="main-content">
        <div class="container">

            <div class="card">
                <h2>Insurance Entry <span class="role-badge">(Admin)</span></h2>

                <?php if (!empty($success)) { ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php } ?>

                <?php if (!empty($errors)) { ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $e) { ?>
                                <li><?php echo htmlspecialchars($e); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <form action="../controllers/insuranceCreate6_10.php" method="post">
                    <div class="form-group">
                        <label>Select Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <option value="">-- Choose Patient --</option>
                            <?php foreach ($patients as $p) {
                                $sel = (isset($old['patient_id']) && (string) $p['PatientID'] === (string) $old['patient_id']) ? "selected" : "";
                                ?>
                                <option value="<?php echo (int) $p['PatientID']; ?>" <?php echo $sel; ?>>
                                    <?php echo htmlspecialchars($p['Name']); ?> (ID: <?php echo $p['PatientID']; ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Insurance Company</label>
                        <input type="text" name="insurance_company" class="form-control"
                            placeholder="e.g. Allianz, MetLife"
                            value="<?php echo isset($old['insurance_company']) ? htmlspecialchars($old['insurance_company']) : ''; ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Policy ID</label>
                        <input type="text" name="policy_id" class="form-control" placeholder="Policy Number"
                            value="<?php echo isset($old['policy_id']) ? htmlspecialchars($old['policy_id']) : ''; ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Validity Date</label>
                        <input type="date" name="validity_date" class="form-control"
                            value="<?php echo isset($old['validity_date']) ? htmlspecialchars($old['validity_date']) : ''; ?>"
                            required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            Save Record
                        </button>
                        <a href="admindashboard6_10.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

            <br><br>

            <div class="card">
                <h3>Recent Insurance Records</h3>
                <div class="table-responsive">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Company</th>
                                <th>Policy ID</th>
                                <th>Validity</th>
                                <th style="width: 150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($records)) { ?>
                                <tr>
                                    <td colspan="5" class="no-data">No records found.</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($records as $r) { ?>
                                    <tr>
                                        <td>
                                            <span class="patient-name"><?php echo htmlspecialchars($r['PatientName']); ?></span>
                                            <span class="patient-id">ID: <?php echo htmlspecialchars($r['PatientID']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($r['Company']); ?></td>
                                        <td><span class="tag-test"><?php echo htmlspecialchars($r['PolicyID']); ?></span></td>
                                        <td><?php echo htmlspecialchars($r['ValidityDate']); ?></td>
                                        <td>
                                            <a href="insurance_edit6_10.php?id=<?php echo $r['InsuranceID']; ?>" class="link-action"
                                                style="color: var(--accent); margin-right: 10px;">Edit</a>
                                            <a href="../controllers/insuranceDelete6_10.php?id=<?php echo $r['InsuranceID']; ?>"
                                                class="link-action" style="color: var(--danger);"
                                                onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</body>

</html>