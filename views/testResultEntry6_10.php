<?php
// views/lab/testResultEntry.php
session_start();
// require_once '../../middleware/accessControl.php'; // Removed as file is being deleted
// requireRole(['Lab', 'Doctor', 'Admin']); // Removed as per request

$errors = isset($_SESSION['lab_errors']) ? $_SESSION['lab_errors'] : [];
$success = isset($_SESSION['lab_success']) ? $_SESSION['lab_success'] : "";
$old = isset($_SESSION['lab_old']) ? $_SESSION['lab_old'] : [];

unset($_SESSION['lab_errors']);
unset($_SESSION['lab_success']);
unset($_SESSION['lab_old']);

$resultId = isset($_GET['result_id']) ? $_GET['result_id'] : "";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Test Result Entry</title>
    <link rel="stylesheet" href="../assets/style_admindashboard6_10.css">
    <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
    <link rel="stylesheet" href="../assets/style_lab6_10.css">
    <script src="../assets/sidebar6_10.js"></script>
</head>

<body>

    <?php include 'layoutAdmin6_10.php'; ?>

    <div class="main-content" style="margin-top: 80px;">
        <div class="container card">
            <h2>Test Result Entry <span class="role-badge">(Lab Staff / Doctor)</span></h2>

            <?php if (count($errors) > 0) { ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $e) { ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>

            <?php if ($success !== "") { ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php } ?>

            <form action="../controllers/labResultController6_10.php?action=save" method="post">
                <div class="form-group">
                    <label>Patient ID</label>
                    <input type="text" name="patient_id" class="form-control"
                        value="<?php echo isset($old['patient_id']) ? htmlspecialchars($old['patient_id']) : ""; ?>"
                        placeholder="Enter Patient ID" required>
                </div>

                <div class="form-group">
                    <label>Test Type</label>
                    <select name="test_type" class="form-control" required>
                        <option value="">-- Select Test Type --</option>
                        <option value="CBC" <?php echo (isset($old['test_type']) && $old['test_type'] == "CBC") ? "selected" : ""; ?>>CBC</option>
                        <option value="Blood Test" <?php echo (isset($old['test_type']) && $old['test_type'] == "Blood Test") ? "selected" : ""; ?>>Blood Test</option>
                        <option value="Urine Test" <?php echo (isset($old['test_type']) && $old['test_type'] == "Urine Test") ? "selected" : ""; ?>>Urine Test</option>
                        <option value="X-Ray" <?php echo (isset($old['test_type']) && $old['test_type'] == "X-Ray") ? "selected" : ""; ?>>X-Ray</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Result Details</label>
                    <textarea name="result" rows="6" class="form-control" placeholder="Enter findings..." required><?php
                    echo isset($old['result']) ? htmlspecialchars($old['result']) : "";
                    ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Result</button>
                    <!-- Show Download button only if we have a valid numeric Result ID -->
                    <?php if ($resultId !== "" && ctype_digit($resultId)) { ?>
                        <a href="downloadSingleResult6_10.php?result_id=<?php echo htmlspecialchars($resultId); ?>"
                            class="btn btn-secondary">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                    <?php } ?>
                </div>
            </form>

            <p class="note">
                <small>* “Download PDF” opens a print-friendly page. Use browser: <b>Print → Save as PDF</b>.</small>
            </p>
        </div>
    </div>

</body>

</html>