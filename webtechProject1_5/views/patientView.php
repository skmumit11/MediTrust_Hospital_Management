
<?php
require_once ('../models/patientModel.php');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$p = ($id > 0) ? getPatientById($id) : null;
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Patient View</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script></head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Patient #<?php echo $id; ?></h3>
  <?php if (!$p) { echo "Not found"; } else { ?>
    <p>Name: <?php echo htmlspecialchars($p['Name']); ?></p>
    <p>Age: <?php echo (int)$p['Age']; ?></p>
    <p>Gender: <?php echo htmlspecialchars($p['Gender']); ?></p>
    <p>Contact: <?php echo htmlspecialchars($p['Contact']); ?></p>
    <p>Address: <?php echo htmlspecialchars($p['Address']); ?></p>
    <p>Category: <?php echo htmlspecialchars($p['PatientCategory']); ?></p>
    <p>Linked Username: <?php echo htmlspecialchars($p['LinkedUsername'] ?? ''); ?></p>
  <?php } ?>
  <p><a class="btn btn-sm" href="admindashboard.php">Back</a></p>
</div>
</body></html>
