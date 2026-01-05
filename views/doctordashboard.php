
<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once('../controllers/authCheck.php');
include __DIR__ . '/../controllers/logout_auto.php';
require_once('../controllers/DoctorDashboardDataController.php');

$csrfToken = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';

$appointments_mapped = [];
if (isset($appointments) && is_array($appointments)) {
    foreach ($appointments as $a) {
        $id   = isset($a['AppointmentID']) ? (int)$a['AppointmentID'] : (isset($a['id']) ? (int)$a['id'] : 0);
        $name = isset($a['PatientName']) ? $a['PatientName'] : (isset($a['patient_name']) ? $a['patient_name'] : '');
        $slot = isset($a['Slot']) ? $a['Slot'] : (isset($a['appointment_date']) ? $a['appointment_date'] : '');
        $purpose = isset($a['Purpose']) ? $a['Purpose'] : (isset($a['purpose']) ? $a['purpose'] : '');
        $appointment_date = ($slot !== '') ? date('Y-m-d H:i', strtotime($slot)) : '';
        $appointments_mapped[] = [
            'id'               => $id,
            'appointment_date' => $appointment_date,
            'patient_name'     => $name,
            'purpose'          => $purpose,
        ];
    }
}

$patients_mapped = [];
if (isset($patients) && is_array($patients)) {
    foreach ($patients as $p) {
        $pid     = isset($p['PatientID']) ? (int)$p['PatientID'] : (isset($p['id']) ? (int)$p['id'] : 0);
        $pname   = isset($p['Name']) ? $p['Name'] : (isset($p['name']) ? $p['name'] : '');
        $pcontact= isset($p['Contact']) ? $p['Contact'] : (isset($p['contact']) ? $p['contact'] : '');
        $patients_mapped[] = [
            'id'      => $pid,
            'name'    => $pname,
            'contact' => $pcontact,
        ];
    }
}

$appointments = $appointments_mapped;
$patients     = $patients_mapped;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Meditrust —Doctor</title>
    <link rel="stylesheet" href="../assets/style_doctordashboard.css">
    <link rel="stylesheet" href="../assets/style_layoutUser.css">
    <script src="../assets/sidebar.js"></script>
</head>
<body>

<?php include 'layoutDoctor.php'; ?>
<div class="main-content">
    <section class="hero-container">
        <h1>Doctor Dashboard</h1>
        <p class="hero-subtitle">Manage appointments, patients, and prescriptions</p>
    </section>

    <?php if (!empty($flash)): ?>
        <div class="alert">
            <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <section class="panel">
        <h2>Upcoming Appointments</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Purpose (editable)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="appointmentsBody">
            <?php foreach ($appointments as $a): ?>
                <tr data-id="<?= (int)$a['id'] ?>">
                    <td><?= htmlspecialchars($a['appointment_date']) ?></td>
                    <td><?= htmlspecialchars($a['patient_name']) ?></td>
                    <td>
                        <div class="editable" contenteditable="true" data-field="purpose">
                            <?= htmlspecialchars($a['purpose']) ?>
                        </div>
                    </td>
                    <td>
                        <form class="inline-form" action="../controllers/DoctorDashboardController.php" method="post">
                            <input type="hidden" name="action" value="update_appointment_purpose">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="hidden" name="appointment_id" value="<?= (int)$a['id'] ?>">
                            <input type="hidden" name="purpose" value="">
                            <button type="submit" class="btn small save-purpose-btn">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="panel">
        <h2>My Patients</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Contact (editable)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="patientsBody">
            <?php foreach ($patients as $p): ?>
                <tr data-id="<?= (int)$p['id'] ?>">
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td>
                        <div class="editable" contenteditable="true" data-field="contact">
                            <?= htmlspecialchars($p['contact']) ?>
                        </div>
                    </td>
                    <td>
                        <form class="inline-form" action="../controllers/DoctorDashboardController.php" method="post">
                            <input type="hidden" name="action" value="update_patient_contact">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="hidden" name="patient_id" value="<?= (int)$p['id'] ?>">
                            <input type="hidden" name="contact" value="">
                            <button type="submit" class="btn small save-contact-btn">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="panel">
        <h2>Upload Prescription</h2>
        <form id="prescriptionForm" class="form" action="../controllers/DoctorDashboardController.php" method="post">
            <input type="hidden" name="action" value="upload_prescription">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="form-row">
                <label for="patient_id">Patient</label>
                <select name="patient_id" id="patient_id" required>
                    <option value="">-- Select Patient --</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label for="medicine">Medicine</label>
                <input type="text" id="medicine" name="medicine" placeholder="e.g., Amoxicillin" required>
            </div>

            <div class="form-row">
                <label for="dosage">Dosage</label>
                <input type="text" id="dosage" name="dosage" placeholder="e.g., 500 mg twice daily" required>
            </div>

            <div class="form-row">
                <label for="duration">Duration</label>
                <input type="text" id="duration" name="duration" placeholder="e.g., 7 days" required>
            </div>

            <div class="form-row">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Additional instructions" required></textarea>
            </div>

            <button type="submit" class="btn">Upload Prescription</button>
        </form>
        <div id="formMessage" class="form-message" role="alert" aria-live="polite"></div>
    </section>

</div>

<?php include ('footer.php');?>

</body>
</html>