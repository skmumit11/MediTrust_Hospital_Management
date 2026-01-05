
<?php
session_start();
require_once __DIR__ . '/../controllers/patientdashboardController.php';

$prefillName = '';
if (isset($_SESSION['name']) && $_SESSION['name'] !== '') {
    $prefillName = $_SESSION['name'];
} elseif (isset($_SESSION['username']) && $_SESSION['username'] !== '') {
    $prefillName = $_SESSION['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MediTrust Patient Dashboard</title>
    <link rel="stylesheet" href="../assets/style_patientdashboard.css">
    <link rel="stylesheet" href="../assets/style_layoutUser.css">
    <script src="../assets/sidebar.js"></script>
</head>
<body>

<?php include 'layoutPatient.php'; ?>

<div class="main-content">

    <section class="hero-container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>!</h1>
        <p>Your personalized dashboard for appointments, doctors, and medical history.</p>
    </section>

    <?php if ($message !== "") { ?>
        <div style="margin: 12px 0; padding: 10px; border-radius: 8px; border: 1px solid #ccc; background: #f9f9f9;">
            <strong><?php echo ($messageType === "success") ? "Success:" : "Error:"; ?></strong>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <section class="dashboard">
        <div class="card">
            <h3>Upcoming Appointments</h3>
            <p><?php echo count($upcomingAppointments); ?></p>
        </div>
        <div class="card">
            <h3>Doctors Registered</h3>
            <p><?php echo count($doctorsList); ?></p>
        </div>
        <div class="card">
            <h3>Available Beds</h3>
            <p><?php echo (int)$availableBeds; ?></p>
        </div>
    </section>

    <section class="dashboard-section">
        <h2>Upcoming Appointments</h2>
        <table>
            <thead>
                <tr>
                    <th>Doctor Name</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($upcomingAppointments) === 0) { ?>
                <tr><td colspan="3">No upcoming appointments.</td></tr>
            <?php } else { ?>
                <?php foreach($upcomingAppointments as $appt): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appt['DoctorName']); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($appt['Slot'])); ?></td>
                        <td><?php echo htmlspecialchars($appt['Status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php } ?>
            </tbody>
        </table>
    </section>

    <section class="dashboard-section">
        <h2>Doctors List</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Specialty</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($doctorsList) === 0) { ?>
                <tr><td colspan="2">No doctors found.</td></tr>
            <?php } else { ?>
                <?php foreach($doctorsList as $doc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doc['Name']); ?></td>
                        <td><?php echo htmlspecialchars($doc['Specialty']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php } ?>
            </tbody>
        </table>
    </section>

    <section class="bed-status">
        <span>ICU Beds Available: <?php echo (int)$bedStatus['ICU']; ?></span>
        <span>General Beds Available: <?php echo (int)$bedStatus['General']; ?></span>
    </section>

    <!-- UPDATED: Everyone can request ambulance (even if PatientID not linked) -->
    <section class="dashboard-section">
        <h2>Request Ambulance</h2>

        <form method="POST" action="">
            <input type="text" name="patient_name" placeholder="Patient Name" value="<?php echo htmlspecialchars($prefillName); ?>">
            <input type="text" name="contact_phone" placeholder="Contact Number">
            <input type="text" name="pickup_location" placeholder="Pickup Location">

            <select name="emergency_type">
                <option value="">-- Select Request Type --</option>
                <option value="Road Accident">Road Accident</option>
                <option value="Breathing Trouble">Breathing Trouble</option>
                <option value="Heart Attack">Heart Attack</option>
                <option value="Stroke">Stroke</option>
                <option value="Pregnancy Emergency">Pregnancy Emergency</option>
                <option value="Burn/Fire Injury">Burn/Fire Injury</option>
                <option value="Other">Other</option>
            </select>

            <button type="submit" name="request_ambulance">Request Ambulance</button>
        </form>
    </section>

    <section class="dashboard-section">
        <h2>Your Ambulance Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Pickup Location</th>
                    <th>Request Type</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Requested At</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($ambulanceRequests) === 0) { ?>
                <tr><td colspan="6">No ambulance requests found.</td></tr>
            <?php } else { ?>
                <?php foreach($ambulanceRequests as $ar): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ar['PatientName']); ?></td>
                        <td><?php echo htmlspecialchars($ar['PickupLocation']); ?></td>
                        <td><?php echo htmlspecialchars($ar['EmergencyType']); ?></td>
                        <td><?php echo htmlspecialchars($ar['PatientPhone']); ?></td>
                        <td><?php echo htmlspecialchars($ar['Status']); ?></td>
                        <td><?php echo htmlspecialchars($ar['RequestedAt']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php } ?>
            </tbody>
        </table>
    </section>

    <section class="dashboard-section">
        <h2>Medical History</h2>
        <table>
            <thead>
                <tr>
                    <th>Encounter #</th>
                    <th>Doctor</th>
                    <th>Diagnosis</th>
                    <th>Vitals</th>
                    <th>Prescription</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($medicalHistory) === 0) { ?>
                <tr><td colspan="5">No medical history found.</td></tr>
            <?php } else { ?>
                <?php foreach($medicalHistory as $history): ?>
                    <tr>
                        <td><?php echo (int)$history['EncounterID']; ?></td>
                        <td><?php echo htmlspecialchars($history['DoctorName']); ?></td>
                        <td><?php echo htmlspecialchars($history['DiagnosisICD']); ?></td>
                        <td><?php echo htmlspecialchars($history['Vitals']); ?></td>
                        <td><?php echo htmlspecialchars($history['Prescription']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php } ?>
            </tbody>
        </table>
    </section>

    <section class="ai-chatbox">
        <h2>AI Assistant</h2>
        <textarea placeholder="Type your message here..."></textarea>
        <button type="button">Send</button>
    </section>

</div>

<?php include('footer.php'); ?>

</body>
</html>
