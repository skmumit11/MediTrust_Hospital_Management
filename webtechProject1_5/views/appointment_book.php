<?php
// views/appointment_book.php
session_start();
require_once ('../models/db.php');
require_once ('../models/appointmentModel.php');
require_once ('../models/patientModel.php');
require_once ('../models/doctorModel.php');

/* ==============================
   Minimal helpers (procedural)
============================== */

// Helper to create guest, similar to other files but duplicated here for independence if needed, 
// or could rely on patientModel `addPatient` if simpler. 
// I'll use the local helper logic for consistency with previous file.
function createGuestPatientIfNeeded($name, $gender, $contact, $address) {
    $conn = getConnection();
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

/* ==============================
   Page state
============================== */
$message = "";
$messageType = "success";

$departments = getAllDepartments(); 
// Doctors loaded via AJAX

// Prefill name if session exists (e.g. user stumbled here)
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
    // Status default for public book is pending
    $status       = 'Pending';

    // Normalize datetime-local to MySQL DATETIME
    $slot = '';
    if ($slotRaw !== '') {
        $slot = str_replace('T', ' ', $slotRaw) . ':00';
    }

    // Always Guest / New Patient Flow for public (unless we added login check logic, but user requested 'Public' implies no login needed)
    // However, if a logged-in user uses this page, we might ideally link them, but "Public" usually means "Guest".
    // I will stick to Guest creation for simplicity and safety, or use session if available.
    // The previous file had logic for linking. I'll keep it simple: Public = Guest inputs required always.
    
    $guest_name    = trim($_POST['GuestName'] ?? '');
    $guest_gender  = $_POST['GuestGender'] ?? 'Other';
    $guest_contact = trim($_POST['GuestContact'] ?? '');
    $guest_address = trim($_POST['GuestAddress'] ?? '');
    
    $patientIdToUse = 0;

    if ($guest_name === '' || $guest_contact === '') {
        $message = "Please provide Name and Contact.";
        $messageType = "error";
    } else {
        $newPid = createGuestPatientIfNeeded($guest_name, $guest_gender, $guest_contact, $guest_address);
        if ($newPid > 0) {
            $patientIdToUse = $newPid;
        } else {
            $message = "Failed to create patient record.";
            $messageType = "error";
        }
    }

    if ($message === "") {
        if ($patientIdToUse <= 0 || $doctorId <= 0 || $departmentId <= 0 || $slot === '') {
            $message = "Please fill all required fields.";
            $messageType = "error";
        } else {
            // CreatedByUserID is null for public
            $newId = addAppointment($patientIdToUse, $doctorId, $departmentId, $slot, $status, null);
            if ($newId > 0) {
                $message = "Appointment booked successfully (ID: {$newId}). We will contact you shortly.";
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
  <script src="../assets/sidebar.js"></script>
  <style>
      .form-group { margin-bottom: 15px; }
      label { display:block; margin-bottom:5px; font-weight:bold; }
      .input-control { width: 100%; padding: 8px; box-sizing: border-box; }
      .main-content { margin-left: 0; padding: 20px; max-width: 800px; margin: 0 auto; }
  </style>
</head>
<body>

<div class="topbar">
  <div class="topbar-title">MediTrust Hospital</div>
  <a href="home.php" style="color:white; text-decoration:none; float:right; margin-right:20px;">Home</a>
</div>

<div class="main-content">
  <h2>Book Appointment (Public)</h2>

  <?php if ($message !== "") { ?>
    <div class="alert <?php echo ($messageType === "success") ? "alert-success" : "alert-error"; ?>" style="padding:10px; margin-bottom:10px; border:1px solid #ccc; background: <?php echo ($messageType === "success") ? '#d4edda' : '#f8d7da'; ?>;">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php } ?>

  <form method="post">
    
    <div class="panel">
        <h4>Patient Information</h4>
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="GuestName" class="input-control" placeholder="Full Name" value="<?php echo htmlspecialchars($prefillName); ?>" required>
        </div>
        <div class="form-group">
            <label>Gender</label>
            <select name="GuestGender" class="input-control">
                <option>Male</option><option>Female</option><option selected>Other</option>
            </select>
        </div>
        <div class="form-group">
            <label>Contact</label>
            <input type="text" name="GuestContact" class="input-control" placeholder="Contact (e.g., +880...)" required>
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="GuestAddress" class="input-control" placeholder="Address (optional)">
        </div>
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
        <a class="btn btn-sm" href="home.php" style="background:#ccc; color:#000;">Cancel</a>
    </div>

  </form>
</div>

<?php include('footer.php'); ?>

<script>
function filterDoctors() {
    var deptSelect = document.getElementById("deptSelect");
    var selectedOption = deptSelect.options[deptSelect.selectedIndex];
    var deptName = selectedOption.getAttribute('data-name');
    
    let xhttp = new XMLHttpRequest();
    // Using GET as requested for this "part" (patient/public booking)
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
