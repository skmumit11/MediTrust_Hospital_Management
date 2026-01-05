
<?php
require_once __DIR__ . '/../models/doctorModel.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$d = ($id > 0) ? getDoctorById($id) : null;
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Doctor View</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script></head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Doctor #<?php echo $id; ?></h3>
  <?php if (!$d) { echo "Not found"; } else { ?>
    <p>Name: <?php echo htmlspecialchars($d['Name']); ?></p>
    <p>Username: <?php echo htmlspecialchars($d['Username']); ?></p>
    <p>Specialty: <?php echo htmlspecialchars($d['Specialty']); ?></p>
    <p>Availability: <?php echo htmlspecialchars($d['Availability']); ?></p>
  <?php } ?>
  <p><a class="btn btn-sm" href="admindashboard.php">Back</a></p>
</div>
</body></html>
