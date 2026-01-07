
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Services</title>

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
                <h2 class="page-title">Our Services</h2>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a class="btn btn-sm" href="appointment_book.php">Book Appointment</a>
                    <a class="btn btn-sm" href="doctors.php">View Doctors</a>
                </div>
            </div>

            <div style="margin-top: 10px;">
                <p style="margin: 0; color:#386D44; font-weight: 600;">
                    Meditrust provides modern healthcare services with efficient management.
                </p>
            </div>

            <div style="margin-top: 16px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Services Included</th>
                            <th>Availability</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Emergency</td>
                            <td>Emergency Care, Ambulance Request, Trauma Support</td>
                            <td>24/7</td>
                        </tr>
                        <tr>
                            <td>Appointments</td>
                            <td>Online Appointment Booking, Doctor Consultation, Follow-up</td>
                            <td>Daily</td>
                        </tr>
                        <tr>
                            <td>Inpatient (IPD)</td>
                            <td>Bed Allocation, Ward Management, Nursing Support</td>
                            <td>24/7</td>
                        </tr>
                        <tr>
                            <td>Outpatient (OPD)</td>
                            <td>OPD Records, Quick Checkup, Referral</td>
                            <td>Daily</td>
                        </tr>
                        <tr>
                            <td>Laboratory</td>
                            <td>Blood Tests, CBC, Reports & Results</td>
                            <td>Daily</td>
                        </tr>
                        <tr>
                            <td>Pharmacy</td>
                            <td>Medicine Inventory, Prescription Fulfillment</td>
                            <td>Daily</td>
                        </tr>
                        <tr>
                            <td>Billing</td>
                            <td>Bill Generation, VAT, Service Items</td>
                            <td>Daily</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-actions" style="margin-top: 14px;">
                <a class="btn" href="home.php">Back to Home</a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
