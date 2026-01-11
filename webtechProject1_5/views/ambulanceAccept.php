
<?php
require_once  '../models/ambulanceModel.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) { updateAmbulanceStatus($id, 'Accepted'); }
header('Location: admindashboard.php');
