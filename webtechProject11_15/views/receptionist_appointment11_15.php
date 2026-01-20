<?php
session_start();
require_once('../models/doctorModel11_15.php');

$doctors = getAllDoctors();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <link rel="stylesheet" href="../assets/style_layoutUser.css">
    <link rel="stylesheet" href="../assets/style_home.css">
    <style>
         .form-container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; text-align: left;}
         label { font-weight: bold; color: #386D44; display: block; margin-top: 10px;}
         input[type="text"], input[type="date"], input[type="time"], select, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
         .error { color: red; margin-bottom: 15px; }
         .success { color: green; margin-bottom: 15px; }
    </style>
</head>
<body>
    <?php include 'layout11_15.php'; ?>
    
    <div class="main-content">
        <div class="hero-container" style="height:auto; min-height: 600px;">
            <h1>Book Appointment</h1>
            <h3 class="hero-subtitle">Receptionist Dashboard</h3>
            
            <div class="form-container">
            <?php
            if(isset($_SESSION['errors']) && !empty($_SESSION['errors'])){
                echo '<div class="error">';
                foreach($_SESSION['errors'] as $error){
                    echo "<p>$error</p>";
                }
                echo '</div>';
                unset($_SESSION['errors']);
            }
            if(isset($_SESSION['success'])){
                echo '<div class="success"><p>'.$_SESSION['success'].'</p></div>';
                unset($_SESSION['success']);
            }
            ?>
            
            <form action="../controllers/appointmentController11_15.php" method="post">
                <label>Patient ID:</label>
                <input type="text" name="patient_id" required placeholder="Enter Patient ID"><br>
                
                <label>Doctor:</label>
                <select name="doctor_id" required> <!-- Updated to doctor_id based on prev fix -->
                    <option value="">Select Doctor</option>
                    <?php foreach($doctors as $doc){ ?>
                        <option value="<?php echo $doc['DoctorID']; ?>"><?php echo $doc['fullname']; ?></option>
                    <?php } ?>
                </select><br>
                
                <label>Department:</label>
                <select name="department" required>
                    <option value="">Select Department</option>
                    <option value="Cardiology">Cardiology</option>
                    <option value="Neurology">Neurology</option>
                    <option value="Orthopedics">Orthopedics</option>
                    <option value="Pediatrics">Pediatrics</option>
                </select><br>
                
                <label>Date:</label>
                <input type="date" name="date" required><br>
                
                <label>Time:</label>
                <input type="time" name="time" required><br>
                
                <br>
                <input type="submit" name="book_appointment" value="Book Appointment" class="btn" style="width:100%">
            </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/sidebar.js"></script>
</body>
</html>
