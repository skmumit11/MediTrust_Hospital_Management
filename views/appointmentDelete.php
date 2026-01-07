
<?php
require_once ('../models/appointmentModel.php');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) { deleteAppointment($id); }
header('Location: admindashboard.php');
