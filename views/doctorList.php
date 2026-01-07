
<?php
session_start();
require_once "../models/doctorModel.php";

$doctors = getAllDoctors();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Doctor List</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>

<?php include "layoutAdmin.php"; ?>

<div class="main-content">
  <section class="page-hero">
    <h1>Doctors</h1>
    <p>View and manage doctor profiles</p>
  </section>

  <section class="panel">
    <div class="panel-title">
      <h2>Doctor List</h2>
      <div>
        <a class="btn small" href="doctorAdd.php">Add Doctor</a>
        <a class="btn small" href="admindashboard.php">Back</a>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <tr>
          <th>DoctorID</th>
          <th>Name</th>
          <th>Specialty</th>
          <th>Username</th>
          <th>Action</th>
        </tr>

        <?php foreach($doctors as $d): ?>
        <tr>
          <td><?php echo (int)$d["DoctorID"]; ?></td>
          <td><?php echo $d["Name"]; ?></td>
          <td><?php echo $d["Specialty"]; ?></td>
          <td><?php echo $d["Username"]; ?></td>
          <td>
            <a class="btn small" href="doctorView.php?id=<?php echo (int)$d["DoctorID"]; ?>">View</a>
            <a class="btn small" href="doctorUpdate.php?id=<?php echo (int)$d["DoctorID"]; ?>">Update</a>
            <a class="btn small danger" href="doctorDelete.php?id=<?php echo (int)$d["DoctorID"]; ?>">Delete</a>
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
