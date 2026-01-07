<?php
require_once('../models/templateModel16_18.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $ok = template_create($_POST['type'], $_POST['placeholders']) ? "Template created." : "Type exists.";
    } elseif (isset($_POST['update'])) {
        template_update($_POST['templateID'], $_POST['placeholders']);
        $ok = "Template updated.";
    }
}

$templates = template_all();
include('../views/template_manager16_18.php');
