<?php
session_start();
require_once('../models/opdIpdModel11_15.php');

$opd_visits = getOPDVisits();
$ipd_admissions = getIPDAdmissions();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Nurse - Patient Monitoring</title>
    <link rel="stylesheet" href="../assets/style_layoutUser.css">
    <link rel="stylesheet" href="../assets/style_home.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        th { background-color: #386D44; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        tr:hover { background-color: #f9f9f9; }
        textarea { width: 90%; padding: 5px; border: 1px solid #ddd; border-radius: 4px; }
        select { padding: 5px; border-radius: 4px; }
        input[type="submit"] { background: #386D44; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        input[type="submit"]:hover { background: #57A469; }
    </style>
</head>
<body>
    <?php include 'layout11_15.php'; ?>
    
    <div class="main-content">
        <div class="hero-container" style="height:auto; min-height: 600px; padding: 20px;">
            <h1>Patient Treatment Monitoring</h1>
            <h3 class="hero-subtitle">Nurse Dashboard</h3>
            
            <?php if(isset($_SESSION['success'])){ echo "<p style='color:green'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>
            <?php if(isset($_SESSION['errors'])){ foreach($_SESSION['errors'] as $e) echo "<p style='color:red'>$e</p>"; unset($_SESSION['errors']); } ?>
            
            <h2 style="color:#386D44; text-align:left; margin-top:30px;">OPD - Outpatient Details</h2>
            <table>
                <tr>
                    <th>Patient ID</th>
                    <th>Doctor</th>
                    <th>Visit Date</th>
                    <th>Doctor Notes</th>
                    <th>Action</th>
                </tr>
                <?php if(!empty($opd_visits)){ 
                    foreach($opd_visits as $v){ ?>
                <tr>
                    <form action="../controllers/opdIpdController.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $v['OPDID']; ?>">
                        <td><?php echo $v['PatientID']; ?></td>
                        <td><?php echo $v['doctor_name']; ?></td>
                        <td><?php echo $v['VisitDate']; ?></td>
                        <td>
                            <textarea name="note" rows="2" disabled><?php echo isset($v['doctor_notes']) ? $v['doctor_notes'] : ''; ?></textarea>
                        </td>
                        <td>
                            <!-- Notes update disabled as per schema limitation -->
                            <span style="color:#777;">Read Only</span>
                        </td>
                    </form>
                </tr>
                <?php }
                } else { echo "<tr><td colspan='5'>No OPD records found.</td></tr>"; } ?>
            </table>
            
            <h2 style="color:#386D44; text-align:left; margin-top:30px;">IPD - Inpatient Details</h2>
            <table>
                <tr>
                    <th>Room No</th>
                    <th>Admission Date</th>
                    <th>Doctor Notes</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php if(!empty($ipd_admissions)){ 
                    foreach($ipd_admissions as $a){ ?>
                <tr>
                    <form action="../controllers/opdIpdController.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $a['IPDID']; ?>">
                        <td><?php echo $a['RoomNo']; ?></td>
                        <td><?php echo $a['AdmissionDate']; ?></td>
                        <td>
                            <textarea name="note" rows="2" disabled><?php echo isset($a['doctor_notes']) ? $a['doctor_notes'] : ''; ?></textarea>
                        </td>
                        <td>
                            <select name="status">
                                <option value="Admitted" <?php if(isset($a['Status']) && $a['Status']=='Admitted') echo 'selected'; ?>>Admitted</option>
                                <option value="Under Treatment" <?php if(isset($a['Status']) && $a['Status']=='Under Treatment') echo 'selected'; ?>>Under Treatment</option>
                                <option value="Discharged" <?php if(isset($a['Status']) && $a['Status']=='Discharged') echo 'selected'; ?>>Discharged</option>
                            </select>
                        </td>
                        <td>
                            <input type="submit" name="update_ipd" value="Update">
                        </td>
                    </form>
                </tr>
                <?php }
                } else { echo "<tr><td colspan='5'>No IPD records found.</td></tr>"; } ?>
            </table>
        </div>
    </div>
    
    <script src="../assets/sidebar.js"></script>
</body>
</html>
