
<?php
// views/doctorAdd.php
require_once ('../models/doctorModel.php');

$message = "";
$messageType = "success";

// Handle POST (existing/new) — keep your existing handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? 'existing';
    $specialty    = $_POST['Specialty'] ?? '';
    $availability = $_POST['Availability'] ?? '';

    if ($specialty === '' || $availability === '') {
        $message = "Please provide Specialty and Availability.";
        $messageType = "error";
    } else {
        if ($mode === 'existing') {
            $username = $_POST['Username'] ?? '';
            if ($username === '') {
                $message = "Please select a username from the dropdown.";
                $messageType = "error";
            } else {
                $uid = getUserIdByUsername($username);
                if ($uid <= 0) {
                    $message = "No such username found.";
                    $messageType = "error";
                } else {
                    $did = ensureDoctorRow($uid, $specialty, $availability);
                    if ($did > 0) { $message = "Doctor saved successfully (UserID/DoctorID: {$did})."; $messageType = "success"; }
                    else          { $message = "Failed to create/update Doctor."; $messageType = "error"; }
                }
            }
        } else {
            $name     = $_POST['Name'] ?? '';
            $username = $_POST['NewUsername'] ?? '';
            $password = $_POST['Password'] ?? '';   // (no hashing per your preference)
            $email    = $_POST['Email'] ?? '';
            $dob      = $_POST['DOB'] ?? '';
            $gender   = $_POST['Gender'] ?? 'Other';
            $address  = $_POST['Address'] ?? '';

            if ($name === '' || $username === '' || $password === '' || $email === '' || $dob === '') {
                $message = "Please fill Name, Username, Password, Email, DOB.";
                $messageType = "error";
            } else {
                $uid = createDoctorUser($name, $username, $password, $email, $dob, $gender, $address);
                if ($uid <= 0) {
                    $message = "Failed to create user (username may already exist).";
                    $messageType = "error";
                } else {
                    $did = ensureDoctorRow($uid, $specialty, $availability);
                    if ($did > 0) { $message = "New Doctor created successfully (UserID/DoctorID: {$did})."; $messageType = "success"; }
                    else          { $message = "User created, but doctor insert failed."; $messageType = "error"; }
                }
            }
        }
    }
}

// Fetch dropdown data
$eligibleUsers   = getUsersNotInDoctor();
$specialtyOptions = getSpecialtyOptions(); // new helper
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Doctor</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>
<?php include 'layoutAdmin.php'; ?>

<div class="main-content">
  <h3>Add Doctor</h3>

  <?php if ($message !== "") { ?>
    <div class="alert <?php echo ($messageType === "success") ? "alert-success" : "alert-error"; ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php } ?>

  <!-- EXISTING USER MODE WITH DROPDOWN -->
  <h4 style="margin-top:10px;">Link Existing User</h4>
  <form method="post" style="margin-bottom:20px;">
    <input type="hidden" name="mode" value="existing">

    <label>Select Username</label>
    <select name="Username">
      <option value="">-- Select a user --</option>
      <?php foreach ($eligibleUsers as $u) { ?>
        <option value="<?php echo htmlspecialchars($u['Username']); ?>">
          <?php echo htmlspecialchars($u['Name']) . " (" . htmlspecialchars($u['Username']) . ") – Role: " . htmlspecialchars($u['Role']); ?>
        </option>
      <?php } ?>
    </select>

    <label>Specialty</label>
    <select name="Specialty">
      <option value="">-- Select specialty --</option>
      <?php foreach ($specialtyOptions as $sp) { ?>
        <option value="<?php echo htmlspecialchars($sp); ?>"><?php echo htmlspecialchars($sp); ?></option>
      <?php } ?>
      <option value="Other">Other (type manually below)</option>
    </select>
    <!-- Optional free-text when "Other" is chosen -->
    <input type="text" name="SpecialtyOther" placeholder="If 'Other' selected, type specialty here">

    <label>Availability</label>
    <input type="text" name="Availability" placeholder="Sun-Thu 10:00-16:00" required>

    <button type="submit" class="btn btn-sm">Save Doctor (Existing)</button>
    admindashboard.phpBack</a>
  </form>

  <!-- CREATE NEW USER + DOCTOR -->
  <h4>Create New Doctor (User + Doctor)</h4>
  <form method="post">
    <input type="hidden" name="mode" value="new">

    <!-- User fields -->
    <input type="text" name="Name" placeholder="Full Name" required>
    <input type="text" name="NewUsername" placeholder="New Username" required>
    <input type="text" name="Password" placeholder="Password" required>
    <input type="email" name="Email" placeholder="Email" required>
    <input type="date" name="DOB" placeholder="YYYY-MM-DD" required>
    <select name="Gender">
      <option>Male</option><option>Female</option><option selected>Other</option>
    </select>
    <input type="text" name="Address" placeholder="Address">

    <!-- Doctor fields -->
    <label>Specialty</label>
    <select name="Specialty">
      <option value="">-- Select specialty --</option>
      <?php foreach ($specialtyOptions as $sp) { ?>
        <option value="<?php echo htmlspecialchars($sp); ?>"><?php echo htmlspecialchars($sp); ?></option>
      <?php } ?>
      <option value="Other">Other (type manually below)</option>
    </select>
    <input type="text" name="SpecialtyOther" placeholder="If 'Other' selected, type specialty here">

    <label>Availability</label>
    <input type="text" name="Availability" placeholder="Sun-Thu 10:00-16:00" required>

    <button type="submit" class="btn btn-sm">Create New Doctor</button>
    <a href="admindashboard.php">Back</a>
  </form>
</div>

<div class="footer">
  <p>&copy; <?php echo date('Y'); ?> MediTrust Hospital Management System</p>
</div>
</body>
</html>
