
<?php
require_once ("../models/bedAllocationModel.php");

function cleanText($s) { return trim($s); }

function handleBedAllocate() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $bedId = isset($_POST["BedID"]) ? (int)$_POST["BedID"] : 0;
    $patientId = isset($_POST["PatientID"]) ? (int)$_POST["PatientID"] : 0;
    $ipdId = isset($_POST["IPDID"]) ? (int)$_POST["IPDID"] : 0;

    // you can store admin user id in session, example: $_SESSION["user_id"]
    $adminId = isset($_SESSION["user_id"]) ? (int)$_SESSION["user_id"] : 0;

    if($bedId <= 0) { return "Select a bed"; }
    if($patientId <= 0) { return "Select a patient"; }
    if($adminId <= 0) { return "Admin session missing"; }

    $result = allocateBedToPatient($bedId, $patientId, $ipdId, $adminId);
    if($result === true) {
        header("Location: ../views/bedAllocationList.php");
        exit();
    }
    return $result;
}

function handleBedRelease() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $allocationId = isset($_POST["AllocationID"]) ? (int)$_POST["AllocationID"] : 0;
    if($allocationId <= 0) { return "Invalid allocation"; }

    $result = releaseAllocation($allocationId);
    if($result === true) {
        header("Location: ../views/bedAllocationList.php");
        exit();
    }
    return $result;
}
?>
``
