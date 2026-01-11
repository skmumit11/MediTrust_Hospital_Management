<?php
// views/testResultEntry6_10.php
session_start();

$errors = isset($_SESSION['lab_errors']) ? $_SESSION['lab_errors'] : [];
$success = isset($_SESSION['lab_success']) ? $_SESSION['lab_success'] : "";
unset($_SESSION['lab_errors']);
unset($_SESSION['lab_success']);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Test Result Entry</title>
    <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
    <link rel="stylesheet" href="../assets/style_lab6_10.css">
    <script src="../assets/sidebar6_10.js"></script>
    <style>
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            max-width: 600px;
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
        }

        .btn-submit {
            background: #386D44;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
        }

        .btn-submit:hover {
            background: #2e5a36;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #7f8c8d;
            text-decoration: none;
        }
    </style>
    <script>
        function validateForm() {
            let pid = document.getElementById('pid').value;
            let type = document.getElementById('type').value;
            let res = document.getElementById('res').value;

            if (pid == "" || type == "" || res == "") {
                alert("Null value!");
                return false;
            }
            if (isNaN(pid)) {
                alert("Invalid ID!");
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <?php include 'layoutAdmin6_10.php'; ?>

    <div class="main-content">
        <div class="container-fluid">

            <div class="form-card">
                <h2 style="text-align:center; color:#2c3e50; margin-top:0;">Lab Test Result Entry</h2>

                <?php
                if (!empty($errors)) {
                    foreach ($errors as $e)
                        echo "<p style='color:red; text-align:center;'>{$e}</p>";
                }
                if ($success != "")
                    echo "<p style='color:green; text-align:center;'>{$success}</p>";
                ?>

                <form action="../controllers/labResultController6_10.php?action=save" method="post"
                    onsubmit="return validateForm()">
                    <div class="form-group">
                        <label>Patient ID:</label>
                        <input type="text" name="patient_id" id="pid" placeholder="Enter Patient ID">
                    </div>

                    <div class="form-group">
                        <label>Test Type:</label>
                        <select name="test_type" id="type">
                            <option value="">-- Select Test --</option>
                            <option value="CBC">CBC</option>
                            <option value="Blood Test">Blood Test</option>
                            <option value="Urine Test">Urine Test</option>
                            <option value="X-Ray">X-Ray</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Result / Findings:</label>
                        <textarea name="result" id="res" rows="5" placeholder="Enter detailed results..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Save Result</button>
                    <a href="admindashboard6_10.php" class="back-link">Back to Dashboard</a>
                </form>
            </div>

        </div>
    </div>
</body>

</html>