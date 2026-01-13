<?php
session_start();
require_once('../controllers/authCheck11_15.php');
require_once('../models/doctorModel11_15.php');

$doctors = getAllDoctors();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        h1{padding-top:50px;}
    </style>
    <title>Admin - Doctor Schedule</title>
    <link rel="stylesheet" href="../../webtechProject1_5/assets/style_layoutUser.css">
    <link rel="stylesheet" href="../../webtechProject1_5/assets/style_home.css">
    <script src='../../webtechProject1_5/assets/sidebar.js '></script>


</head>
<body>

<?php include 'layoutAdmin11_15.php'; ?>    
    <div class="container">
        <h1 >Doctor Duty Schedule Manager</h1>
        
        <?php if(isset($_SESSION['success'])){ echo "<p style='color:green'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>
        <?php if(isset($_SESSION['errors'])){ foreach($_SESSION['errors'] as $e) echo "<p style='color:red'>$e</p>"; unset($_SESSION['errors']); } ?>
        
        <table border="1" style="width:100%; text-align:left;">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Current Department</th>
                <th>Current Duty Hours</th>
                <th>Action</th>
            </tr>
            <?php if(!empty($doctors)){ 
                foreach($doctors as $d){ ?>
            <tr>
                <form action="../controllers/doctorController11_15.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $d['DoctorID']; ?>">
                    <td><?php echo $d['DoctorID']; ?></td>
                    <td><?php echo $d['fullname']; ?></td>
                    <td>
                        <select name="department">
                            <option value="">Select Dept</option>
                            <?php
                            $depts = ["Cardiology", "Neurology", "Orthopedics", "General Surgery", "Pediatrics", "ENT"];
                            foreach($depts as $dept){
                                $selected = (isset($d['department']) && $d['department'] == $dept) ? 'selected' : '';
                                echo "<option value='$dept' $selected>$dept</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select name="duty_hours">
                            <option value="">Select Shift</option>
                            <?php 
                            $shifts = ["9:00 AM - 5:00 PM", "2:00 PM - 10:00 PM", "10:00 PM - 6:00 AM"];
                            $current_duty = isset($d['Availability']) ? $d['Availability'] : '';
                            foreach($shifts as $shift){
                                $selected = ($current_duty == $shift) ? 'selected' : '';
                                echo "<option value='$shift' $selected>$shift</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <input type="submit" name="assign_duty" value="Assign Duty">
                    </td>
                </form>
            </tr>
            <?php }
            } else { echo "<tr><td colspan='5'>No doctors found.</td></tr>"; } ?>
        </table>
        
        <br>
        <a href="login11_15.php">Logout</a>
    </div>
</body>
</html>
