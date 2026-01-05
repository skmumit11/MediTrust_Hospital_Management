
<?php
require_once __DIR__ . '/../models/patientModel.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$p = ($id > 0) ? getPatientById($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $p) {
    $userId   = $_POST['UserID'] ?? $p['UserID'];
    $name     = $_POST['Name'] ?? $p['Name'];
    $age      = (int)($_POST['Age'] ?? $p['Age']);
    $gender   = $_POST['Gender'] ?? $p['Gender'];
    $contact  = $_POST['Contact'] ?? $p['Contact'];
    $address  = $_POST['Address'] ?? $p['Address'];
    $category = $_POST['PatientCategory'] ?? $p['PatientCategory'];
    $notes    = $_POST['Notes'] ?? $p['Notes'];
    $setLinkedAtNow = isset($_POST['SetLinkedAtNow']) ? 1 : 0;
    updatePatient($id, $userId, $name, $age, $gender, $contact, $address, $category, $notes, $setLinkedAtNow);
    header('Location: admindashboard.php'); exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Update Patient</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script></head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Update Patient #<?php echo $id; ?></h3>
  <?php if (!$p) { echo "Not found"; } else { ?>
  <form method="post">
    <input type="number" name="UserID" value="<?php echo (int)$p['UserID']; ?>">
    <input type="text" name="Name" value="<?php echo htmlspecialchars($p['Name']); ?>">
    <input type="number" name="Age" value="<?php echo (int)$p['Age']; ?>">
    <select name="Gender">
      <option <?php echo ($p['Gender']=='Male')?'selected':''; ?>>Male</option>
      <option <?php echo ($p['Gender']=='Female')?'selected':''; ?>>Female</option>
      <option <?php echo ($p['Gender']=='Other')?'selected':''; ?>>Other</option>
    </select>
    <input type="text" name="Contact" value="<?php echo htmlspecialchars($p['Contact']); ?>">
    <input type="text" name="Address" value="<?php echo htmlspecialchars($p['Address']); ?>">
    <select name="PatientCategory">
      <option <?php echo ($p['PatientCategory']=='OPD')?'selected':''; ?>>OPD</option>
      <option <?php echo ($p['PatientCategory']=='IPD')?'selected':''; ?>>IPD</option>
      <option <?php echo ($p['PatientCategory']=='Emergency')?'selected':''; ?>>Emergency</option>
      <option <?php echo ($p['PatientCategory']=='Unknown')?'selected':''; ?>>Unknown</option>
    </select>
    <input type="text" name="Notes" value="<?php echo htmlspecialchars($p['Notes']); ?>">
    <label><input type="checkbox" name="SetLinkedAtNow"> Set LinkedAt = NOW()</label>
    <button type="submit" class="btn btn-sm">Update</button>
    <a class="btn btn-sm" href="admindashboard.php">Back</a>
  </form>
  <?php } ?>
</div>
</body></html>
