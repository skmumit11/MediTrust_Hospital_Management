
<?php
require_once ('../models/patientModel.php');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) { deletePatient($id); }
header('Location: admindashboard.php');

