
<?php
// views/lab/printReport.php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$rows = isset($_SESSION['lab_report_rows']) ? $_SESSION['lab_report_rows'] : [];
$filters = isset($_SESSION['lab_report_filters']) ? $_SESSION['lab_report_filters'] : "";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Lab Report</title>
    ../../assets/style_lab.css
    <style>
        @media print { .no-print { display:none; } }
        .paper { width: 1000px; margin: 0 auto; background:#fff; padding: 20px; }
    </style>
</head>
<body>
<div class="paper">
    <div class="no-print" style="margin-bottom:10px;">
        <button onclick="window.print()">Print / Save as PDF</button>
    </div>

    <h2>Lab Report</h2>
    <p><b>Filters:</b> <?php echo htmlspecialchars($filters); ?></p>
    <p><b>Generated At:</b> <?php echo htmlspecialchars(date("Y-m-d H:i:s")); ?></p>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>ResultID</th>
                <th>Patient</th>
                <th>Contact</th>
                <th>Test Type</th>
                <th>Date/Time</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($rows) === 0) { ?>
            <tr><td colspan="6">No results found.</td></tr>
        <?php } else { ?>
            <?php foreach ($rows as $r) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['ResultID']); ?></td>
                    <td><?php echo htmlspecialchars($r['PatientName']); ?> (<?php echo htmlspecialchars($r['PatientID']); ?>)</td>
                    <td><?php echo htmlspecialchars($r['PatientContact']); ?></td>
                    <td><?php echo htmlspecialchars($r['TestType']); ?></td>
                    <td><?php echo htmlspecialchars($r['Timestamp']); ?></td>
                    <td style="white-space:pre-wrap;"><?php echo htmlspecialchars($r['Result']); ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
