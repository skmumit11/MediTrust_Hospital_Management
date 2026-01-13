
<?php
require_once ('../models/bedModel.php');
$allocs = getAllBedAllocations(200);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Bed Allocation</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script></head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h3>Bed Allocations</h3>
  <table class="table">
    <tr>
      <th>AllocID</th><th>BedID</th><th>Type</th><th>PatientID</th><th>PatientName</th>
      <th>IPDID</th><th>AllocatedAt</th><th>ReleasedAt</th><th>Status</th><th>Action</th>
    </tr>
    <?php foreach ($allocs as $a) { ?>
      <tr>
        <td><?php echo (int)$a['AllocationID']; ?></td>
        <td><?php echo (int)$a['BedID']; ?></td>
        <td><?php echo htmlspecialchars($a['Type']); ?></td>
        <td><?php echo (int)$a['PatientID']; ?></td>
        <td><?php echo htmlspecialchars($a['PatientName']); ?></td>
        <td><?php echo htmlspecialchars($a['IPDID']); ?></td>
        <td><?php echo htmlspecialchars($a['AllocatedAt']); ?></td>
        <td><?php echo htmlspecialchars($a['ReleasedAt']); ?></td>
        <td><?php echo htmlspecialchars($a['AllocationStatus']); ?></td>
        <td><a class="btn btn-sm" href="bedRelease.php?id=<?php echo (int)$a['AllocationID']; ?>">Release</a></td>
      </tr>
    <?php } ?>
  </table>

  <h4 style="margin-top:20px;">Allocate Bed</h4>
  <form method="post" action="bedAllocate.php">
    <input type="number" name="BedID" placeholder="BedID" required>
    <input type="number" name="PatientID" placeholder="PatientID" required>
    <input type="number" name="AllocatedByUserID" placeholder="AllocatedByUserID" required>
    <input type="number" name="IPDID" placeholder="IPDID (optional)">
    <button type="submit" class="btn btn-sm">Allocate</button>
    <a class="btn btn-sm" href="admindashboard.php">Back</a>
  </form>
</div>
</body></html>
