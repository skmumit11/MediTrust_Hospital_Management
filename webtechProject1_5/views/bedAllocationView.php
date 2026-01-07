
<?php
session_start();
require_once "../models/bedAllocationModel.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$alloc = $id > 0 ? getAllocationById($id) : null;
if(!$alloc) { echo "Allocation not found"; exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Allocation Details</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>

<?php include "layoutAdmin.php"; ?>

<div class="main-content">

  <section class="page-hero">
    <h1>Allocation Details</h1>
    <p>View allocation record</p>
  </section>

  <section class="panel">
    <div class="info-grid">
      <div class="info-item"><b>AllocationID</b>#<?php echo (int)$alloc["AllocationID"]; ?></div>
      <div class="info-item"><b>Bed</b><?php echo (int)$alloc["BedID"]; ?> (<?php echo $alloc["Type"]; ?>)</div>
      <div class="info-item"><b>Bed Status</b><?php echo $alloc["Status"]; ?></div>
      <div class="info-item"><b>Patient</b><?php echo $alloc["PatientName"]; ?> (<?php echo (int)$alloc["PatientID"]; ?>)</div>
      <div class="info-item"><b>Allocation Status</b><?php echo $alloc["AllocationStatus"]; ?></div>
      <div class="info-item"><b>AllocatedAt</b><?php echo $alloc["AllocatedAt"]; ?></div>
      <div class="info-item"><b>ReleasedAt</b><?php echo $alloc["ReleasedAt"] ?? ""; ?></div>
    </div>

    <div class="form-actions">
      <a class="btn small" href="bedAllocationList.php">Back</a>
      <?php if($alloc["AllocationStatus"] === "Allocated"): ?>
        <a class="btn small danger" href="bedRelease.php?id=<?php echo (int)$alloc["AllocationID"]; ?>">Release</a>
      <?php endif; ?>
    </div>
  </section>

</div>

<?php include "footer.php"; ?>
</body>
</html>
``
