<?php
require_once('db16_18.php');

function template_all()
{
    $con = getConnection();
    return mysqli_query($con, "SELECT * FROM NotificationTemplate ORDER BY TemplateID DESC");
}

function template_create($type, $placeholders)
{
    $con = getConnection();
    $type = mysqli_real_escape_string($con, $type);
    $ph = mysqli_real_escape_string($con, $placeholders);

    $exists = mysqli_query($con, "SELECT 1 FROM NotificationTemplate WHERE Type='$type' LIMIT 1");
    if (mysqli_num_rows($exists) > 0)
        return false;

    mysqli_query($con, "INSERT INTO NotificationTemplate (Type, Placeholders) VALUES ('$type','$ph')");
    return true;
}

function template_update($id, $placeholders)
{
    $con = getConnection();
    $id = (int) $id;
    $ph = mysqli_real_escape_string($con, $placeholders);
    mysqli_query($con, "UPDATE NotificationTemplate SET Placeholders='$ph' WHERE TemplateID=$id");
}