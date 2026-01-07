
<?php
require_once ('../models/bedModel.php');
$beds = getAllBeds();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Bed List</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script></head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Bed List</h3>
  <table class="table">
    <tr><th>ID</th><th>Type</th><th>Status</th><th>Action</th></tr>
    <?php foreach ($beds as $b) { ?>
      <tr>
        <td><?php echo (int)$b['BedID']; ?></td>
        <td><?php echo htmlspecialchars($b['Type']); ?></td>
        <td><?php echo htmlspecialchars($b['Status']); ?></td>
        <td>
          <a class="btn btn-sm" href="bedUpdate.php?id=<?php echo (int)$b['BedID']; ?>">Update</a>
          <a class="btn btn-sm" href="bedDelete.php?id=<?php echo (int)$b['BedID']; ?>">Delete</a>
        </td>
      </tr>
    <?php } ?>
  </table>
  <p><a class="btn btn-sm" href="admindashboard.php">Back</a></p>
</div>
</body></html>
