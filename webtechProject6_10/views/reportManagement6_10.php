<?php
// views/reportManagement6_10.php
session_start();
// No requires needed here as we use session data from controller
$rows = isset($_SESSION['lab_report_rows']) ? $_SESSION['lab_report_rows'] : [];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Report Management</title>
    <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
    <link rel="stylesheet" href="../assets/style_lab6_10.css">
    <script src="../assets/sidebar6_10.js"></script>
    <style>
        .report-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }

        .filter-bar {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-group label {
            display: block;
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }

        .filter-group input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn-filter {
            background: #3498db;
            color: white;
            border: none;
            padding: 9px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-download {
            float: right;
            background: #27ae60;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f1f2f6;
            color: #2c3e50;
        }
    </style>
</head>

<body>
    <?php include 'layoutAdmin6_10.php'; ?>

    <div class="main-content">
        <div class="container-fluid">

            <div class="report-card">
                <div style="overflow:hidden; margin-bottom:20px;">
                    <h2 style="float:left; margin:0; color:#2c3e50;">Lab Reports</h2>
                    <a href="../controllers/labReportController6_10.php?action=download" class="btn-download">Download
                        CSV</a>
                </div>

                <form action="../controllers/labReportController6_10.php" method="post" class="filter-bar">
                    <div class="filter-group">
                        <label>Date</label>
                        <input type="date" name="date_filter">
                    </div>
                    <div class="filter-group">
                        <label>Test Type</label>
                        <input type="text" name="test_type_filter" placeholder="e.g. CBC">
                    </div>
                    <div class="filter-group">
                        <label>Patient Name</label>
                        <input type="text" name="patient_filter" placeholder="Search name...">
                    </div>
                    <div class="filter-group">
                        <input type="submit" value="Filter Records" class="btn-filter">
                    </div>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient Name</th>
                            <th>Test Type</th>
                            <th>Date</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)) { ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding:20px; color:#888;">No reports found
                                    matching filters.</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($rows as $r) { ?>
                                <tr>
                                    <td><?= $r['ResultID'] ?></td>
                                    <td><?= $r['PatientName'] ?></td>
                                    <td><?= $r['TestType'] ?></td>
                                    <td><?= $r['Timestamp'] ?></td>
                                    <td><?= $r['Result'] ?></td>
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