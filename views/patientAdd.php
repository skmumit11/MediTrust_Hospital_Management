
<?php
require_once  '../models/patientModel.php';
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
<style>
  input{  padding: 10px;
  border-radius: 8px;
  border: 1px solid #bdbdbd;
  outline:none;
  background: #fff;}
</style>
<link rel="stylesheet" href="../assets/style_layout.css">
<link rel="stylesheet" href="../assets/style_admindashboard.css">
<script src="../assets/sidebar.js"></script>
</head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Add Patient</h3>
  <form method="post">
    <input type="number" name="UserID" placeholder="Linked UserID (optional)"><br>
    <input type="text" name="Name" placeholder="Name" required><br>
    <input type="number" name="Age" placeholder="Age" required><br>
    <select name="Gender"><option>Male</option><option>Female</option><option>Other</option></select><br>
    <input type="text" name="Contact" placeholder="Contact"><br>
    <input type="text" name="Address" placeholder="Address"><br>
    <select name="PatientCategory"> <br>
      <option>OPD</option><option>IPD</option><option>Emergency</option><option selected>Unknown</option>
    </select><br>
    <input type="text" name="Notes" placeholder="Notes"><br>
    <button type="submit" class="btn btn-sm">Create</button><br>      
    <a class="btn btn-sm" href="admindashboard.php">Back</a>
  </form>
</div>
</body></html>
