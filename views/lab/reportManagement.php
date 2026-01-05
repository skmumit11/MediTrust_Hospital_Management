<?php
// views/lab/reportManagement.php
session_start();
require_once __DIR__ . '/../../models/labModel.php';
// require_once '../../middleware/accessControl.php'; // Removed as file is being deleted
// requireRole(['Lab', 'Admin', 'Doctor']); // Removed as per request

$rows = isset($_SESSION['lab_report_rows']) ? $_SESSION['lab_report_rows'] : [];
$filtersText = isset($_SESSION['lab_report_filters']) ? $_SESSION['lab_report_filters'] : "";

$testTypes = getDistinctTestTypes();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Report Management</title>
    <link rel="stylesheet" href="../../assets/style_lab.css">
    <link rel="stylesheet" href="../../assets/style_layoutUser.css">
    <script src="../../assets/sidebar.js"></script>
</head>

<body>

    <?php include '../layoutAdmin_6_10.php'; ?>

    <div class="main-content" style="margin-top: 80px;">
        <div class="container card">
            <div class="header-flex">
                <h2>Report Management <span class="role-badge">(Lab / Doctor / Admin)</span></h2>
                <div class="header-actions">
                    <a href="../../controllers/labReportController.php?action=download" class="btn btn-outline">Download
                        CSV</a>
                    <a href="../../controllers/labReportController.php?action=print" class="btn btn-outline">Print
                        List</a>
                </div>
            </div>

            <form action="../../controllers/labReportController.php?action=view" method="get" class="filter-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date_filter" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Test Type</label>
                        <select name="test_type_filter" class="form-control">
                            <option value="">-- All --</option>
                            <?php foreach ($testTypes as $t) { ?>
                                <option value="<?php echo htmlspecialchars($t); ?>">
                                    <?php echo htmlspecialchars($t); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Patient (ID/Name)</label>
                        <input type="text" name="patient_filter" class="form-control" placeholder="Search patient...">
                    </div>

                    <div class="form-group align-end">
                        <button type="submit" class="btn btn-primary">View Report</button>
                    </div>
                </div>
            </form>

            <?php if ($filtersText !== "") { ?>
                <div class="filters-active">
                    <b>Active Filters:</b> <?php echo htmlspecialchars($filtersText); ?>
                </div>
            <?php } ?>

            <hr>

            <h3>Results (<?php echo count($rows); ?>)</h3>

            <div class="table-responsive">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Contact</th>
                            <th>Test Type</th>
                            <th>Date/Time</th>
                            <th>Result (Preview)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($rows) === 0) { ?>
                            <tr>
                                <td colspan="6" class="no-data">No results found.</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($rows as $r) { ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($r['ResultID']); ?></td>
                                    <td>
                                        <span class="patient-name"><?php echo htmlspecialchars($r['PatientName']); ?></span>
                                        <span class="patient-id">ID: <?php echo htmlspecialchars($r['PatientID']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($r['PatientContact']); ?></td>
                                    <td><span class="tag-test"><?php echo htmlspecialchars($r['TestType']); ?></span></td>
                                    <td><?php echo htmlspecialchars($r['Timestamp']); ?></td>
                                    <td>
                                        <div class="result-preview">
                                            <?php
                                            $preview = $r['Result'];
                                            if (strlen($preview) > 60) {
                                                $preview = substr($preview, 0, 60) . "...";
                                            }
                                            echo htmlspecialchars($preview);
                                            ?>
                                        </div>
                                        <a href="downloadSingleResult.php?result_id=<?php echo htmlspecialchars($r['ResultID']); ?>"
                                            class="link-action">
                                            Open / Print
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>