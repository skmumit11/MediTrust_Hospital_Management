
<?php
require_once ('../models/ambulanceModel.php');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$rows = getAllAmbulanceRequests(500);
$detail = null;
foreach ($rows as $r) { if ((int)$r['RequestID'] === $id) { $detail = $r; break; } }
?>
<!doctype html>
<html>

<head>
<meta charset="utf-8"><title>Ambulance View</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>
<?php include 'layoutAdmin.php'; ?>
<div class="main-content">
  <h2>Ambulance Request #<?php echo $id; ?></h2>
  <?php if (!$detail) { echo "Not found"; } else { ?>
    <p><b>Name:</b> <?php echo htmlspecialchars($detail['Name']); ?></p>
    <p><b>Contact:</b> <?php echo htmlspecialchars($detail['Contact']); ?></p>
    <p><b>Location:</b> <?php echo htmlspecialchars($detail['PickupLocation']); ?></p>
    <p><b>Type:</b> <?php echo htmlspecialchars($detail['EmergencyType']); ?></p>
    <p><b>Status:</b> <?php echo htmlspecialchars($detail['Status']); ?></p>
    <p><b>Requested At:</b> <?php echo htmlspecialchars($detail['RequestedAt']); ?></p>
  <?php } ?>
  <p><a class="btn btn-sm" href="admindashboard.php">Back</a></p>
</div>
</body>
</html>
