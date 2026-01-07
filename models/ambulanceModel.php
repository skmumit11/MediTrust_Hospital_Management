<?php
require_once ('db.php');

function demoAmbulanceRequests() {
    return [
        ["RequestID"=>1, "Name"=>"Unknown Patient", "Contact"=>"+8801913009999", "PickupLocation"=>"Siddhirganj, Narayanganj", "EmergencyType"=>"Road Accident", "Status"=>"Pending"],
        ["RequestID"=>2, "Name"=>"Mariam Khatun", "Contact"=>"+8801711001001", "PickupLocation"=>"Savar, Dhaka", "EmergencyType"=>"Breathing Trouble", "Status"=>"Accepted"],
    ];
}



function getAmbulanceById($id) {
    global $USE_DEMO;
    $id = (int)$id;

    if($USE_DEMO) {
        foreach(demoAmbulanceRequests() as $a) {
            if((int)$a["RequestID"] === $id) { return $a; }
        }
        return null;
    }

    $con = getConnection();

    $sql = "SELECT RequestID, PatientID, PickupLocation, EmergencyType, Status, PatientName, PatientPhone
            FROM AmbulanceRequest
            WHERE RequestID=?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    mysqli_stmt_close($stmt);
    closeConnection($con);
    return $row;
}




function am_getPatientIdForUser($userId)
{
    $conn = getDBConnection();
    $sql = "SELECT PatientID FROM Patient WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $pid = null;
    if ($row = $res->fetch_assoc()) {
        $pid = (int)$row['PatientID'];
    }
    $stmt->close();
    $conn->close();
    return $pid; // null if not linked
}

function am_createGuestContact($fullName, $phone, $email, $address)
{
    $conn = getDBConnection();
    // Try to reuse existing guest by phone
    $check = $conn->prepare("SELECT GuestID FROM GuestContact WHERE Phone = ?");
    $check->bind_param('s', $phone);
    $check->execute();
    $cr = $check->get_result();
    if ($row = $cr->fetch_assoc()) {
        $guestId = (int)$row['GuestID'];
        $check->close();
        $conn->close();
        return $guestId;
    }
    $check->close();

    $sql = "INSERT INTO GuestContact (FullName, Phone, Email, Address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $fullName, $phone, $email, $address);
    $stmt->execute();
    $guestId = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    return $guestId;
}

function am_insertAmbulanceRequest($patientId, $pickupLocation, $emergencyType, $status, $requesterUserId, $requesterGuestId, $patientName, $patientPhone)
{
    $conn = getDBConnection();
    $sql = "INSERT INTO AmbulanceRequest
            (PatientID, PickupLocation, EmergencyType, Status, RequesterUserID, RequesterGuestID, PatientName, PatientPhone, RequestedAt)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    // PatientID, RequesterUserID, RequesterGuestID can be null
    $pid = ($patientId === null) ? null : (int)$patientId;
    $ruid = ($requesterUserId === null) ? null : (int)$requesterUserId;
    $rgid = ($requesterGuestId === null) ? null : (int)$requesterGuestId;

    $stmt->bind_param(
        'isssiiss',
        $pid,
        $pickupLocation,
        $emergencyType,
        $status,
        $ruid,
        $rgid,
        $patientName,
        $patientPhone
    );
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    return $newId;
}
//
function am_getMyAmbulanceRequests($username, $limit = 50)
{
    // Show requests made by this logged-in user (RequesterUserID) OR where PatientName matches their session name
    $conn = getDBConnection();

    // Get user id for username
    $userId = null;
    $uq = $conn->prepare("SELECT UserID FROM `User` WHERE Username = ?");
    $uq->bind_param('s', $username);
    $uq->execute();
    $ur = $uq->get_result();
    if ($row = $ur->fetch_assoc()) {
        $userId = (int)$row['UserID'];
    }
    $uq->close();

    // Fetch recent requests
    if ($userId !== null) {
        $sql = "SELECT PatientName, PickupLocation, EmergencyType, Status, PatientPhone, RequestedAt
                FROM AmbulanceRequest
                WHERE RequesterUserID = ?
                ORDER BY RequestedAt DESC
                LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $userId, $limit);
    } else {
        // Fallback by PatientName (guest users using a name only)
        $sql = "SELECT PatientName, PickupLocation, EmergencyType, Status, PatientPhone, RequestedAt
                FROM AmbulanceRequest
                WHERE PatientName = ?
                ORDER BY RequestedAt DESC
                LIMIT ?";
        $stmt = $conn->prepare($sql);
        $pn = isset($_SESSION['name']) ? $_SESSION['name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown Patient');
        $stmt->bind_param('si', $pn, $limit);
    }

    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    $stmt->close();
    $conn->close();
    return $rows;
}




//
function getAllAmbulanceRequests($limit = 200)
{
    $conn = getConnection();
    $sql = "SELECT ar.RequestID,
                   COALESCE(p.Name, ar.PatientName) AS Name,
                   COALESCE(p.Contact, ar.PatientPhone) AS Contact,
                   ar.PickupLocation, ar.EmergencyType, ar.Status, ar.RequestedAt
            FROM AmbulanceRequest ar
            LEFT JOIN Patient p ON p.PatientID = ar.PatientID
            ORDER BY ar.RequestedAt DESC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = [
            'RequestID'      => (int)$r['RequestID'],
            'Name'           => $r['Name'],
            'Contact'        => $r['Contact'],
            'PickupLocation' => $r['PickupLocation'],
            'Status'         => $r['Status'],
            'EmergencyType'  => $r['EmergencyType'],
            'RequestedAt'    => $r['RequestedAt'],
        ];
    }
    $stmt->close();
    closeConnection($conn);
    return $rows;
}
//
function updateAmbulanceStatus($requestId, $newStatus)
{
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE AmbulanceRequest SET Status = ? WHERE RequestID = ?");
    $stmt->bind_param('si', $newStatus, $requestId);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}
//
function deleteAmbulanceRequest($requestId)
{
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM AmbulanceRequest WHERE RequestID = ?");
    $stmt->bind_param('i', $requestId);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}

