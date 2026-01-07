
<?php
session_start();
require_once "../models/bedModel.php";

$beds = getAllBeds();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bed List</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>

<?php include "layoutAdmin.php"; ?>

<div class="main-content">
  <section class="page-hero">
    <h1>Beds</h1>
    <p>Manage BedInventory records</p>
  </section>

  <section class="panel">
    <div class="panel-title">
      <h2>Bed List</h2>
      <div>
        <a class="btn small" href="bedAdd.php">Add Bed</a>
        <a class="btn small" href="bedAllocationList.php">Bed Allocation</a>
        <a class="btn small" href="admindashboard.php">Back</a>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <tr>
          <th>BedID</th>
          <th>Type</th>
          <th>Status</th>
          <th>Action</th>
        </tr>

        <?php foreach($beds as $b): ?>
        <tr>
          <td><?php echo (int)$b["BedID"]; ?></td>
          <td><?php echo $b["Type"]; ?></td>
          <td><?php echo $b["Status"]; ?></td>
          <td>
            <a class="btn small" href="bedUpdate.php?id=<?php echo (int)$b["BedID"]; ?>">Update</a>
            <a class="btn small danger" href="bedDelete.php?id=<?php echo (int)$b["BedID"]; ?>">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>

      </table>
    </div>
  </section>
</div>

<?php include "footer.php"; ?>
</body>
</html>
