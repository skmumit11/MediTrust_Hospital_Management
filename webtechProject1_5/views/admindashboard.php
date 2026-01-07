<?php
session_start();
require_once('../controllers/authCheck.php');
require_once "../controllers/adminController.php";

$dashboardData = loadDashboard();

$totalDoctors      = $dashboardData['totalDoctors'];
$totalPatients     = $dashboardData['totalPatients'];
$availableBeds     = $dashboardData['availableBeds'];
$icuBeds           = $dashboardData['icuBeds'];
$generalBeds       = $dashboardData['generalBeds'];
$appointments      = $dashboardData['appointments'];
$doctors           = $dashboardData['doctors'];
$patients          = $dashboardData['patients'];
$ambulanceRequests = $dashboardData['ambulanceRequests'];
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - MediTrust</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>

<?php include 'layoutAdmin.php'; ?>

<div class="main-content">
  <section class="page-title">
    <h1>Admin Dashboard</h1>
    <p>Manage hospital operations efficiently</p>
  </section>

  <section class="dashboard">
    <button class="btn">Total Doctors: <?php echo $totalDoctors ?? 0; ?></button>
    <button class="btn">Total Patients: <?php echo $totalPatients ?? 0; ?></button>
    <button class="btn">Available Beds: <?php echo $availableBeds ?? 0; ?></button>
  </section>

  <!-- Ambulance -->
  <section class="panel">
    <h2>Ambulance Requests</h2>
    <table class="table">
      <tr>
        <th>Request ID</th><th>Patient</th><th>Contact</th>
        <th>Pickup Location</th><th>Status</th><th>Action</th>
      </tr>
      <?php if(!empty($ambulanceRequests)) foreach($ambulanceRequests as $ar): ?>
      <tr>
        <td>#<?php echo $ar["RequestID"]; ?></td>
        <td><?php echo htmlspecialchars($ar["Name"]); ?></td>
        <td><?php echo htmlspecialchars($ar["Contact"]); ?></td>
        <td><?php echo htmlspecialchars($ar["PickupLocation"]); ?></td>
        <td><?php echo htmlspecialchars($ar["Status"]); ?></td>
        <td>
          <a class="btn btn-sm" href="ambulanceView.php?id=<?php echo $ar["RequestID"]; ?>">View</a>
          <a class="btn btn-sm" href="ambulanceAccept.php?id=<?php echo $ar["RequestID"]; ?>">Accept</a>
          <a class="btn btn-sm" href="ambulanceDispatch.php?id=<?php echo $ar["RequestID"]; ?>">Dispatch</a>
          <a class="btn btn-sm" href="ambulanceUpdate.php?id=<?php echo $ar["RequestID"]; ?>">Update</a>
          <a class="btn btn-sm" href="ambulanceDelete.php?id=<?php echo $ar["RequestID"]; ?>">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </section>

  <!-- Appointments -->
  <section class="panel">
    <div class="panel-title">
      <h2>Appointments Management</h2>
      <a href="appointmentAdd.php" class="btn btn-sm">Add Appointment</a>
    </div>
    <table class="table">
      <tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Status</th><th colspan="2">Action</th></tr>
      <?php if(!empty($appointments)) foreach($appointments as $a): ?>
      <tr>
        <td><?php echo htmlspecialchars($a["patient"]); ?></td>
        <td><?php echo htmlspecialchars($a["doctor"]); ?></td>
        <td><?php echo htmlspecialchars($a["Slot"]); ?></td>
        <td><?php echo htmlspecialchars($a["Status"]); ?></td>
        <td><a href="appointmentView.php?id=<?php echo $a["AppointmentID"]; ?>" class="btn btn-sm">View</a></td>
        <td>
          <a href="appointmentUpdate.php?id=<?php echo $a["AppointmentID"]; ?>" class="btn btn-sm">Update</a>
          <a href="appointmentDelete.php?id=<?php echo $a["AppointmentID"]; ?>" class="btn btn-sm">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </section>

  <!-- Patients -->
  <section class="panel">
    <div class="panel-title">
      <h2>Patients Management</h2>
      <a href="patientAdd.php" class="btn btn-sm">Add Patient</a>
    </div>
    <table class="table">
      <tr><th>Name</th><th>Age</th><th>Gender</th><th colspan="2">Action</th></tr>
      <?php if(!empty($patients)) foreach($patients as $p): ?>
      <tr>
        <td><?php echo htmlspecialchars($p["Name"]); ?></td>
        <td><?php echo htmlspecialchars($p["Age"]); ?></td>
        <td><?php echo htmlspecialchars($p["Gender"]); ?></td>
        <td><a href="patientView.php?id=<?php echo $p["PatientID"]; ?>" class="btn btn-sm">View</a></td>
        <td>
          <a href="patientUpdate.php?id=<?php echo $p["PatientID"]; ?>" class="btn btn-sm">Update</a>
          <a href="patientDelete.php?id=<?php echo $p["PatientID"]; ?>" class="btn btn-sm">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </section>

  <!-- Beds -->
  <section class="panel">
    <div class="panel-title"><h2>Bed Management</h2> <a href="bedAdd.php" class="btn btn-sm">Add Bed</a></div>
    <div class="bed-status" style="display:flex; gap:12px;">
      <button class="btn">ICU Beds: <?php echo $icuBeds ?? 0; ?></button>
      <button class="btn">General Beds: <?php echo $generalBeds ?? 0; ?></button>
    </div>
    <p style="margin-top:10px;">
      <a class="btn btn-sm" href="bedView.php">View Beds</a>
      <a class="btn btn-sm" href="bedAllocationList.php">Bed Allocation</a>
    </p>
  </section>

  <!-- Doctors -->
  <section class="panel">
    <div class="panel-title">
      <h2>Doctors Management</h2>
      <a href="doctorAdd.php" class="btn btn-sm">Add Doctor</a>
    </div>
    <table class="table">
      <tr><th>Name</th><th>Specialty</th><th>Contact (Username)</th><th colspan="2">Action</th></tr>
      <?php if(!empty($doctors)) foreach($doctors as $d): ?>
      <tr>
        <td><?php echo htmlspecialchars($d["Name"]); ?></td>
        <td><?php echo htmlspecialchars($d["Specialty"]); ?></td>
        <td><?php echo htmlspecialchars($d["Username"]); ?></td>
        <td><a href="doctorView.php?id=<?php echo $d["DoctorID"]; ?>" class="btn btn-sm">View</a></td>
        <td>
          <a href="doctorUpdate.php?id=<?php echo $d["DoctorID"]; ?>" class="btn btn-sm">Update</a>
          <a href="doctorDelete.php?id=<?php echo $d["DoctorID"]; ?>" class="btn btn-sm">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </section>
</div>

<div class="footer">
  <?php include ('footer.php');?>
</div>

</body>
</html>
