<?php
// views/appointment_bookPatient.php
session_start();
require_once ('../models/db.php');
require_once ('../models/appointmentModel.php');
require_once ('../models/patientModel.php');
require_once ('../models/doctorModel.php');

/* ==============================
   Minimal helpers (procedural)
============================== */

function getLinkedPatientIdForLoggedUser() {
    if (!isset($_SESSION['username']) || $_SESSION['username'] === '') return null;

    $username = $_SESSION['username'];
    $conn = getConnection();

    // Resolve UserID
    $uid = null;
    $stmt = $conn->prepare("SELECT UserID FROM `User` WHERE Username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && ($row = $res->fetch_assoc())) { $uid = (int)$row['UserID']; }
    $stmt->close();

    // Resolve PatientID (linked)
    $pid = null;
    if ($uid !== null) {
        $q = $conn->prepare("SELECT PatientID FROM Patient WHERE UserID = ?");
        $q->bind_param('i', $uid);
        $q->execute();
        $rr = $q->get_result();
        if ($rr && ($pr = $rr->fetch_assoc())) { $pid = (int)$pr['PatientID']; }
        $q->close();
    }

    closeConnection($conn);
    return $pid; // null if not linked
}

function createGuestPatientIfNeeded($name, $gender, $contact, $address) {
    // Creates a Patient row with UserID = NULL and returns PatientID
    $conn = getConnection();

    // Basic insert; you can set default category Unknown
    $sql = "INSERT INTO Patient (UserID, Name, Age, Gender, Contact, Address, PatientCategory, CreatedAt, Notes)
            VALUES (NULL, ?, NULL, ?, ?, ?, 'Unknown', NOW(), NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $name, $gender, $contact, $address);
    $stmt->execute();
    $newPid = $stmt->insert_id;
    $stmt->close();

    closeConnection($conn);
    return ($newPid > 0) ? $newPid : 0;
}

function resolveCreatedByUserIdFromSession() {
    if (!isset($_SESSION['username']) || $_SESSION['username'] === '') return null;
    $conn = getConnection();
    $uid = null;
    $stmt = $conn->prepare("SELECT UserID FROM `User` WHERE Username = ?");
    $stmt->bind_param('s', $_SESSION['username']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && ($row = $res->fetch_assoc())) { $uid = (int)$row['UserID']; }
    $stmt->close();
    closeConnection($conn);
    return $uid;
}

/* ==============================
   Page state
============================== */
$message = "";
$messageType = "success";

$departments = getAllDepartments(); 
// Doctors loaded via AJAX now
$prefillPatientId = getLinkedPatientIdForLoggedUser(); // if linked

// Prefill name for convenience
$prefillName = '';
if (isset($_SESSION['name']) && $_SESSION['name'] !== '') {
    $prefillName = $_SESSION['name'];
} elseif (isset($_SESSION['username']) && $_SESSION['username'] !== '') {
    $prefillName = $_SESSION['username'];
}

/* ==============================
   Handle form submit
============================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departmentId = isset($_POST['DepartmentID']) ? (int)$_POST['DepartmentID'] : 0;
    $doctorId     = isset($_POST['DoctorID']) ? (int)$_POST['DoctorID'] : 0;
    $slotRaw      = $_POST['Slot'] ?? '';
    // Status default for book is pending
    $status       = 'Pending';

    // Normalize datetime-local to MySQL DATETIME
    $slot = '';
    if ($slotRaw !== '') {
        // 'YYYY-MM-DDTHH:MM' -> 'YYYY-MM-DD HH:MM:00'
        $slot = str_replace('T', ' ', $slotRaw) . ':00';
    }

    // Determine PatientID to use
    $patientIdToUse = null;

    if ($prefillPatientId !== null && $prefillPatientId > 0) {
        // Logged-in AND linked as Patient
        $patientIdToUse = $prefillPatientId;
    } else {
        // Either logged-in but not linked OR completely guest/non-user
        // We need guest patient info from the form
        $guest_name    = trim($_POST['GuestName'] ?? '');
        $guest_gender  = $_POST['GuestGender'] ?? 'Other';
        $guest_contact = trim($_POST['GuestContact'] ?? '');
        $guest_address = trim($_POST['GuestAddress'] ?? '');

        if ($guest_name === '' || $guest_contact === '') {
            $message = "Please provide Name and Contact for guest/non-linked booking.";
            $messageType = "error";
        } else {
            // Create guest Patient row
            $newPid = createGuestPatientIfNeeded($guest_name, $guest_gender, $guest_contact, $guest_address);
            if ($newPid > 0) {
                $patientIdToUse = $newPid;
            } else {
                $message = "Failed to create guest patient.";
                $messageType = "error";
            }
        }
    }

    // Validate selects and slot
    if ($message === "") {
        if ($patientIdToUse <= 0 || $doctorId <= 0 || $departmentId <= 0 || $slot === '') {
            $message = "Please select Department, Doctor, Date/Time, and ensure patient details are valid.";
            $messageType = "error";
        } else {
            $createdByUserId = resolveCreatedByUserIdFromSession(); // nullable
            $newId = addAppointment($patientIdToUse, $doctorId, $departmentId, $slot, $status, $createdByUserId);
            if ($newId > 0) {
                $message = "Appointment booked successfully (ID: {$newId}).";
                $messageType = "success";
            } else {
                $message = "Failed to book appointment.";
                $messageType = "error";
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Book Appointment - MediTrust</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script src="../assets/sidebar.js"></script>
    <style>
      .form-group { margin-bottom: 15px; }
      label { display:block; margin-bottom:5px; font-weight:bold; }
      .input-control { width: 100%; padding: 8px; box-sizing: border-box; }
  </style>
</head>
<body>
<?php include 'layoutPatient.php'; ?>

<!-- <div class="topbar">
  <div class="topbar-title">MediTrust</div>
  <button class="menu-btn" aria-label="Open Menu">☰</button>
</div>
<div class="sidebar" id="publicSidebar">
  <div class="sidebar-header">
    // ... sidebar content ...
  </div>
   ...
</div> 
-->
<!-- Note: layoutPatient.php already includes sidebar and topbar structure usually, but the original file had duplicates. 
     I will keep the layoutPatient include and adding the main content container. 
     The original file had a duplicate sidebar definition. I'll rely on layoutPatient.php 
-->

<div class="main-content">
  <h2>Book Appointment</h2>

  <?php if ($message !== "") { ?>
    <div class="alert <?php echo ($messageType === "success") ? "alert-success" : "alert-error"; ?>" style="padding:10px; margin-bottom:10px; border:1px solid #ccc; background: <?php echo ($messageType === "success") ? '#d4edda' : '#f8d7da'; ?>;">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php } ?>

  <form method="post">
    <!-- If logged-in AND linked as Patient, show their PatientID -->
    <div class="panel">
        <?php if ($prefillPatientId !== null && $prefillPatientId > 0) { ?>
        <p><b>Your PatientID:</b> <?php echo (int)$prefillPatientId; ?></p>
        <p>This booking will use your linked patient record.</p>
        <?php } else { ?>
        <h4>Guest / Non-linked Patient Info</h4>
        <div class="form-group">
            <input type="text" name="GuestName" class="input-control" placeholder="Full Name" value="<?php echo htmlspecialchars($prefillName); ?>" required>
        </div>
        <div class="form-group">
            <select name="GuestGender" class="input-control">
                <option>Male</option><option>Female</option><option selected>Other</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" name="GuestContact" class="input-control" placeholder="Contact (e.g., +880...)" required>
        </div>
        <div class="form-group">
            <input type="text" name="GuestAddress" class="input-control" placeholder="Address (optional)">
        </div>
        <?php } ?>
    </div>

    <div class="panel">
        <h4>Appointment Details</h4>
        
        <div class="form-group">
            <label>Department</label>
            <select name="DepartmentID" id="deptSelect" class="input-control" required onchange="filterDoctors()">
            <option value="" data-name="">-- Select Department --</option>
            <?php foreach ($departments as $dep) { ?>
                <option value="<?php echo (int)$dep['DepartmentID']; ?>" data-name="<?php echo htmlspecialchars($dep['Name']); ?>">
                <?php echo htmlspecialchars($dep['Name']); ?>
                </option>
            <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Doctor</label>
            <select name="DoctorID" id="doctorSelect" class="input-control" required>
            <option value="">-- Select Doctor --</option>
            <!-- Filled via AJAX -->
            </select>
        </div>

        <div class="form-group">
            <label>Date & Time</label>
            <input type="datetime-local" name="Slot" class="input-control" required>
        </div>

        <button type="submit" class="btn btn-sm">Book Appointment</button>
    </div>
  </form>

  <a class="btn" href="patientdashboard.php" style="margin-top:10px; display:inline-block;">Back</a>
</div>

<?php  include('footer.php')?>

<script>
function filterDoctors() {
    var deptSelect = document.getElementById("deptSelect");
    var selectedOption = deptSelect.options[deptSelect.selectedIndex];
    var deptName = selectedOption.getAttribute('data-name');
    
    let xhttp = new XMLHttpRequest();
    xhttp.open('GET', '../controllers/getDoctorsByDept.php?dept='+encodeURIComponent(deptName), true);
    xhttp.send();
    
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            let response = JSON.parse(this.responseText);
            let docSelect = document.getElementById("doctorSelect");
            docSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
            
            for(let i=0; i<response.length; i++){
                let doc = response[i];
                let option = document.createElement("option");
                option.value = doc.DoctorID;
                option.text = doc.Name + ' - ' + doc.Specialty;
                docSelect.appendChild(option);
            }
        }
    }
}
</script>

</body>
</html>
