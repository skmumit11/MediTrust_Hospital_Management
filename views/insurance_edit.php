<?php
// views/insurance_edit.php
session_start();
require_once __DIR__ . '/../models/insuranceModel.php';
require_once __DIR__ . '/../models/patientModel.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
if (!ctype_digit($id)) {
    header("Location: insurance_create.php");
    exit();
}

// Fetch existing data
$record = getInsuranceRecordById($id);
if (!$record) {
    // Record not found
    header("Location: insurance_create.php");
    exit();
}

$errors = isset($_SESSION['insurance_edit_errors']) ? $_SESSION['insurance_edit_errors'] : [];
unset($_SESSION['insurance_edit_errors']);

// Patient list for dropdown (reuse patientModel functionality if needed, 
// or simpler to just keep the Patient ID if we don't have a dropdown helper ready)
// Let's assume we can fetch patients for a nice dropdown
$patients = getPatientList();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Edit Insurance</title>
    <link rel="stylesheet" href="../assets/style_lab.css">
    <link rel="stylesheet" href="../assets/style_layoutUser.css">
    <script src="../assets/sidebar.js"></script>
</head>

<body>

    <?php include 'layoutAdmin_6_10.php'; ?>

    <div class="main-content" style="margin-top: 80px;">
        <div class="container card" style="max-width: 600px;">
            <h2>Edit Insurance Record #<?php echo htmlspecialchars($id); ?></h2>

            <?php if (count($errors) > 0) { ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $e) { ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>

            <form action="../controllers/insuranceUpdate.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                <div class="form-group">
                    <label>Patient</label>
                    <!-- If we have patients list, show select, else show text input -->
                    <select name="patient_id" class="form-control" required>
                        <?php foreach ($patients as $p) { ?>
                            <option value="<?php echo $p['PatientID']; ?>" <?php echo ($p['PatientID'] == $record['PatientID']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['Name'] . ' (ID: ' . $p['PatientID'] . ')'); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Insurance Company</label>
                    <input type="text" name="insurance_company" class="form-control"
                        value="<?php echo htmlspecialchars($record['Company']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Policy ID</label>
                    <input type="text" name="policy_id" class="form-control"
                        value="<?php echo htmlspecialchars($record['PolicyID']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Validity Date</label>
                    <input type="date" name="validity_date" class="form-control"
                        value="<?php echo htmlspecialchars($record['ValidityDate']); ?>" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Record</button>
                    <a href="insurance_create.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>