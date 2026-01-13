<?php
// views/lab/downloadSingleResult.php
session_start();
require_once '../models/labModel6_10.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login6_10.php");
    exit();
}

$resultId = isset($_GET['result_id']) ? $_GET['result_id'] : "";
if ($resultId === "" || !ctype_digit($resultId)) {
    echo "Invalid Result ID";
    exit();
}

$row = getLabResultById((int) $resultId);
if (!$row) {
    echo "Result not found";
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Lab Result #<?php echo htmlspecialchars($row['ResultID']); ?></title>
    <style>
        /* Base Setup */
        body {
            background-color: #525659;
            /* Browser PDF viewer background look */
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', serif;
        }

        /* A4 Paper */
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            border: 1px solid #d3d3d3;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* Printing Rules */
        @media print {
            body {
                background: white;
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        /* Header */
        .header {
            border-bottom: 2px solid #008080;
            padding-bottom: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .header h1 {
            color: #008080;
            margin: 0;
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p {
            margin: 0;
            color: #555;
            font-size: 13px;
        }

        .meta-info {
            text-align: right;
        }

        /* Patient Grid */
        .patient-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .info-group label {
            display: block;
            color: #008080;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-group span {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        /* Result Section */
        .result-section h3 {
            background: #008080;
            color: white;
            padding: 8px 12px;
            margin-bottom: 20px;
            font-size: 16px;
            text-transform: uppercase;
        }

        .result-content {
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            line-height: 1.6;
            font-size: 14px;
            color: #333;
            min-height: 200px;
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #777;
        }

        .signature {
            text-align: center;
            margin-top: 40px;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-weight: bold;
        }

        /* Controls */
        .controls {
            text-align: center;
            margin-bottom: 20px;
            padding: 20px;
        }

        .btn-print {
            background: #008080;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn-print:hover {
            background: #005f5f;
        }
    </style>
</head>

<body>

    <div class="controls no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print Report / Save as PDF</button>
    </div>

    <div class="page">
        <div class="header">
            <div>
                <h1>MediTrust Hospital</h1>
                <p>123 Health Avenue, Medical District</p>
                <p>Phone: +880 123 456 7890</p>
            </div>
            <div class="meta-info">
                <p><strong>Report Date:</strong> <?php echo date("d M Y"); ?></p>
                <p><strong>Result ID:</strong> #<?php echo htmlspecialchars($row['ResultID']); ?></p>
            </div>
        </div>

        <div class="patient-grid">
            <div class="info-group">
                <label>Patient Name</label>
                <span><?php echo htmlspecialchars($row['PatientName']); ?></span>
            </div>
            <div class="info-group">
                <label>Patient ID</label>
                <span><?php echo htmlspecialchars($row['PatientID']); ?></span>
            </div>
            <div class="info-group">
                <label>Contact Info</label>
                <span><?php echo htmlspecialchars($row['PatientContact']); ?></span>
            </div>
            <div class="info-group">
                <label>Test Type</label>
                <span><?php echo htmlspecialchars($row['TestType']); ?></span>
            </div>
        </div>

        <div class="result-section">
            <h3>Clinical Findings / Results</h3>
            <div class="result-content"><?php echo htmlspecialchars($row['Result']); ?></div>
        </div>

        <div class="footer">
            <div>
                <p>Generated by: MediTrust Lab System</p>
                <p>This report is electronically generated.</p>
            </div>
            <div class="signature">
                <div class="signature-line">Authorized Signature</div>
            </div>
        </div>
    </div>

</body>

</html>