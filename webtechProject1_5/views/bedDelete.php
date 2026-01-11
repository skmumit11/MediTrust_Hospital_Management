
<?php
require_once ('../models/bedModel.php');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) { deleteBed($id); }
header('Location: bedView.php');
