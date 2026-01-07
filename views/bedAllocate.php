
<?php
require_once ('../models/bedModel.php');
$bedId = (int)($_POST['BedID'] ?? 0);
$patientId = (int)($_POST['PatientID'] ?? 0);
$allocatedByUserId = (int)($_POST['AllocatedByUserID'] ?? 0);
$ipdId = $_POST['IPDID'] ?? null;
if ($bedId > 0 && $patientId > 0 && $allocatedByUserId > 0) {
    allocateBed($bedId, $patientId, $allocatedByUserId, ($ipdId === '' ? null : (int)$ipdId));
}
header('Location: bedAllocationList.php');
