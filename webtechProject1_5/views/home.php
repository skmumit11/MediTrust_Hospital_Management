
<?php
require_once '../models/db.php';

$conn = getConnection();

$totalDoctors = 0;
$totalPatients = 0;
$todaysAppointments = 0;

$q1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Doctor");
if ($q1) {
    $row = mysqli_fetch_assoc($q1);
    $totalDoctors = (int)$row['total'];
}

$q2 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Patient");
if ($q2) {
    $row = mysqli_fetch_assoc($q2);
    $totalPatients = (int)$row['total'];
}

$q3 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Appointment WHERE DATE(Slot) = CURDATE()");
if ($q3) {
    $row = mysqli_fetch_assoc($q3);
    $todaysAppointments = (int)$row['total'];
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meditrust Hospital Management</title>

    <link rel="stylesheet" href="../assets/style_home.css">
    <link rel="stylesheet" href="../assets/style_layout.css">

    <script src="../assets/sidebar.js"></script>
</head>
<body>

<?php include 'layout.php'; ?>

<div class="main-content">
    <section class="hero-container">
        <h1>Welcome to Meditrust Hospital Management System</h1>
        <p class="hero-subtitle">
            Manage patients, appointments, doctors, and billing efficiently
        </p>
    </section>

    <section class="cta-container">
        <a class="btn" href="appointment_book.php">Book Appointment</a>
        <a class="btn" href="doctors.php">View Doctors</a>
        <a class="btn" href="services.php">Services</a>
    </section>

    <section class="dashboard">
        <div class="card">
            <h3>Total Doctors</h3>
            <p><?php echo $totalDoctors; ?></p>
        </div>

        <div class="card">
            <h3>Registered Patients</h3>
            <p><?php echo $totalPatients; ?></p>
        </div>

        <div class="card">
            <h3>Today's Appointments</h3>
            <p><?php echo $todaysAppointments; ?></p>
        </div>
    </section>

    <section class="services">
        <h2>Our Services</h2>
        <table class="services-table">
            <tr>
                <td>Emergency Care</td>
                <td>Online Appointments</td>
                <td>Pharmacy</td>
            </tr>
            <tr>
                <td>ICU Support</td>
                <td>Laboratory</td>
                <td></td>
            </tr>
        </table>
    </section>
</div>

<?php
include ('footer.php');
?>

</body>
</html>
