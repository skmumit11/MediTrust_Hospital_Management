
<?php
require_once __DIR__ . '/../models/patientModel.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId   = $_POST['UserID'] ?? null;
    $name     = $_POST['Name'] ?? '';
    $age      = (int)($_POST['Age'] ?? 0);
    $gender   = $_POST['Gender'] ?? 'Other';
    $contact  = $_POST['Contact'] ?? '';
    $address  = $_POST['Address'] ?? '';
    $category = $_POST['PatientCategory'] ?? 'Unknown';
    $notes    = $_POST['Notes'] ?? '';
    addPatient($userId, $name, $age, $gender, $contact, $address, $category, $notes);
    header('Location: admindashboard.php'); exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Add Patient</title>
<link rel="stylesheet" href="../assets/style_layout.css"></head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Add Patient</h3>
  <form method="post">
    <input type="number" name="UserID" placeholder="Linked UserID (optional)">
    <input type="text" name="Name" placeholder="Name" required>
    <input type="number" name="Age" placeholder="Age" required>
    <select name="Gender"><option>Male</option><option>Female</option><option>Other</option></select>
    <input type="text" name="Contact" placeholder="Contact">
    <input type="text" name="Address" placeholder="Address">
    <select name="PatientCategory">
      <option>OPD</option><option>IPD</option><option>Emergency</option><option selected>Unknown</option>
    </select>
    <input type="text" name="Notes" placeholder="Notes">
    <button type="submit" class="btn btn-sm">Create</button>
    <a class="btn btn-sm" href="admindashboard.php">Back</a>
  </form>
</div>
</body></html>
