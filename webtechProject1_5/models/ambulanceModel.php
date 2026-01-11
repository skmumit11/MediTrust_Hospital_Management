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
            WHERE RequestID={$id}";

    $res = mysqli_query($con, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    closeConnection($con);
    return $row;
}




function am_getPatientIdForUser($userId)
{
    $conn = getConnection();
    $userId = (int)$userId;
    $sql = "SELECT PatientID FROM Patient WHERE UserID = {$userId}";
    $res = mysqli_query($conn, $sql);
    $pid = null;
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $pid = (int)$row['PatientID'];
    }
    $conn->close();
    return $pid; // null if not linked
}

function am_createGuestContact($fullName, $phone, $email, $address)
{
    $conn = getConnection();
    
    $phoneEscaped = trim($phone);
    $checkSql = "SELECT GuestID FROM GuestContact WHERE Phone = '$phoneEscaped'";
    $cr = mysqli_query($conn, $checkSql);
    
    if ($cr && $row = mysqli_fetch_assoc($cr)) {
        $guestId = (int)$row['GuestID'];
        $conn->close();
        return $guestId;
    }

    $fullName = trim($fullName);
    $email = trim($email);
    $address = trim($address);

    $sql = "INSERT INTO GuestContact (FullName, Phone, Email, Address) VALUES ('$fullName', '$phoneEscaped', '$email', '$address')";
    mysqli_query($conn, $sql);
    $guestId = mysqli_insert_id($conn);
    $conn->close();
    return $guestId;
}

function am_insertAmbulanceRequest($patientId, $pickupLocation, $emergencyType, $status, $requesterUserId, $requesterGuestId, $patientName, $patientPhone)
{
    $conn = getConnection();
    
    // PatientID, RequesterUserID, RequesterGuestID can be null
    $pid = ($patientId === null) ? 'NULL' : (int)$patientId;
    $ruid = ($requesterUserId === null) ? 'NULL' : (int)$requesterUserId;
    $rgid = ($requesterGuestId === null) ? 'NULL' : (int)$requesterGuestId;
    
    $pickupLocation = trim($pickupLocation);
    $emergencyType = trim($emergencyType);
    $status = trim($status);
    $patientName = trim($patientName);
    $patientPhone = trim($patientPhone);

    $sql = "INSERT INTO AmbulanceRequest
            (PatientID, PickupLocation, EmergencyType, Status, RequesterUserID, RequesterGuestID, PatientName, PatientPhone, RequestedAt)
            VALUES ($pid, '$pickupLocation', '$emergencyType', '$status', $ruid, $rgid, '$patientName', '$patientPhone', NOW())";
            
    mysqli_query($conn, $sql);
    $newId = mysqli_insert_id($conn);
    $conn->close();
    return $newId;
}
//
function am_getMyAmbulanceRequests($username, $limit = 50)
{
    // Show requests made by this logged-in user (RequesterUserID) OR where PatientName matches their session name
    $conn = getConnection();
    $limit = (int)$limit;

    // Get user id for username
    $userId = null;
    $usernameEscaped = trim($username);
    $uqSql = "SELECT UserID FROM `User` WHERE Username = '$usernameEscaped'";
    $ur = mysqli_query($conn, $uqSql);
    
    if ($ur && $row = mysqli_fetch_assoc($ur)) {
        $userId = (int)$row['UserID'];
    }

    // Fetch recent requests
    if ($userId !== null) {
        $sql = "SELECT PatientName, PickupLocation, EmergencyType, Status, PatientPhone, RequestedAt
                FROM AmbulanceRequest
                WHERE RequesterUserID = $userId
                ORDER BY RequestedAt DESC
                LIMIT $limit";
    } else {
        // Fallback by PatientName (guest users using a name only)
        $pn = isset($_SESSION['name']) ? $_SESSION['name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown Patient');
        $pn = trim($pn);
        
        $sql = "SELECT PatientName, PickupLocation, EmergencyType, Status, PatientPhone, RequestedAt
                FROM AmbulanceRequest
                WHERE PatientName = '$pn'
                ORDER BY RequestedAt DESC
                LIMIT $limit";
    }

    $res = mysqli_query($conn, $sql);
    $rows = [];
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = $r;
        }
    }
    $conn->close();
    return $rows;
}




//
function getAllAmbulanceRequests($limit = 200)
{
    $conn = getConnection();
    $limit = (int)$limit;
    
    $sql = "SELECT ar.RequestID,
                   COALESCE(p.Name, ar.PatientName) AS Name,
                   COALESCE(p.Contact, ar.PatientPhone) AS Contact,
                   ar.PickupLocation, ar.EmergencyType, ar.Status, ar.RequestedAt
            FROM AmbulanceRequest ar
            LEFT JOIN Patient p ON p.PatientID = ar.PatientID
            ORDER BY ar.RequestedAt DESC
            LIMIT $limit";
            
    $res = mysqli_query($conn, $sql);
    $rows = [];
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
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
    }
    closeConnection($conn);
    return $rows;
}
//
function updateAmbulanceStatus($requestId, $newStatus)
{
    $conn = getConnection();
    $requestId = (int)$requestId;
    $newStatus = trim($newStatus);
    
    $sql = "UPDATE AmbulanceRequest SET Status = '$newStatus' WHERE RequestID = $requestId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);
    closeConnection($conn);
    return $ok;
}
//
function deleteAmbulanceRequest($requestId)
{
    $conn = getConnection();
    $requestId = (int)$requestId;
    
    $sql = "DELETE FROM AmbulanceRequest WHERE RequestID = $requestId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);
    closeConnection($conn);
    return $ok;
}

