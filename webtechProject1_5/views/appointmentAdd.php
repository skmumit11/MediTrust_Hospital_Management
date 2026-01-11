<?php
require_once ('../models/appointmentModel.php');
require_once ('../models/patientModel.php');
require_once ('../models/doctorModel.php');

$patients = getAllPatients();
$doctors  = getAllDoctors(); 
$departments = getAllDepartments(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Resolve Patient ID
    $patientId = 0;
    $pType = $_POST['patient_type'] ?? 'existing';

    if ($pType === 'new') {
        $newName = trim($_POST['new_patient_name'] ?? '');
        $newContact = trim($_POST['new_patient_contact'] ?? '');
        
        if ($newName !== '') {
             // Add new patient with minimal info. Defaults: Age=0, Gender='Other', Address='', Category='OPD'
             $patientId = addPatient(null, $newName, 0, 'Other', $newContact, '', 'OPD', 'Quick Added via Appointment');
        }
    } else {
        $patientId = (int)($_POST['PatientID'] ?? 0);
    }

    // 2. Other fields
    $doctorId     = (int)($_POST['DoctorID'] ?? 0);
    $departmentId = (int)($_POST['DepartmentID'] ?? 0);
    $slot         = $_POST['Slot'] ?? '';
    // Status not in form request, but model has it. View form had it. We'll keep it or default to Pending.
    // The user didn't explicitly say remove status, but usually creation is 'Pending' or 'Confirmed'.
    // The previous form had status select. I'll keep it or default pending. User said "remove dept id" (input), not status.
    // user didn't mention status. I'll assume default or keep it. I'll keep it for admin flexibility.
    $status       = $_POST['Status'] ?? 'Pending';

    if ($patientId > 0 && $doctorId > 0 && $departmentId > 0 && $slot !== '') {
        addAppointment($patientId, $doctorId, $departmentId, $slot, $status, null);
        header('Location: admindashboard.php'); 
        exit;
    } else {
        $error = "Please fill all required fields.";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Appointment</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script src="../assets/sidebar.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
  <style>
      .form-group { margin-bottom: 15px; }
      label { display:block; margin-bottom:5px; font-weight:bold; }
      .input-control { width: 100%; padding: 8px; box-sizing: border-box; }
      .radio-group { margin-bottom: 15px; }
      .hidden { display: none; }
  </style>
</head>
<body>
<?php include 'layoutAdmin.php'; ?>

<div class="main-content">
  <h3>Add Appointment</h3>
  
  <?php if(isset($error)): ?>
    <div style="color:red; margin-bottom:10px;"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form method="post">
    
    <!-- PATIENT SELECTION -->
    <div class="panel">
        <h4>Patient Details</h4>
        <div class="radio-group">
            <label><input type="radio" name="patient_type" value="existing" checked onchange="togglePatient('existing')"> Existing Patient</label>
            <label><input type="radio" name="patient_type" value="new" onchange="togglePatient('new')"> New Patient</label>
        </div>

        <div id="existing_patient_div" class="form-group">
            <label>Select Patient:</label>
            <select name="PatientID" class="input-control">
                <option value="">-- Select Patient --</option>
                <?php foreach($patients as $p): ?>
                <option value="<?php echo $p['PatientID']; ?>">
                    <?php echo htmlspecialchars($p['Name'] . ' (' . $p['Contact'] . ')'); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="new_patient_div" class="form-group hidden">
            <label>Patient Name:</label>
            <input type="text" name="new_patient_name" class="input-control" placeholder="Full Name">
            <br><br>
            <label>Contact Number:</label>
            <input type="text" name="new_patient_contact" class="input-control" placeholder="Phone Number">
        </div>
    </div>

    <!-- APPOINTMENT DETAILS -->
    <div class="panel">
        <h4>Appointment Details</h4>
        
        <!-- Department Dropdown -->
        <div class="form-group">
            <label>Department:</label>
            <select name="DepartmentID" id="deptSelect" class="input-control" required onchange="filterDoctors()">
                <option value="" data-name="">-- Select Department --</option>
                <?php foreach($departments as $d): ?>
                    <option value="<?php echo $d['DepartmentID']; ?>" data-name="<?php echo htmlspecialchars($d['Name']); ?>">
                        <?php echo htmlspecialchars($d['Name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Doctor Dropdown -->
        <div class="form-group">
            <label>Doctor:</label>
            <select name="DoctorID" id="doctorSelect" class="input-control" required>
                <option value="">-- Select Doctor --</option>
                <?php foreach($doctors as $doc): ?>
                    <option value="<?php echo $doc['DoctorID']; ?>">
                        <?php echo htmlspecialchars($doc['Name'] . ' - ' . $doc['Specialty']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Date & Time:</label>
            <input type="datetime-local" name="Slot" class="input-control" required>
        </div>

        <div class="form-group">
            <label>Status:</label>
            <select name="Status" class="input-control">
                <option value="Pending">Pending</option>
                <option value="Confirmed">Confirmed</option>
            </select>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-sm">Create Appointment</button>
            <a class="btn btn-sm" href="admindashboard.php" style="background:#ccc; color:#000;">Cancel</a>
        </div>
    </div>

  </form>
</div>

<script>
function togglePatient(type) {
    if (type === 'existing') {
        document.getElementById('existing_patient_div').classList.remove('hidden');
        document.getElementById('new_patient_div').classList.add('hidden');
    } else {
        document.getElementById('existing_patient_div').classList.add('hidden');
        document.getElementById('new_patient_div').classList.remove('hidden');
    }
}

function filterDoctors() {
    var deptSelect = document.getElementById("deptSelect");
    var selectedOption = deptSelect.options[deptSelect.selectedIndex];
    var deptName = selectedOption.getAttribute('data-name');
    
    let xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../controllers/getDoctorsByDept.php', true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send('dept='+encodeURIComponent(deptName));
    
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
