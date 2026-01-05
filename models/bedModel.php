
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

    $sql = "INSERT INTO BedInventory (Type, Status) VALUES (?,?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $type, $status);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    closeConnection($con);
    return $ok ? true : false;
}

//
function countBedsBy($type, $status)
{
    $conn = getConnection();
    $sql = "SELECT COUNT(*) AS c FROM BedInventory WHERE Type = ? AND Status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $type, $status);
    $stmt->execute();
    $res = $stmt->get_result();
    $c = 0;
    if ($res && ($row = $res->fetch_assoc())) { $c = (int)$row['c']; }
    $stmt->close();
    closeConnection($conn);
    return $c;
}
//
function getAllBeds()
{
    $conn = getConnection();
    $res = $conn->query("SELECT BedID, Type, Status FROM BedInventory ORDER BY BedID DESC");
    $rows = [];
    if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } $res->free(); }
    closeConnection($conn);
    return $rows;
}
//
function getBedById($bedId)
{
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT BedID, Type, Status FROM BedInventory WHERE BedID = ?");
    $stmt->bind_param('i', $bedId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = null; if ($res) { $row = $res->fetch_assoc(); }
    $stmt->close();
    closeConnection($conn);
    return $row;
}
//
function addBed($type, $status)
{
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO BedInventory (Type, Status) VALUES (?, ?)");
    $stmt->bind_param('ss', $type, $status);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    return ($newId > 0) ? $newId : 0;
}
//
function updateBed($bedId, $type, $status)
{
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE BedInventory SET Type = ?, Status = ? WHERE BedID = ?");
    $stmt->bind_param('ssi', $type, $status, $bedId);
    $stmt->execute();
    $ok = ($stmt->affected_rows >= 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}
//
function deleteBed($bedId)
{
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM BedInventory WHERE BedID = ?");
    $stmt->bind_param('i', $bedId);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}
//
/* Bed allocation (track who’s using which bed) */
function getAllBedAllocations($limit = 200)
{
    $conn = getConnection();
    $sql = "SELECT ba.AllocationID, ba.BedID, b.Type, ba.PatientID, p.Name AS PatientName,
                   ba.IPDID, ba.AllocatedAt, ba.ReleasedAt, ba.AllocationStatus, ba.AllocatedByUserID
            FROM BedAllocation ba
            JOIN BedInventory b ON b.BedID = ba.BedID
            JOIN Patient p ON p.PatientID = ba.PatientID
            ORDER BY ba.AllocatedAt DESC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
    $stmt->close();
    closeConnection($conn);
    return $rows;
}
//
function allocateBed($bedId, $patientId, $allocatedByUserId, $ipdId = null)
{
    $conn = getConnection();
    // Insert allocation
    $sql = "INSERT INTO BedAllocation
            (BedID, PatientID, IPDID, AllocatedAt, AllocationStatus, AllocatedByUserID)
            VALUES (?, ?, ?, NOW(), 'Allocated', ?)";
    $stmt = $conn->prepare($sql);
    $ipd = ($ipdId === null) ? null : (int)$ipdId;
    $stmt->bind_param('iiii', $bedId, $patientId, $ipd, $allocatedByUserId);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();

    if ($ok) {
        // Mark bed occupied
        $up = $conn->prepare("UPDATE BedInventory SET Status = 'Occupied' WHERE BedID = ?");
        $up->bind_param('i', $bedId);
        $up->execute();
        $up->close();
    }
    closeConnection($conn);
    return $ok;
}
//
function releaseBedAllocation($allocationId)
{
    $conn = getConnection();
    // Get bed for this allocation
    $sel = $conn->prepare("SELECT BedID FROM BedAllocation WHERE AllocationID = ?");
    $sel->bind_param('i', $allocationId);
    $sel->execute();
    $r = $sel->get_result();
    $row = $r->fetch_assoc();
    $sel->close();
    if (!$row) { closeConnection($conn); return false; }
    $bedId = (int)$row['BedID'];

    // Release allocation
    $stmt = $conn->prepare("UPDATE BedAllocation SET ReleasedAt = NOW(), AllocationStatus = 'Released' WHERE AllocationID = ?");
    $stmt->bind_param('i', $allocationId);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();

    if ($ok) {
        // Mark bed available again
        $up = $conn->prepare("UPDATE BedInventory SET Status = 'Available' WHERE BedID = ?");
        $up->bind_param('i', $bedId);
        $up->execute();
        $up->close();
    }
    closeConnection($conn);
    return $ok;
}
