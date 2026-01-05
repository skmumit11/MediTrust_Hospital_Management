
<?php
// controllers/bedController.php
require_once __DIR__ . "/../models/bedModel.php";

function cleanText($s) { return trim($s); }

function isValidBedType($t) { return ($t === "ICU" || $t === "General"); }
function isValidBedStatus($s) { return ($s === "Available" || $s === "Occupied"); }

function handleBedCreate() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $type = isset($_POST["Type"]) ? cleanText($_POST["Type"]) : "";
    $st   = isset($_POST["Status"]) ? cleanText($_POST["Status"]) : "";

    if(!isValidBedType($type)) { return "Invalid Type"; }
    if(!isValidBedStatus($st)) { return "Invalid Status"; }

    $ok = createBed($type, $st);
    if($ok) { header("Location: ../views/admindashboard.php"); exit(); }
    return "Failed to create";
}

function handleBedUpdate() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $id   = isset($_POST["BedID"]) ? (int)$_POST["BedID"] : 0;
    $type = isset($_POST["Type"]) ? cleanText($_POST["Type"]) : "";
    $st   = isset($_POST["Status"]) ? cleanText($_POST["Status"]) : "";

    if($id <= 0) { return "Invalid BedID"; }
    if(!isValidBedType($type)) { return "Invalid Type"; }
    if(!isValidBedStatus($st)) { return "Invalid Status"; }

    $ok = updateBed($id, $type, $st);
    if($ok) { header("Location: ../views/admindashboard.php"); exit(); }
    return "Failed to update";
}

function handleBedDelete() {
    if($_SERVER["REQUEST_METHOD"] !== "POST") { return null; }

    $id = isset($_POST["BedID"]) ? (int)$_POST["BedID"] : 0;
    if($id <= 0) { return "Invalid BedID"; }

    $ok = deleteBed($id);
    if($ok) { header("Location: ../views/admindashboard.php"); exit(); }
    return "Failed to delete";
}
