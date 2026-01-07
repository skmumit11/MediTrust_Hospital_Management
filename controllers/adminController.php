<?php
// controllers/adminController.php
require_once "../models/doctorModel.php";
require_once "../models/patientModel.php";
require_once "../models/appointmentModel.php";
require_once "../models/bedModel.php";
require_once "../models/ambulanceModel.php";

function loadDashboard() {
    $doctors           = getAllDoctors();           // Name, Specialty, Username, DoctorID
    $patients          = getAllPatients();          // Name, Age, Gender, PatientID
    $appointments      = getAllAppointments();      // patient, doctor, Slot, Status, AppointmentID
    $ambulanceRequests = getAllAmbulanceRequests(); // RequestID, Name, Contact, PickupLocation, Status

    $icuBeds     = countBedsBy("ICU", "Available");
    $generalBeds = countBedsBy("General", "Available");
    $availableBeds = $icuBeds + $generalBeds;

    return [
        "totalDoctors"      => count($doctors),
        "totalPatients"     => count($patients),
        "availableBeds"     => $availableBeds,
        "icuBeds"           => $icuBeds,
        "generalBeds"       => $generalBeds,
        "appointments"      => $appointments,
        "doctors"           => $doctors,
        "patients"          => $patients,
        "ambulanceRequests" => $ambulanceRequests
    ];
}
