<?php
require_once('../models/encounterModel16_18.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientID = $_POST['patientID'];
    $doctorID = $_POST['doctorID'];
    $vitals = $_POST['vitals'];
    $diagnosis = $_POST['diagnosisICD'];
    $allergies = isset($_POST['allergies']) ? $_POST['allergies'] : [];
    $prescriptions = [];
    if (isset($_POST['medicine'])) {
        foreach ($_POST['medicine'] as $i => $m) {
            $prescriptions[] = [
                'medicine' => $m,
                'dosage' => $_POST['dosage'][$i],
                'duration' => $_POST['duration'][$i]
            ];
        }
    }
    $attachments = $_POST['attachments'] ?? '';

    $id = encounter_save($patientID, $doctorID, $vitals, $diagnosis, $allergies, $prescriptions, $attachments);
    $msg = "Encounter saved with ID $id";
}

include('../views/encounter_form16_18.php');
