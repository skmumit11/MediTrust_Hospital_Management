
<?php
require_once ('db.php');

function getPatientIdByUserId($userID){
    $con = getConnection();

    $sql = "SELECT PatientID FROM Patient WHERE UserID = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    if(!$stmt){
        mysqli_close($con);
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $patientID = 0;
    if($res && mysqli_num_rows($res) === 1){
        $row = mysqli_fetch_assoc($res);
        $patientID = (int)$row['PatientID'];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $patientID;
}

function getUpcomingAppointments($patientID){
    $con = getConnection();

    $sql = "
        SELECT
            a.AppointmentID,
            a.Slot,
            a.Status,
            COALESCE(u.Name, CONCAT('Doctor#', a.DoctorID)) AS DoctorName
        FROM Appointment a
        LEFT JOIN `User` u ON u.UserID = a.DoctorID
        WHERE a.PatientID = ?
          AND a.Slot >= NOW()
        ORDER BY a.Slot ASC
        LIMIT 20
    ";

    $stmt = mysqli_prepare($con, $sql);
    if(!$stmt){
        mysqli_close($con);
        return [];
    }

    mysqli_stmt_bind_param($stmt, "i", $patientID);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $data = [];
    if($res){
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $data;
}

function getAllDoctorsList(){
    $con = getConnection();

    $sql = "
        SELECT
            d.DoctorID,
            COALESCE(u.Name, CONCAT('Doctor#', d.DoctorID)) AS Name,
            d.Specialty
        FROM Doctor d
        LEFT JOIN `User` u ON u.UserID = d.DoctorID
        ORDER BY Name ASC
        LIMIT 200
    ";

    $res = mysqli_query($con, $sql);
    $data = [];

    if($res){
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }
    }

    mysqli_close($con);
    return $data;
}

function getBedStatus(){
    $con = getConnection();

    $sql = "
        SELECT Type, COUNT(*) AS total
        FROM BedInventory
        WHERE Status = 'Available'
        GROUP BY Type
    ";

    $res = mysqli_query($con, $sql);

    $status = [
        'ICU' => 0,
        'General' => 0
    ];

    if($res){
        while($row = mysqli_fetch_assoc($res)){
            $type = $row['Type'];
            $status[$type] = (int)$row['total'];
        }
    }

    mysqli_close($con);
    return $status;
}

function getAvailableBeds(){
    $con = getConnection();

    $sql = "SELECT COUNT(*) AS total FROM BedInventory WHERE Status='Available'";
    $res = mysqli_query($con, $sql);

    $count = 0;
    if($res){
        $row = mysqli_fetch_assoc($res);
        $count = (int)$row['total'];
    }

    mysqli_close($con);
    return $count;
}

function getMedicalHistory($patientID){
    $con = getConnection();

    $sql = "
        SELECT
            e.EncounterID,
            e.DiagnosisICD,
            e.Vitals,
            COALESCE(doc.Name, CONCAT('Doctor#', e.DoctorID)) AS DoctorName,
            GROUP_CONCAT(CONCAT(p.Medicine, ' ', p.Dosage, ' (', p.Duration, ')') SEPARATOR ', ') AS Prescription
        FROM Encounter e
        LEFT JOIN `User` doc ON doc.UserID = e.DoctorID
        LEFT JOIN Prescription p ON p.EncounterID = e.EncounterID
        WHERE e.PatientID = ?
        GROUP BY e.EncounterID, e.DiagnosisICD, e.Vitals, DoctorName
        ORDER BY e.EncounterID DESC
        LIMIT 50
    ";

    $stmt = mysqli_prepare($con, $sql);
    if(!$stmt){
        mysqli_close($con);
        return [];
    }

    mysqli_stmt_bind_param($stmt, "i", $patientID);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $data = [];
    if($res){
        while($row = mysqli_fetch_assoc($res)){
            if($row['Prescription'] === null) $row['Prescription'] = '';
            $data[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $data;
}

/* NEW: Get requests for the logged-in user (works even without PatientID linked) */
function getAmbulanceRequestsForUser($requesterUserID, $patientID){
    $con = getConnection();

    if($patientID > 0){
        $sql = "
            SELECT RequestID, PickupLocation, EmergencyType, PatientName, PatientPhone, Status, RequestedAt
            FROM AmbulanceRequest
            WHERE (RequesterUserID = ? OR PatientID = ?)
            ORDER BY RequestedAt DESC
            LIMIT 30
        ";
        $stmt = mysqli_prepare($con, $sql);
        if(!$stmt){
            mysqli_close($con);
            return [];
        }

        mysqli_stmt_bind_param($stmt, "ii", $requesterUserID, $patientID);
    } else {
        $sql = "
            SELECT RequestID, PickupLocation, EmergencyType, PatientName, PatientPhone, Status, RequestedAt
            FROM AmbulanceRequest
            WHERE RequesterUserID = ?
            ORDER BY RequestedAt DESC
            LIMIT 30
        ";
        $stmt = mysqli_prepare($con, $sql);
        if(!$stmt){
            mysqli_close($con);
            return [];
        }

        mysqli_stmt_bind_param($stmt, "i", $requesterUserID);
    }

    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $data = [];
    if($res){
        while($row = mysqli_fetch_assoc($res)){
            $data[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $data;
}

/*
  NEW: Create ambulance request for ANY logged-in user.
  - If patient profile exists -> store PatientID too
  - If not -> PatientID stays NULL, still valid request
*/
function createAmbulanceRequestForUser($requesterUserID, $patientID, $patientName, $patientPhone, $pickupLocation, $emergencyType){
    $con = getConnection();

    if($patientID > 0){
        $sql = "
            INSERT INTO AmbulanceRequest
            (PatientID, PickupLocation, EmergencyType, Status, RequesterUserID, PatientName, PatientPhone, RequestedAt)
            VALUES (?, ?, ?, 'Pending', ?, ?, ?, NOW())
        ";
        $stmt = mysqli_prepare($con, $sql);
        if(!$stmt){
            mysqli_close($con);
            return false;
        }

        mysqli_stmt_bind_param($stmt, "ississ", $patientID, $pickupLocation, $emergencyType, $requesterUserID, $patientName, $patientPhone);
    } else {
        $sql = "
            INSERT INTO AmbulanceRequest
            (PatientID, PickupLocation, EmergencyType, Status, RequesterUserID, PatientName, PatientPhone, RequestedAt)
            VALUES (NULL, ?, ?, 'Pending', ?, ?, ?, NOW())
        ";
        $stmt = mysqli_prepare($con, $sql);
        if(!$stmt){
            mysqli_close($con);
            return false;
        }

        mysqli_stmt_bind_param($stmt, "ssiss", $pickupLocation, $emergencyType, $requesterUserID, $patientName, $patientPhone);
    }

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $ok;
}
?>
