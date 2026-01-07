<?php
session_start();
require_once('../models/patientModel11_15.php');

if (!isset($_GET['id'])) {
    header("Location: nurse_patient_list11_15.php");
    exit();
}

$id = $_GET['id'];
$patient = getPatientById($id);

if (!$patient) {
    echo "Patient not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Patient - Nurse</title>
    <link rel="stylesheet" href="../assets/style_layoutUser.css">
</head>
<body>
    <?php include 'layout11_15.php'; ?>
    
    <div class="main-content">
        <div class="hero-container" style="height:auto; min-height: 500px;">
            <h1>Edit Patient Contact</h1>
            
            <div class="form-container">
                <?php
                if(isset($_SESSION['errors']) && !empty($_SESSION['errors'])){
                    foreach($_SESSION['errors'] as $error){
                        echo "<p style='color:red'>$error</p>";
                    }
                    unset($_SESSION['errors']);
                }
                ?>
                <form action="../controllers/patientController11_15.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $patient['PatientID']; ?>">
                    
                    <label>Name:</label>
                    <input type="text" value="<?php echo $patient['Name']; ?>" disabled>
                    
                    <label>Contact (Phone):</label>
                    <input type="text" name="contact" value="<?php echo $patient['Contact']; ?>">
                    
                    <label>Address:</label>
                    <textarea name="address" rows="4"><?php echo $patient['Address']; ?></textarea>
                    
                    <br><br>
                    <input type="submit" name="update_contact" value="Update" class="btn">
                    <a href="nurse_patient_list11_15.php" style="margin-left: 10px; color: #555;">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/sidebar.js"></script>
</body>
</html>
