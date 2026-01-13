
<?php
require_once "db.php";

function getAllAvailableBeds($typeOptional = "") {
    $con = getConnection();
    
    $typeOptional = trim($typeOptional);

    if($typeOptional === "ICU" || $typeOptional === "General") {
        $sql = "SELECT BedID, Type, Status FROM BedInventory
                WHERE Status='Available' AND Type='$typeOptional'
                ORDER BY BedID ASC";
        $res = mysqli_query($con, $sql);
        $rows = [];
        if($res) {
            while($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
        }
        closeConnection($con);
        return $rows;
    }

    $sql = "SELECT BedID, Type, Status FROM BedInventory
            WHERE Status='Available'
            ORDER BY BedID ASC";
    $res = mysqli_query($con, $sql);

    $rows = [];
    if($res) {
        while($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
    }
    closeConnection($con);
    return $rows;
}

function getActiveAllocationByPatient($patientId) {
    $patientId = (int)$patientId;
    $con = getConnection();

    $sql = "SELECT AllocationID, BedID, PatientID, IPDID, AllocatedAt, ReleasedAt, AllocationStatus
            FROM BedAllocation
            WHERE PatientID=$patientId AND AllocationStatus='Allocated'
            ORDER BY AllocationID DESC LIMIT 1";

    $res = mysqli_query($con, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    closeConnection($con);
    return $row;
}

function getActiveAllocationByBed($bedId) {
    $bedId = (int)$bedId;
    $con = getConnection();

    $sql = "SELECT AllocationID, BedID, PatientID, IPDID, AllocatedAt, ReleasedAt, AllocationStatus
            FROM BedAllocation
            WHERE BedID=$bedId AND AllocationStatus='Allocated'
            ORDER BY AllocationID DESC LIMIT 1";

    $res = mysqli_query($con, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    closeConnection($con);
    return $row;
}

function getBedStatusById($bedId) {
    $bedId = (int)$bedId;
    $con = getConnection();

    $sql = "SELECT BedID, Type, Status FROM BedInventory WHERE BedID=$bedId";
    $res = mysqli_query($con, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    closeConnection($con);
    return $row;
}

function allocateBedToPatient($bedId, $patientId, $ipdId, $allocatedByUserId) {
    $bedId = (int)$bedId;
    $patientId = (int)$patientId;
    $ipdId = (int)$ipdId;
    $allocatedByUserId = (int)$allocatedByUserId;

    $con = getConnection();
    mysqli_autocommit($con, false);

    // 1) Bed must be available
    $bed = null;
    $sqlBed = "SELECT BedID, Status FROM BedInventory WHERE BedID=$bedId FOR UPDATE";
    $resBed = mysqli_query($con, $sqlBed);
    $bed = $resBed ? mysqli_fetch_assoc($resBed) : null;

    if(!$bed || $bed["Status"] !== "Available") {
        mysqli_rollback($con);
        closeConnection($con);
        return "Bed not available";
    }

    // 2) Patient must not have active allocation
    $sqlCheck = "SELECT AllocationID FROM BedAllocation WHERE PatientID=$patientId AND AllocationStatus='Allocated' LIMIT 1 FOR UPDATE";
    $resCheck = mysqli_query($con, $sqlCheck);
    $existing = $resCheck ? mysqli_fetch_assoc($resCheck) : null;

    if($existing) {
        mysqli_rollback($con);
        closeConnection($con);
        return "Patient already has an allocated bed";
    }

    // 3) Insert allocation
    $sqlIns = "INSERT INTO BedAllocation (BedID, PatientID, IPDID, AllocatedAt, ReleasedAt, AllocationStatus, AllocatedByUserID)
               VALUES ($bedId, $patientId, $ipdId, NOW(), NULL, 'Allocated', $allocatedByUserId)";
    $okIns = mysqli_query($con, $sqlIns);

    if(!$okIns) {
        mysqli_rollback($con);
        closeConnection($con);
        return "Failed to allocate bed";
    }

    // 4) Update bed status to occupied
    $sqlUp = "UPDATE BedInventory SET Status='Occupied' WHERE BedID=$bedId";
    $okUp = mysqli_query($con, $sqlUp);

    if(!$okUp) {
        mysqli_rollback($con);
        closeConnection($con);
        return "Failed to update bed status";
    }

    mysqli_commit($con);
    mysqli_autocommit($con, true);
    closeConnection($con);
    return true;
}

function releaseAllocation($allocationId) {
    $allocationId = (int)$allocationId;

    $con = getConnection();
    mysqli_autocommit($con, false);

    // Lock allocation row
    $sqlGet = "SELECT AllocationID, BedID, AllocationStatus FROM BedAllocation WHERE AllocationID=$allocationId FOR UPDATE";
    $resGet = mysqli_query($con, $sqlGet);
    $alloc = $resGet ? mysqli_fetch_assoc($resGet) : null;

    if(!$alloc) {
        mysqli_rollback($con);
        closeConnection($con);
        return "Allocation not found";
    }

    if($alloc["AllocationStatus"] !== "Allocated") {
        mysqli_rollback($con);
        closeConnection($con);
        return "Allocation already released";
    }

    $bedId = (int)$alloc["BedID"];

    // Release allocation
    $sqlRel = "UPDATE BedAllocation
               SET ReleasedAt=NOW(), AllocationStatus='Released'
               WHERE AllocationID=$allocationId";
    $okRel = mysqli_query($con, $sqlRel);

    if(!$okRel) {
        mysqli_rollback($con);
        closeConnection($con);
        return "Failed to release allocation";
    }

    // Set bed to available
    $sqlBed = "UPDATE BedInventory SET Status='Available' WHERE BedID=$bedId";
    $okBed = mysqli_query($con, $sqlBed);

    if(!$okBed) {
        mysqli_rollback($con);
        closeConnection($con);
        return "Failed to update bed status";
    }

    mysqli_commit($con);
    mysqli_autocommit($con, true);
    closeConnection($con);
    return true;
}

function getAllAllocations($onlyActive = false) {
    $con = getConnection();

    if($onlyActive) {
        $sql = "SELECT ba.AllocationID, ba.BedID, bi.Type, bi.Status, ba.PatientID, p.Name AS PatientName,
                       ba.AllocatedAt, ba.ReleasedAt, ba.AllocationStatus
                FROM BedAllocation ba
                JOIN BedInventory bi ON bi.BedID = ba.BedID
                JOIN Patient p ON p.PatientID = ba.PatientID
                WHERE ba.AllocationStatus='Allocated'
                ORDER BY ba.AllocatedAt DESC";
    } else {
        $sql = "SELECT ba.AllocationID, ba.BedID, bi.Type, bi.Status, ba.PatientID, p.Name AS PatientName,
                       ba.AllocatedAt, ba.ReleasedAt, ba.AllocationStatus
                FROM BedAllocation ba
                JOIN BedInventory bi ON bi.BedID = ba.BedID
                JOIN Patient p ON p.PatientID = ba.PatientID
                ORDER BY ba.AllocatedAt DESC";
    }

    $res = mysqli_query($con, $sql);
    $rows = [];
    if($res) {
        while($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
    }
    closeConnection($con);
    return $rows;
}

function getAllocationById($allocationId) {
    $allocationId = (int)$allocationId;
    $con = getConnection();

    $sql = "SELECT ba.AllocationID, ba.BedID, bi.Type, bi.Status, ba.PatientID, p.Name AS PatientName,
                   ba.AllocatedAt, ba.ReleasedAt, ba.AllocationStatus
            FROM BedAllocation ba
            JOIN BedInventory bi ON bi.BedID = ba.BedID
            JOIN Patient p ON p.PatientID = ba.PatientID
            WHERE ba.AllocationID=$allocationId";

    $res = mysqli_query($con, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    closeConnection($con);
    return $row;
}
?>
