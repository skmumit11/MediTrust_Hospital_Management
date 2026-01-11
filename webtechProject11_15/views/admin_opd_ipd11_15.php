<?php
session_start();
require_once('../models/opdIpdModel11_15.php');

$opd_visits = getOPDVisits();
$ipd_admissions = getIPDAdmissions();

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'opd';
?>
<!DOCTYPE html>
<html lang="en">
<html>
<head>
    <title>OPD/IPD Management</title>
    <link rel="stylesheet" href="../assets/style_layoutUser11_15.css">
    <link rel="stylesheet" href="../assets/style_home11_15.css">
    <style>
        .tab-buttons { display: flex; justify-content: center; gap: 10px; margin-bottom: 20px; }
        .tab-btn { background: #dedede; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer; border-radius: 5px 5px 0 0; }
        .tab-btn.active { background: #386D44; color: white; }
        .tab-content { display: none; background: white; padding: 20px; border-radius: 8px; width: 100%; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .tab-content.active { display: block; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #386D44; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
    </style>
    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(div => div.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            document.getElementById('btn-'+tabName).classList.add('active');
        }
    </script>
    <script src="../assets/sidebar11_15.js"></script>
</head>
<body>
    <?php include 'layoutAdmin11_15.php'; ?>
    
    <div class="main-content">
        <div class="hero-container" style="height:auto; min-height: 600px; padding: 20px;">
            <h1>OPD & IPD Management</h1>
            <h3 class="hero-subtitle">Admin View</h3>

            <div class="tab-buttons">
                <button id="btn-opd" class="tab-btn <?php echo $active_tab=='opd'?'active':''; ?>" onclick="showTab('opd')">OPD Visits</button>
                <button id="btn-ipd" class="tab-btn <?php echo $active_tab=='ipd'?'active':''; ?>" onclick="showTab('ipd')">Inpatient (IPD)</button>
            </div>

            <!-- OPD TAB -->
            <div id="opd" class="tab-content <?php echo $active_tab=='opd'?'active':''; ?>">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h2 style="color:#386D44;">Outpatient List</h2>
                    <a href="admin_opd_form11_15.php" class="btn" style="background:#386D44; color:white; padding:10px; text-decoration:none;">+ Add Visit</a>
                </div>
                <table>
                    <tr>
                        <th>Doctor</th>
                        <th>Visit Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php if(!empty($opd_visits)){ 
                        foreach($opd_visits as $v){ ?>
                    <tr>
                        <td><?php echo $v['doctor_name']; ?></td>
                        <td><?php echo $v['VisitDate']; ?></td>
                        <td><?php echo $v['Status']; ?></td>
                        <td>
                            <a href="admin_opd_form11_15.php?id=<?php echo $v['OPDID']; ?>">Edit</a> | 
                            <a href="../controllers/opdIpdController11_15.php?delete_opd=1&id=<?php echo $v['OPDID']; ?>" onclick="return confirm('Delete this record?');">Delete</a>
                        </td>
                    </tr>
                    <?php }
                    } else { echo "<tr><td colspan='4'>No OPD records found.</td></tr>"; } ?>
                </table>
            </div>
            
            <!-- IPD TAB -->
            <div id="ipd" class="tab-content <?php echo $active_tab=='ipd'?'active':''; ?>">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h2 style="color:#386D44;">Inpatient List</h2>
                    <a href="admin_ipd_form11_15.php" class="btn" style="background:#386D44; color:white; padding:10px; text-decoration:none;">+ Add Admission</a>
                </div>
                <table>
                    <tr>
                        <th>Room No</th>
                        <th>Status</th>
                        <th>Admission Date</th>
                        <th>Discharge Date</th>
                        <th>Action</th>
                    </tr>
                    <?php if(!empty($ipd_admissions)){ 
                        foreach($ipd_admissions as $a){ ?>
                    <tr>
                        <td><?php echo $a['RoomNo']; ?></td>
                        <td><?php echo $a['Status']; ?></td>
                        <td><?php echo $a['AdmissionDate']; ?></td>
                        <td><?php echo $a['DischargeDate']; ?></td>
                        <td>
                            <a href="admin_ipd_form11_15.php?id=<?php echo $a['IPDID']; ?>">Edit</a> | 
                            <a href="../controllers/opdIpdController11_15.php?delete_ipd=1&id=<?php echo $a['IPDID']; ?>" onclick="return confirm('Delete this record?');">Delete</a>
                        </td>
                    </tr>
                    <?php }
                    } else { echo "<tr><td colspan='5'>No IPD records found.</td></tr>"; } ?>
                </table>
            </div>
        </div>
    </div>
    
    
</body>
</html>
