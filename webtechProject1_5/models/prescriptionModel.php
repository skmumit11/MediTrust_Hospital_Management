
<?php
require_once ('db.php');

function getPatientIdByUserId($userID){
    $con = getConnection();
    $userID = (int)$userID;
    
    $sql = "SELECT PatientID FROM Patient WHERE UserID = $userID LIMIT 1";
    $res = mysqli_query($con, $sql);

    $patientID = 0;
    if($res && mysqli_num_rows($res) === 1){
        $row = mysqli_fetch_assoc($res);
        $patientID = (int)$row['PatientID'];
    }

    mysqli_close($con);
    return $patientID;
}

function getUpcomingAppointments($patientID){
    $con = getConnection();
    $patientID = (int)$patientID;

    $sql = "
        SELECT
            a.AppointmentID,
            a.Slot,
            a.Status,
            COALESCE(u.Name, CONCAT('Doctor#', a.DoctorID)) AS DoctorName
        FROM Appointment a
        LEFT JOIN `User` u ON u.UserID = a.DoctorID
        WHERE a.PatientID = $patientID
          AND a.Slot >= NOW()
        ORDER BY a.Slot ASC
        LIMIT 20
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
    $patientID = (int)$patientID;

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
        WHERE e.PatientID = $patientID
        GROUP BY e.EncounterID, e.DiagnosisICD, e.Vitals, DoctorName
        ORDER BY e.EncounterID DESC
        LIMIT 50
    ";

    $res = mysqli_query($con, $sql);

    $data = [];
    if($res){
        while($row = mysqli_fetch_assoc($res)){
            if($row['Prescription'] === null) $row['Prescription'] = '';
            $data[] = $row;
        }
    }

    mysqli_close($con);
    return $data;
}

function getAmbulanceRequests($patientID){
    $con = getConnection();
    $patientID = (int)$patientID;

    $sql = "
        SELECT RequestID, PickupLocation, EmergencyType, PatientPhone, Status, RequestedAt
        FROM AmbulanceRequest
        WHERE PatientID = $patientID
        ORDER BY RequestedAt DESC
        LIMIT 20
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

/* UPDATED: now also stores PatientPhone + dropdown emergency type still goes to EmergencyType */
function createAmbulanceRequest($patientID, $pickupLocation, $emergencyType, $contactPhone){
    $con = getConnection();
    $patientID = (int)$patientID;
    $pickupLocation = trim($pickupLocation);
    $emergencyType = trim($emergencyType);
    $contactPhone = trim($contactPhone);

    $sql = "
        INSERT INTO AmbulanceRequest
        (PatientID, PickupLocation, EmergencyType, PatientPhone, Status, RequestedAt)
        VALUES ($patientID, '$pickupLocation', '$emergencyType', '$contactPhone', 'Pending', NOW())
    ";

    $ok = mysqli_query($con, $sql);

    mysqli_close($con);

    return (bool)$ok;
}
?>
