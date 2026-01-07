
<?php
require_once ('../models/appointmentModel.php');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ap = ($id > 0) ? getAppointmentById($id) : null;
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Appointment View</title>
<link rel="stylesheet" href="../assets/style_layout.css"></head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Appointment #<?php echo $id; ?></h3>
  <?php if (!$ap) { echo "Not found"; } else { ?>
    <p>PatientID: <?php echo (int)$ap['PatientID']; ?></p>
    <p>DoctorID: <?php echo (int)$ap['DoctorID']; ?></p>
    <p>DepartmentID: <?php echo (int)$ap['DepartmentID']; ?></p>
    <p>Slot: <?php echo htmlspecialchars($ap['Slot']); ?></p>
    <p>Status: <?php echo htmlspecialchars($ap['Status']); ?></p>
  <?php } ?>
  <p><a class="btn btn-sm" href="admindashboard.php">Back</a></p>
</div>
</body></html>
