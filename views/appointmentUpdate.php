
<?php
require_once __DIR__ . '/../models/appointmentModel.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ap = ($id > 0) ? getAppointmentById($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ap) {
    $patientId    = (int)($_POST['PatientID'] ?? $ap['PatientID']);
    $doctorId     = (int)($_POST['DoctorID'] ?? $ap['DoctorID']);
    $departmentId = (int)($_POST['DepartmentID'] ?? $ap['DepartmentID']);
    $slot         = $_POST['Slot'] ?? $ap['Slot'];
    $status       = $_POST['Status'] ?? $ap['Status'];
    updateAppointment($id, $patientId, $doctorId, $departmentId, $slot, $status);
    header('Location: admindashboard.php'); exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Update Appointment</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Update Appointment #<?php echo $id; ?></h3>
  <?php if (!$ap) { echo "Not found"; } else { ?>
  <form method="post">
    <input type="number" name="PatientID" value="<?php echo (int)$ap['PatientID']; ?>">
    <input type="number" name="DoctorID" value="<?php echo (int)$ap['DoctorID']; ?>">
    <input type="number" name="DepartmentID" value="<?php echo (int)$ap['DepartmentID']; ?>">
    <input type="datetime-local" name="Slot" value="<?php echo date('Y-m-d\TH:i', strtotime($ap['Slot'])); ?>">
    <select name="Status">
      <option <?php echo ($ap['Status']=='Pending')?'selected':''; ?> value="Pending">Pending</option>
      <option <?php echo ($ap['Status']=='Confirmed')?'selected':''; ?> value="Confirmed">Confirmed</option>
      <option <?php echo ($ap['Status']=='Completed')?'selected':''; ?> value="Completed">Completed</option>
      <option <?php echo ($ap['Status']=='Cancelled')?'selected':''; ?> value="Cancelled">Cancelled</option>
    </select>
    <button type="submit" class="btn btn-sm">Update</button>
    <a class="btn btn-sm" href="admindashboard.php">Back</a>
  </form>
  <?php } ?>
</div>
</body></html>
