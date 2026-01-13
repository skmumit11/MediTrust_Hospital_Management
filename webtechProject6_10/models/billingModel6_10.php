<?php
// models/billingModel6_10.php
require_once 'db6_10.php';

// Simple function to fetch patients for the dropdown
function getPatientListSimple()
{
    $conn = getConnection();
    $sql = "SELECT * FROM Patient";
    $result = mysqli_query($conn, $sql);
    $list = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}

function createBill($patientId, $subTotal, $discount, $vat, $grandTotal, $payMethod)
{
    $conn = getConnection();
    $sql = "INSERT INTO Bill (PatientID, SubTotal, Discount, VAT, GrandTotal, PaymentMethod, BillDate, Status) 
            VALUES ('{$patientId}', '{$subTotal}', '{$discount}', '{$vat}', '{$grandTotal}', '{$payMethod}', NOW(), 'Paid')";
    if (mysqli_query($conn, $sql)) {
        return mysqli_insert_id($conn);
    }
    return 0;
}

function addBillItem($billId, $serviceName, $price)
{
    $conn = getConnection();
    $sql = "INSERT INTO BillItem (BillID, ServiceName, Price) VALUES ('{$billId}', '{$serviceName}', '{$price}')";
    return mysqli_query($conn, $sql);
}

function getBillResultById($billId)
{
    $conn = getConnection();
    $sql = "SELECT b.*, p.Name as PatientName, p.Contact 
            FROM Bill b, Patient p 
            WHERE b.PatientID = p.PatientID AND b.BillID = '{$billId}'";
    $result = mysqli_query($conn, $sql);
    $bill = mysqli_fetch_assoc($result);

    if ($bill) {
        $sql2 = "SELECT * FROM BillItem WHERE BillID = '{$billId}'";
        $res2 = mysqli_query($conn, $sql2);
        $items = [];
        while ($r = mysqli_fetch_assoc($res2)) {
            $items[] = $r;
        }
        $bill['Items'] = $items;
        return $bill;
    }
    return null;
}

function getServiceList()
{
    return [
        ['ServiceID' => 1, 'ServiceName' => 'General Consultation', 'Price' => 500.00],
        ['ServiceID' => 2, 'ServiceName' => 'Specialist Consultation', 'Price' => 1000.00],
        ['ServiceID' => 3, 'ServiceName' => 'CBC Blood Test', 'Price' => 300.00],
        ['ServiceID' => 4, 'ServiceName' => 'X-Ray (Chest)', 'Price' => 600.00],
        ['ServiceID' => 5, 'ServiceName' => 'MRI Scan', 'Price' => 5000.00],
        ['ServiceID' => 6, 'ServiceName' => 'Ultrasound', 'Price' => 1200.00],
        ['ServiceID' => 7, 'ServiceName' => 'ECG', 'Price' => 400.00],
        ['ServiceID' => 8, 'ServiceName' => 'Urine Analysis', 'Price' => 150.00],
        ['ServiceID' => 9, 'ServiceName' => 'Vaccination', 'Price' => 200.00]
    ];
}
?>