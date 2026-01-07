
<?php
require_once  '../models/doctorModel.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$d = ($id > 0) ? getDoctorById($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $d) {
    $specialty = $_POST['Specialty'] ?? $d['Specialty'];
    $availability = $_POST['Availability'] ?? $d['Availability'];
    updateDoctor($id, $specialty, $availability);
    header('Location: admindashboard.php'); exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Update Doctor</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script></head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Update Doctor #<?php echo $id; ?></h3>
  <?php if (!$d) { echo "Not found"; } else { ?>
  <form method="post">
    <input type="text" name="Specialty" value="<?php echo htmlspecialchars($d['Specialty']); ?>">
    <input type="text" name="Availability" value="<?php echo htmlspecialchars($d['Availability']); ?>">
    <button type="submit" class="btn btn-sm">Update</button>
    <a class="btn btn-sm" href="admindashboard.php">Back</a>
  </form>
  <?php } ?>
</div>
</body></html>
