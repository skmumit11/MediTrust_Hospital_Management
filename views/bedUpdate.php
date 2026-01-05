
<?php
require_once __DIR__ . '/../models/bedModel.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$bed = ($id > 0) ? getBedById($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $bed) {
    $type = $_POST['Type'] ?? $bed['Type'];
    $status = $_POST['Status'] ?? $bed['Status'];
    updateBed($id, $type, $status);
    header('Location: bedView.php'); exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Update Bed</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Update Bed #<?php echo $id; ?></h3>
  <?php if (!$bed) { echo "Not found"; } else { ?>
  <form method="post">
    <select name="Type">
      <option <?php echo ($bed['Type']=='ICU')?'selected':''; ?>>ICU</option>
      <option <?php echo ($bed['Type']=='General')?'selected':''; ?>>General</option>
    </select>
    <select name="Status">
      <option <?php echo ($bed['Status']=='Available')?'selected':''; ?>>Available</option>
      <option <?php echo ($bed['Status']=='Occupied')?'selected':''; ?>>Occupied</option>
    </select>
    <button type="submit" class="btn btn-sm">Update</button>
    <a class="btn btn-sm" href="bedView.php">Back</a>
  </form>
  <?php } ?>
</div>
</body></html>
