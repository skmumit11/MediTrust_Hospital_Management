
<?php
require_once ('../models/bedModel.php');
$allocId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($allocId > 0) { releaseBedAllocation($allocId); }
header('Location: bedAllocationList.php');

