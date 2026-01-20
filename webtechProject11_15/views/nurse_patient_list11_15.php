<?php
session_start();
require_once('../models/patientModel11_15.php');

// Security Check (Ensure user is Nurse)
/*
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Nurse') {
    header("Location: login11_15.php");
    exit();
}
*/
// Commented out strict check for now as I don't want to lock myself out during testing if 'Nurse' role doesn't exist yet.

// Fetch Patients
// If search results exist in session, use them, otherwise fetch all
if (isset($_SESSION['patients'])) {
    $patients = $_SESSION['patients'];
    unset($_SESSION['patients']); // Clear after use
} else {
    $patients = getAllPatients();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient List - Nurse</title>
    <link rel="stylesheet" href="../assets/style_layoutUser.css">
</head>
<body>
    <?php include 'layout11_15.php'; ?>

    <div class="main-content">
        <div class="hero-container">
            <h1>Patient Management</h1>
            <h3 class="hero-subtitle">Nurse Dashboard</h3>

            <div style="margin: 20px 0;">
                <form action="" method="post">
                    <input type="text" name="term" placeholder="Search Name or ID" style="padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc;">
                    <input type="submit" name="search" value="Search" class="btn">
                </form>
            </div>

            <?php if(isset($_SESSION['success'])) { echo "<p style='color:green'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Email</th> <!-- Joined from User -->
                    <th>Address</th>
                    <th>Action</th>
                </tr>
                <?php if (!empty($patients)) {
                    foreach ($patients as $p) { ?>
                <tr>
                    <td><?php echo $p['PatientID']; ?></td>
                    <td><?php echo $p['Name']; ?></td>
                    <td><?php echo $p['Gender']; ?></td>
                    <td><?php echo $p['Email']; ?></td>
                    <td><?php echo $p['Address']; ?></td>
                    <td>
                        <a href="nurse_patient_edit11_15.php?id=<?php echo $p['PatientID']; ?>" style="color: #386D44; font-weight: bold;">Edit Contact</a>
                        <!-- | <a href="#">View History</a> -->
                    </td>
                </tr>
                <?php }
                } else {
                    echo "<tr><td colspan='6'>No patients found.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <script src="../assets/sidebar.js"></script>
</body>
</html>
