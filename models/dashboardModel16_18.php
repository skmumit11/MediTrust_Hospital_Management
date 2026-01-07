<?php
require_once('db16_18.php');

function kpi_counts()
{
    $con = getConnection();
    $data = [];

    $data['opd'] = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM OPDRecord"))[0];
    $data['ipd'] = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM IPDRecord WHERE Status='Admitted'"))[0];
    $data['beds'] = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM BedInventory WHERE Status='Occupied'"))[0];
    $data['revenue'] = mysqli_fetch_row(mysqli_query($con, "SELECT SUM(Total) FROM Bill"))[0];

    return $data;
}