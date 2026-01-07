<?php
require_once('db11_15.php');

function createAppointment($patient_id, $doctor_id, $department_name, $date, $time) {
    $con = getConnection();
    $patient_id = (int)$patient_id;
    $doctor_id = (int)$doctor_id; // Now using ID
    
    // Resolve DepartmentID
    $department_name = mysqli_real_escape_string($con, $department_name);
    $dept_sql = "SELECT DepartmentID FROM Department WHERE Name='$department_name'";
    $dept_res = mysqli_query($con, $dept_sql);
    $dept_row = mysqli_fetch_assoc($dept_res);
    
    if(!$dept_row) return false;
    $department_id = $dept_row['DepartmentID'];
    
    // Create Slot DATETIME
    $slot = $date . ' ' . $time; // "YYYY-MM-DD HH:MM"
    $slot = mysqli_real_escape_string($con, $slot);
    
    // Insert into Appointment
    // Schema: PatientID, DoctorID, DepartmentID, Slot, Status
    // Note: No 'Reason' column in Appointment table provided in schema.
    $sql = "INSERT INTO Appointment (PatientID, DoctorID, DepartmentID, Slot, Status) VALUES ($patient_id, $doctor_id, $department_id, '$slot', 'Pending')";
    
    if(mysqli_query($con, $sql)){
        return true;
    } else {
        return false;
    }
}
?>
