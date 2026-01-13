
<?php
// views/lab/downloadReportCsv.php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$rows = isset($_SESSION['lab_report_rows']) ? $_SESSION['lab_report_rows'] : [];
$filters = isset($_SESSION['lab_report_filters']) ? $_SESSION['lab_report_filters'] : "";

$filename = "lab_report_" . date("Ymd_His") . ".csv";

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=" . $filename);

$output = fopen("php://output", "w");

fputcsv($output, ["Lab Report"]);
fputcsv($output, ["Filters", $filters]);
fputcsv($output, ["Generated At", date("Y-m-d H:i:s")]);
fputcsv($output, [""]);
fputcsv($output, ["ResultID", "PatientID", "PatientName", "Contact", "TestType", "Timestamp", "Result"]);

foreach ($rows as $r) {
    fputcsv($output, [
        $r['ResultID'],
        $r['PatientID'],
        $r['PatientName'],
        $r['PatientContact'],
        $r['TestType'],
        $r['Timestamp'],
        $r['Result']
    ]);
}

fclose($output);
exit();
