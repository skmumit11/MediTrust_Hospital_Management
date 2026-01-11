<?php
require_once ("db.php") ;

function demoBeds() {
    return [
        ["BedID"=>1, "Type"=>"ICU", "Status"=>"Available"],
        ["BedID"=>2, "Type"=>"General", "Status"=>"Occupied"],
        ["BedID"=>3, "Type"=>"General", "Status"=>"Available"],
    ];
}



function createBed($type, $status) {
    global $USE_DEMO;
    if($USE_DEMO) { return true; }

    $con = getConnection();
    
    $type = trim($type);
    $status = trim($status);

    $sql = "INSERT INTO BedInventory (Type, Status) VALUES ('$type','$status')";
    $ok = mysqli_query($con, $sql);

    closeConnection($con);
    return $ok ? true : false;
}

//
function countBedsBy($type, $status)
{
    $conn = getConnection();
    $type = trim($type);
    $status = trim($status);
    
    $sql = "SELECT COUNT(*) AS c FROM BedInventory WHERE Type = '$type' AND Status = '$status'";
    $res = mysqli_query($conn, $sql);
    $c = 0;
    if ($res && ($row = mysqli_fetch_assoc($res))) { $c = (int)$row['c']; }
    closeConnection($conn);
    return $c;
}
//
function getAllBeds()
{
    $conn = getConnection();
    $res = mysqli_query($conn, "SELECT BedID, Type, Status FROM BedInventory ORDER BY BedID DESC");
    $rows = [];
    if ($res) { 
        while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; } 
    }
    closeConnection($conn);
    return $rows;
}
//
function getBedById($bedId)
{
    $conn = getConnection();
    $bedId = (int)$bedId;
    $sql = "SELECT BedID, Type, Status FROM BedInventory WHERE BedID = $bedId";
    $res = mysqli_query($conn, $sql);
    $row = null; if ($res) { $row = mysqli_fetch_assoc($res); }
    closeConnection($conn);
    return $row;
}
//
function addBed($type, $status)
{
    $conn = getConnection();
    $type = trim($type);
    $status = trim($status);
    
    $sql = "INSERT INTO BedInventory (Type, Status) VALUES ('$type', '$status')";
    mysqli_query($conn, $sql);
    $newId = mysqli_insert_id($conn);
    closeConnection($conn);
    return ($newId > 0) ? $newId : 0;
}
//
function updateBed($bedId, $type, $status)
{
    $conn = getConnection();
    $bedId = (int)$bedId;
    $type = trim($type);
    $status = trim($status);
    
    $sql = "UPDATE BedInventory SET Type = '$type', Status = '$status' WHERE BedID = $bedId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) >= 0);
    closeConnection($conn);
    return $ok;
}
//
function deleteBed($bedId)
{
    $conn = getConnection();
    $bedId = (int)$bedId;
    
    $sql = "DELETE FROM BedInventory WHERE BedID = $bedId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);
    closeConnection($conn);
    return $ok;
}
//
/* Bed allocation (track who’s using which bed) */
function getAllBedAllocations($limit = 200)
{
    $conn = getConnection();
    $limit = (int)$limit;
    
    $sql = "SELECT ba.AllocationID, ba.BedID, b.Type, ba.PatientID, p.Name AS PatientName,
                   ba.IPDID, ba.AllocatedAt, ba.ReleasedAt, ba.AllocationStatus, ba.AllocatedByUserID
            FROM BedAllocation ba
            JOIN BedInventory b ON b.BedID = ba.BedID
            JOIN Patient p ON p.PatientID = ba.PatientID
            ORDER BY ba.AllocatedAt DESC
            LIMIT $limit";
    $res = mysqli_query($conn, $sql);
    $rows = [];
    if($res){
        while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
    }
    closeConnection($conn);
    return $rows;
}
//
function allocateBed($bedId, $patientId, $allocatedByUserId, $ipdId = null)
{
    $conn = getConnection();
    $bedId = (int)$bedId;
    $patientId = (int)$patientId;
    $allocatedByUserId = (int)$allocatedByUserId;
    $ipd = ($ipdId === null) ? 'NULL' : (int)$ipdId;

    // Insert allocation
    $sql = "INSERT INTO BedAllocation
            (BedID, PatientID, IPDID, AllocatedAt, AllocationStatus, AllocatedByUserID)
            VALUES ($bedId, $patientId, $ipd, NOW(), 'Allocated', $allocatedByUserId)";
    
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);

    if ($ok) {
        // Mark bed occupied
        $upSql = "UPDATE BedInventory SET Status = 'Occupied' WHERE BedID = $bedId";
        mysqli_query($conn, $upSql);
    }
    closeConnection($conn);
    return $ok;
}
//
function releaseBedAllocation($allocationId)
{
    $conn = getConnection();
    $allocationId = (int)$allocationId;
    
    // Get bed for this allocation
    $selSql = "SELECT BedID FROM BedAllocation WHERE AllocationID = $allocationId";
    $r = mysqli_query($conn, $selSql);
    $row = $r ? mysqli_fetch_assoc($r) : null;
    
    if (!$row) { closeConnection($conn); return false; }
    $bedId = (int)$row['BedID'];

    // Release allocation
    $sql = "UPDATE BedAllocation SET ReleasedAt = NOW(), AllocationStatus = 'Released' WHERE AllocationID = $allocationId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);

    if ($ok) {
        // Mark bed available again
        $upSql = "UPDATE BedInventory SET Status = 'Available' WHERE BedID = $bedId";
        mysqli_query($conn, $upSql);
    }
    closeConnection($conn);
    return $ok;
}
