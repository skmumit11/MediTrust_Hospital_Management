
<?php
require_once ('../models/bedModel.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['Type'] ?? 'General';
    $status = $_POST['Status'] ?? 'Available';
    addBed($type, $status);
    header('Location: admindashboard.php'); exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Add Bed</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script></head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Add Bed</h3>
  <form method="post">
    <select name="Type"><option>ICU</option><option selected>General</option></select>
    <select name="Status"><option selected>Available</option><option>Occupied</option></select>
    <button type="submit" class="btn btn-sm">Create</button>
    <a class="btn btn-sm" href="admindashboard.php">Back</a>
  </form>
</div>
</body></html>
