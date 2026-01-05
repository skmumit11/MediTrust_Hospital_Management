
<?php
require_once __DIR__ . '/../models/appointmentModel.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId    = (int)($_POST['PatientID'] ?? 0);
    $doctorId     = (int)($_POST['DoctorID'] ?? 0);
    $departmentId = (int)($_POST['DepartmentID'] ?? 0);
    $slot         = $_POST['Slot'] ?? '';
    $status       = $_POST['Status'] ?? 'Pending';
    addAppointment($patientId, $doctorId, $departmentId, $slot, $status, null);
    header('Location: admindashboard.php'); exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Add Appointment</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Add Appointment</h3>
  <form method="post">
    <input type="number" name="PatientID" placeholder="PatientID" required>
    <input type="number" name="DoctorID" placeholder="DoctorID" required>
    <input type="number" name="DepartmentID" placeholder="DepartmentID" required>
    <input type="datetime-local" name="Slot" required>
    <select name="Status">
      <option value="Pending">Pending</option>
      <option value="Confirmed">Confirmed</option>
      <option value="Completed">Completed</option>
      <option value="Cancelled">Cancelled</option>
    </select>
    <button type="submit" class="btn btn-sm">Create</button>
    <a class="btn btn-sm" href="admindashboard.php">Back</a>
  </form>
</div>
</body></html>
