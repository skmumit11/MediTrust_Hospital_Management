
<?php
require_once '../models/db.php';
$conn = getConnection();

$doctors = [];

$sql = "
    SELECT
        d.DoctorID,
        COALESCE(u.Name, CONCAT('Doctor#', d.DoctorID)) AS DoctorName,
        d.Specialty,
        d.Availability
    FROM Doctor d
    LEFT JOIN `User` u ON u.UserID = d.DoctorID
    ORDER BY d.DoctorID DESC
";

$res = mysqli_query($conn, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $doctors[] = $row;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctors</title>
    <link rel="stylesheet" href="../assets/style_layout.css">
    <link rel="stylesheet" href="../assets/style_home.css">
    <script src="../assets/sidebar.js"></script>
</head>
<body>

<?php include 'layout.php'; ?>

<div class="main-content">
    <div class="page-wrap">
        <div class="page-card">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
                <h2 class="page-title">Doctor List</h2>
                <a class="btn" href="appointment_book.php">Book Appointment</a>
            </div>

            <div style="margin-top: 10px;"></div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 90px;">ID</th>
                        <th>Name</th>
                        <th style="width: 220px;">Specialty</th>
                        <th style="width: 240px;">Availability</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($doctors) === 0) { ?>
                        <tr>
                            <td colspan="4">No doctors found.</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($doctors as $d) { ?>
                            <tr>
                                <td><?php echo (int)$d['DoctorID']; ?></td>
                                <td><?php echo htmlspecialchars($d['DoctorName']); ?></td>
                                <td><?php echo htmlspecialchars($d['Specialty']); ?></td>
                                <td><?php echo htmlspecialchars($d['Availability']); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>

            <div class="form-actions" style="margin-top:14px;">
                <a class="btn" href="home.php">Back to Home</a>
                <a class="btn" href="services.php">Services</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
