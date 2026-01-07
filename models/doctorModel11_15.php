<?php
require_once('db11_15.php');

function getAllDoctors() {
    $con = getConnection();
    // Join Doctor with User to get Name and exclude Admins
    $sql = "SELECT d.*, u.Name as fullname FROM Doctor d JOIN User u ON d.DoctorID = u.UserID WHERE u.Role = 'Doctor'"; 
    $result = mysqli_query($con, $sql);
    
    $doctors = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $doctors[] = $row;
        }
    }
    return $doctors;
}

function updateDoctorDuty($id, $department, $duty_hours) {
    $con = getConnection();
    $id = (int)$id;
    $department = mysqli_real_escape_string($con, $department);
    $duty_hours = mysqli_real_escape_string($con, $duty_hours);
    
    // Update Availability in Doctor table
    $sql = "UPDATE Doctor SET Availability='$duty_hours' WHERE DoctorID=$id";
    return mysqli_query($con, $sql);
}

function assignDoctorDuty($doctor_id, $department_name, $start_time, $end_time) {
    $con = getConnection();
    $doctor_id = (int)$doctor_id;
    $department_name = mysqli_real_escape_string($con, $department_name);
    
    $dept_sql = "SELECT DepartmentID FROM Department WHERE Name='$department_name'";
    $dept_res = mysqli_query($con, $dept_sql);
    $dept_row = mysqli_fetch_assoc($dept_res);
    
    if (!$dept_row) return false;
    $dept_id = $dept_row['DepartmentID'];
    
    // Insert into DutySchedule
    $sql = "INSERT INTO DutySchedule (DoctorID, DepartmentID, StartTime, EndTime) VALUES ($doctor_id, $dept_id, '$start_time', '$end_time')";
    return mysqli_query($con, $sql);
}

// Helper to get Department List
function getAllDepartments() {
    $con = getConnection();
    $sql = "SELECT * FROM Department";
    $result = mysqli_query($con, $sql);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}
?>
