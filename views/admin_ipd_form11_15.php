<?php
session_start();
require_once('../controllers/authCheck11_15.php');
require_once('../models/opdIpdModel11_15.php');

$patients = getAllPatients();

$id = isset($_GET['id']) ? $_GET['id'] : null;
$ipd = null;
if($id){
    $ipd = getIPDById($id);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $id ? 'Edit' : 'Add'; ?> IPD Admission</title>
    <link rel="stylesheet" href="../assets/style_layoutUser11_15.css">
</head>
<body>
    <?php include 'layoutAdmin11_15.php'; ?>
    
    <div class="main-content">
        <div class="container" style="max-width:600px; margin: 30px auto; background:white; padding:20px; border-radius:8px;">
            <h2><?php echo $id ? 'Edit' : 'Add'; ?> IPD Admission</h2>
            
            <form action="../controllers/opdIpdController11_15.php" method="post">
                <?php if($id){ echo "<input type='hidden' name='id' value='$id'>"; } ?>
                
                <label>Patient:</label>
                <select name="patient_id" required>
                    <option value="">Select Patient</option>
                    <?php foreach($patients as $p){
                        $sel = ($ipd && $ipd['PatientID'] == $p['PatientID']) ? 'selected' : '';
                        echo "<option value='{$p['PatientID']}' $sel>{$p['Name']}</option>";
                    } ?>
                </select>
                <br><br>
                
                <label>Room No:</label>
                <input type="text" name="room_no" value="<?php echo $ipd ? $ipd['RoomNo'] : ''; ?>" required>
                <br><br>
                
                <label>Admission Date:</label>
                <input type="date" name="admission_date" value="<?php echo $ipd ? $ipd['AdmissionDate'] : date('Y-m-d'); ?>" required>
                <br><br>
                
                <label>Discharge Date:</label>
                <input type="date" name="discharge_date" value="<?php echo $ipd ? $ipd['DischargeDate'] : ''; ?>">
                <br><br>
                
                <label>Admission Source:</label>
                <select name="admission_source">
                    <?php 
                    $sources = ["OPD", "Emergency", "Referral", "Transfer"];
                    foreach($sources as $s){
                        $sel = ($ipd && $ipd['AdmissionSource'] == $s) ? 'selected' : '';
                        echo "<option value='$s' $sel>$s</option>";
                    }
                    ?>
                </select>
                <br><br>
                
                <label>Status:</label>
                <select name="status">
                    <?php 
                    $statuses = ["Admitted", "Discharged"];
                    foreach($statuses as $s){
                        $sel = ($ipd && $ipd['Status'] == $s) ? 'selected' : '';
                        echo "<option value='$s' $sel>$s</option>";
                    }
                    ?>
                </select>
                <br><br>
                
                <input type="submit" name="<?php echo $id ? 'edit_ipd' : 'add_ipd'; ?>" value="Save">
            </form>
        </div>
    </div>
</body>
</html>
