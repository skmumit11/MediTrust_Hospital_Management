
<?php
require_once "db.php";

function getAllAvailableBeds($typeOptional = "") {
    $con = getConnection();

    if($typeOptional === "ICU" || $typeOptional === "General") {
        $sql = "SELECT BedID, Type, Status FROM BedInventory
                WHERE Status='Available' AND Type=?
                ORDER BY BedID ASC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $typeOptional);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $rows = [];
        if($res) {
            while($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
        }
        mysqli_stmt_close($stmt);
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
            WHERE PatientID=? AND AllocationStatus='Allocated'
            ORDER BY AllocationID DESC LIMIT 1";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $patientId);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    mysqli_stmt_close($stmt);
    closeConnection($con);
    return $row;
}

function getActiveAllocationByBed($bedId) {
    $bedId = (int)$bedId;
    $con = getConnection();

    $sql = "SELECT AllocationID, BedID, PatientID, IPDID, AllocatedAt, ReleasedAt, AllocationStatus
            FROM BedAllocation
            WHERE BedID=? AND AllocationStatus='Allocated'
            ORDER BY AllocationID DESC LIMIT 1";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $bedId);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    mysqli_stmt_close($stmt);
    closeConnection($con);
    return $row;
}

function getBedStatusById($bedId) {
    $bedId = (int)$bedId;
    $con = getConnection();

    $sql = "SELECT BedID, Type, Status FROM BedInventory WHERE BedID=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $bedId);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    mysqli_stmt_close($stmt);
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
    $sqlBed = "SELECT BedID, Status FROM BedInventory WHERE BedID=? FOR UPDATE";
    $stmtBed = mysqli_prepare($con, $sqlBed);
    mysqli_stmt_bind_param($stmtBed, "i", $bedId);
    mysqli_stmt_execute($stmtBed);
    $resBed = mysqli_stmt_get_result($stmtBed);
    $bed = $resBed ? mysqli_fetch_assoc($resBed) : null;
    mysqli_stmt_close($stmtBed);

    if(!$bed || $bed["Status"] !== "Available") {
        mysqli_rollback($con);
        closeConnection($con);
        return "Bed not available";
    }

    // 2) Patient must not have active allocation
    $sqlCheck = "SELECT AllocationID FROM BedAllocation WHERE PatientID=? AND AllocationStatus='Allocated' LIMIT 1 FOR UPDATE";
    $stmtCheck = mysqli_prepare($con, $sqlCheck);
    mysqli_stmt_bind_param($stmtCheck, "i", $patientId);
    mysqli_stmt_execute($stmtCheck);
    $resCheck = mysqli_stmt_get_result($stmtCheck);
    $existing = $resCheck ? mysqli_fetch_assoc($resCheck) : null;
    mysqli_stmt_close($stmtCheck);

    if($existing) {
        mysqli_rollback($con);
        closeConnection($con);
        return "Patient already has an allocated bed";
    }

    // 3) Insert allocation
    $sqlIns = "INSERT INTO BedAllocation (BedID, PatientID, IPDID, AllocatedAt, ReleasedAt, AllocationStatus, AllocatedByUserID)
               VALUES (?, ?, ?, NOW(), NULL, 'Allocated', ?)";
    $stmtIns = mysqli_prepare($con, $sqlIns);
    mysqli_stmt_bind_param($stmtIns, "iiii", $bedId, $patientId, $ipdId, $allocatedByUserId);
    $okIns = mysqli_stmt_execute($stmtIns);
    mysqli_stmt_close($stmtIns);

    if(!$okIns) {
        mysqli_rollback($con);
        closeConnection($con);
        return "Failed to allocate bed";
    }

    // 4) Update bed status to occupied
    $sqlUp = "UPDATE BedInventory SET Status='Occupied' WHERE BedID=?";
    $stmtUp = mysqli_prepare($con, $sqlUp);
    mysqli_stmt_bind_param($stmtUp, "i", $bedId);
    $okUp = mysqli_stmt_execute($stmtUp);
    mysqli_stmt_close($stmtUp);

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
    $sqlGet = "SELECT AllocationID, BedID, AllocationStatus FROM BedAllocation WHERE AllocationID=? FOR UPDATE";
    $stmtGet = mysqli_prepare($con, $sqlGet);
    mysqli_stmt_bind_param($stmtGet, "i", $allocationId);
    mysqli_stmt_execute($stmtGet);
    $resGet = mysqli_stmt_get_result($stmtGet);
    $alloc = $resGet ? mysqli_fetch_assoc($resGet) : null;
    mysqli_stmt_close($stmtGet);

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
               WHERE AllocationID=?";
    $stmtRel = mysqli_prepare($con, $sqlRel);
    mysqli_stmt_bind_param($stmtRel, "i", $allocationId);
    $okRel = mysqli_stmt_execute($stmtRel);
    mysqli_stmt_close($stmtRel);

    if(!$okRel) {
        mysqli_rollback($con);
        closeConnection($con);
        return "Failed to release allocation";
    }

    // Set bed to available
    $sqlBed = "UPDATE BedInventory SET Status='Available' WHERE BedID=?";
    $stmtBed = mysqli_prepare($con, $sqlBed);
    mysqli_stmt_bind_param($stmtBed, "i", $bedId);
    $okBed = mysqli_stmt_execute($stmtBed);
    mysqli_stmt_close($stmtBed);

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
            WHERE ba.AllocationID=?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $allocationId);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    mysqli_stmt_close($stmt);
    closeConnection($con);
    return $row;
}
?>
