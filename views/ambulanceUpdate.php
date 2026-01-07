
<?php
require_once ('../models/ambulanceModel.php');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['Status'] ?? '';
    if ($id > 0 && $status !== '') { updateAmbulanceStatus($id, $status); }
    header('Location: admindashboard.php'); exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Update Ambulance</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Update Ambulance Request #<?php echo $id; ?></h3>
  <form method="post">
    <label>Status:</label>
    <select name="Status">
      <option value="Pending">Pending</option>
      <option value="Accepted">Accepted</option>
      <option value="Dispatched">Dispatched</option>
      <option value="Completed">Completed</option>
    </select>
    <button type="submit" class="btn btn-sm">Update</button>
    <a class="btn btn-sm" href="admindashboard.php">Back</a>
  </form>
</div>
</body></html>
