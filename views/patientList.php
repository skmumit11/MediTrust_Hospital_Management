
<?php
session_start();
require_once "../models/patientModel.php";

$patients = getAllPatients();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient List</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>

<?php include "layoutAdmin.php"; ?>

<div class="main-content">
  <section class="page-hero">
    <h1>Patients</h1>
    <p>View and manage patient records</p>
  </section>

  <section class="panel">
    <div class="panel-title">
      <h2>Patient List</h2>
      <div>
        <a class="btn small" href="patientAdd.php">Add Patient</a>
        <a class="btn small" href="admindashboard.php">Back</a>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <tr>
          <th>PatientID</th>
          <th>Name</th>
          <th>Age</th>
          <th>Gender</th>
          <th>Contact</th>
          <th>Action</th>
        </tr>

        <?php foreach($patients as $p): ?>
        <tr>
          <td><?php echo (int)$p["PatientID"]; ?></td>
          <td><?php echo $p["Name"]; ?></td>
          <td><?php echo $p["Age"]; ?></td>
          <td><?php echo $p["Gender"]; ?></td>
          <td><?php echo $p["Contact"]; ?></td>
          <td>
            <a class="btn small" href="patientView.php?id=<?php echo (int)$p["PatientID"]; ?>">View</a>
            <a class="btn small" href="patientUpdate.php?id=<?php echo (int)$p["PatientID"]; ?>">Update</a>
            <a class="btn small danger" href="patientDelete.php?id=<?php echo (int)$p["PatientID"]; ?>">Delete</a>
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
``
