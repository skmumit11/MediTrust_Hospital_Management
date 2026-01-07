<?php
session_start();
require_once('../controllers/authCheck11_15.php');
require_once('../models/opdIpdModel11_15.php');
require_once('../models/doctorModel11_15.php');

$patients = getAllPatients();
$doctors = getAllDoctors();

$id = isset($_GET['id']) ? $_GET['id'] : null;
$opd = null;
if($id){
    $opd = getOPDById($id);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $id ? 'Edit' : 'Add'; ?> OPD Visit</title>
    <link rel="stylesheet" href="../assets/style_layoutUser11_15.css">
</head>
<body>
    <?php include 'layoutAdmin11_15.php'; ?>
    
    <div class="main-content">
        <div class="container" style="max-width:600px; margin: 30px auto; background:white; padding:20px; border-radius:8px;">
            <h2><?php echo $id ? 'Edit' : 'Add'; ?> OPD Visit</h2>
            
            <form action="../controllers/opdIpdController11_15.php" method="post">
                <?php if($id){ echo "<input type='hidden' name='id' value='$id'>"; } ?>
                
                <label>Patient:</label>
                <select name="patient_id" required>
                    <option value="">Select Patient</option>
                    <?php foreach($patients as $p){
                        $sel = ($opd && $opd['PatientID'] == $p['PatientID']) ? 'selected' : '';
                        echo "<option value='{$p['PatientID']}' $sel>{$p['Name']}</option>";
                    } ?>
                </select>
                <br><br>
                
                <label>Doctor:</label>
                <select name="doctor_id" required>
                    <option value="">Select Doctor</option>
                    <?php foreach($doctors as $d){
                        $sel = ($opd && $opd['DoctorID'] == $d['DoctorID']) ? 'selected' : '';
                        echo "<option value='{$d['DoctorID']}' $sel>{$d['fullname']}</option>";
                    } ?>
                </select>
                <br><br>
                
                <label>Visit Date:</label>
                <input type="date" name="visit_date" value="<?php echo $opd ? $opd['VisitDate'] : date('Y-m-d'); ?>" required>
                <br><br>
                
                <label>Status:</label>
                <select name="status">
                    <?php 
                    $statuses = ["Visited", "TransferredToIPD", "Closed"];
                    foreach($statuses as $s){
                        $sel = ($opd && $opd['Status'] == $s) ? 'selected' : '';
                        echo "<option value='$s' $sel>$s</option>";
                    }
                    ?>
                </select>
                <br><br>
                
                <input type="submit" name="<?php echo $id ? 'edit_opd' : 'add_opd'; ?>" value="Save">
            </form>
        </div>
    </div>
</body>
</html>
