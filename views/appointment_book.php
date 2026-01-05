
<?php
// views/appointment_book.php
session_start();
require_once __DIR__ . '/../models/db.php';

/* ==============================
   Minimal helpers (procedural)
============================== */

function getDepartments() {
    $conn = getConnection();
    $rows = [];
    $res = $conn->query("SELECT DepartmentID, Name FROM Department ORDER BY Name ASC");
    if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } $res->free(); }
    closeConnection($conn);
    return $rows;
}

function getDoctors() {
    // Show all doctors (DoctorID -> UserID). If later you want to filter by department/duty, we can add that.
    $conn = getConnection();
    $rows = [];
    $sql = "SELECT d.DoctorID, u.Name AS DoctorName
            FROM Doctor d
            JOIN `User` u ON u.UserID = d.DoctorID
            ORDER BY u.Name ASC";
    $res = $conn->query($sql);
    if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } $res->free(); }
    closeConnection($conn);
    return $rows;
}

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

function addAppointmentRecord($patientId, $doctorId, $departmentId, $slot, $status, $createdByUserId = null) {
    $conn = getConnection();
    $sql = "INSERT INTO Appointment
            (PatientID, DoctorID, DepartmentID, Slot, Status, CreatedByUserID, CreatedAt)
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $cuid = ($createdByUserId === null) ? null : (int)$createdByUserId;
    $stmt->bind_param('iiissi', $patientId, $doctorId, $departmentId, $slot, $status, $cuid);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    return $newId;
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

$departments = getDepartments();
$doctors     = getDoctors();
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
    $status       = $_POST['Status'] ?? 'Pending';

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
            $newId = addAppointmentRecord($patientIdToUse, $doctorId, $departmentId, $slot, $status, $createdByUserId);
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
  <script scr="../assets/sidebar.js"></script>
</head>
<body>
<?php include 'layoutPatient.php'; ?>
<div class="topbar">
  <div class="topbar-title">MediTrust</div>
  <button class="menu-btn" aria-label="Open Menu">☰</button>
</div>
<div class="sidebar" id="publicSidebar">
  <div class="sidebar-header">
    <div class="logo-text">Menu</div>
    <button class="toggle-btn" aria-label="Close Menu">✕</button>
  </div>
  <div class="sidebar-menu">
    admindashboard.phpAdmin</a>
    requestAmbulance.phpAmbulance</a>
    patientdashboard.phpPatient Dashboard</a>
    ../controllers/logoutCheck.phpLogout</a>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var sidebar = document.querySelector('.sidebar');
  var openBtn = document.querySelector('.menu-btn');
  var closeBtn = document.querySelector('.toggle-btn');
  if (openBtn) openBtn.addEventListener('click', function(){ sidebar.classList.add('active'); });
  if (closeBtn) closeBtn.addEventListener('click', function(){ sidebar.classList.remove('active'); });
});
</script>

<div class="main-content">
  <h2>Book Appointment (Public)</h2>

  <?php if ($message !== "") { ?>
    <div class="alert <?php echo ($messageType === "success") ? "alert-success" : "alert-error"; ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php } ?>

  <form method="post">
    <!-- If logged-in AND linked as Patient, show their PatientID -->
    <?php if ($prefillPatientId !== null && $prefillPatientId > 0) { ?>
      <p><b>Your PatientID:</b> <?php echo (int)$prefillPatientId; ?></p>
      <p>This booking will use your linked patient record.</p>
    <?php } else { ?>
      <h4>Guest / Non-linked Patient Info</h4>
      <input type="text" name="GuestName"    placeholder="Full Name" value="<?php echo htmlspecialchars($prefillName); ?>" required>
      <select name="GuestGender">
        <option>Male</option><option>Female</option><option selected>Other</option>
      </select>
      <input type="text" name="GuestContact" placeholder="Contact (e.g., +880...)" required>
      <input type="text" name="GuestAddress" placeholder="Address (optional)">
    <?php } ?>

    <h4>Appointment Details</h4>
    <label>Department</label>
    <select name="DepartmentID" required>
      <option value="">-- Select Department --</option>
      <?php foreach ($departments as $dep) { ?>
        <option value="<?php echo (int)$dep['DepartmentID']; ?>">
          <?php echo htmlspecialchars($dep['Name']); ?>
        </option>
      <?php } ?>
    </select>

    <label>Doctor</label>
    <select name="DoctorID" required>
      <option value="">-- Select Doctor --</option>
      <?php foreach ($doctors as $doc) { ?>
        <option value="<?php echo (int)$doc['DoctorID']; ?>">
          <?php echo htmlspecialchars($doc['DoctorName']); ?>
        </option>
      <?php } ?>
    </select>

    <label>Date & Time</label>
    <input type="datetime-local" name="Slot" required>
<button ></button>
    <!-- <label>Status</label> -->
    <!-- <select name="Status">
       <option value="Pending">Pending</option>
      <option value="Confirmed">Confirmed</option>
      <option value="Completed">Completed</option>
      <option value="Cancelled">Cancelled</option>
    </select> -->

    <button type="submit" class="btn btn-sm">Book Appointment</button>
  </form>

  <a class="btn" href="home.php">Back</a>
</div>

<?php  include('footer.php')?>
</body>
</html>
