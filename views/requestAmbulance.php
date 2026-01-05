
<?php
session_start();
require_once __DIR__ . '/../controllers/ambulanceController.php';
handleAmbulanceCreate();
$ambulanceRequests = loadMyAmbulanceRequests(50);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MediTrust – Request Ambulance</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>
<?php include 'layoutPatient.php'; ?>

<div class="main-content">
  <section class="page-title"><h1>Request Ambulance</h1></section>

  <?php if (isset($message) && $message !== "") { ?>
    <div class="alert <?php echo ($messageType === "success") ? "alert-success" : "alert-error"; ?>">
      <strong><?php echo ($messageType === "success") ? "Success:" : "Error:"; ?></strong>
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php } ?>

  <form method="POST" action="">
    <input type="text" name="patient_name" placeholder="Patient Name" value="<?php echo htmlspecialchars($prefillName); ?>">
    <input type="text" name="contact_phone" placeholder="Contact Number">
    <input type="text" name="pickup_location" placeholder="Pickup Location">
    <select name="emergency_type">
      <option value="">-- Select --</option>
      <option>Road Accident</option><option>Breathing Trouble</option>
      <option>Heart Attack</option><option>Stroke</option>
      <option>Pregnancy Emergency</option><option>Burn/Fire Injury</option><option>Other</option>
    </select>
    <button type="submit" name="request_ambulance" class="btn btn-sm">Request Ambulance</button>
  </form>

  <h2 style="margin-top:24px;">Your Ambulance Requests</h2>
  <table class="table">
    <tr>
      <th>Patient Name</th><th>Pickup Location</th><th>Request Type</th><th>Contact</th><th>Status</th><th>Requested At</th>
    </tr>
    <?php if (!is_array($ambulanceRequests) || count($ambulanceRequests) === 0) { ?>
      <tr><td colspan="6">No ambulance requests found.</td></tr>
    <?php } else { foreach($ambulanceRequests as $ar) { ?>
      <tr>
        <td><?php echo htmlspecialchars($ar['PatientName']); ?></td>
        <td><?php echo htmlspecialchars($ar['PickupLocation']); ?></td>
        <td><?php echo htmlspecialchars($ar['EmergencyType']); ?></td>
        <td><?php echo htmlspecialchars($ar['PatientPhone']); ?></td>
        <td><?php echo htmlspecialchars($ar['Status']); ?></td>
        <td><?php echo htmlspecialchars($ar['RequestedAt']); ?></td>
      </tr>
    <?php } } ?>
  </table>
</div>

<div class="footer">
  <p>&copy; <?php echo date('Y'); ?> MediTrust Hospital Management System</p>
</div>
</body>
</html>
