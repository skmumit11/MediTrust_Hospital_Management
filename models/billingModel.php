<?php
// models/billingModel.php
require_once __DIR__ . '/db.php';

/**
 * Ensures Billing tables exist. 
 * Run this on page load or once.
 */
function setupBillingTables()
{
    $con = getConnection();

    // 1. Bill Table
    $sqlBill = "CREATE TABLE IF NOT EXISTS Bill (
        BillID INT AUTO_INCREMENT PRIMARY KEY,
        PatientID INT NOT NULL,
        BillDate DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($con, $sqlBill);

    // Schema Updates (Add columns if missing)
    // We run these individually. If they fail (column exists), it ignores the error and continues.
    $updates = [
        "ALTER TABLE Bill ADD COLUMN BillDate DATETIME DEFAULT CURRENT_TIMESTAMP",
        "ALTER TABLE Bill ADD COLUMN SubTotal DECIMAL(10,2) DEFAULT 0.00",
        "ALTER TABLE Bill ADD COLUMN Discount DECIMAL(10,2) DEFAULT 0.00",
        "ALTER TABLE Bill ADD COLUMN VAT DECIMAL(10,2) DEFAULT 0.00",
        "ALTER TABLE Bill ADD COLUMN GrandTotal DECIMAL(10,2) DEFAULT 0.00",
        "ALTER TABLE Bill ADD COLUMN PaymentMethod VARCHAR(50) DEFAULT 'Cash'",
        "ALTER TABLE Bill ADD COLUMN Status VARCHAR(20) DEFAULT 'Paid'"
    ];

    foreach ($updates as $u) {
        try {
            mysqli_query($con, $u);
        } catch (Exception $e) {
            // Ignore "Duplicate column name" error
        }
    }

    // 2. BillItem Table
    $sqlItem = "CREATE TABLE IF NOT EXISTS BillItem (
        ItemID INT AUTO_INCREMENT PRIMARY KEY,
        BillID INT NOT NULL,
        ServiceName VARCHAR(100),
        Price DECIMAL(10,2),
        FOREIGN KEY (BillID) REFERENCES Bill(BillID) ON DELETE CASCADE
    )";
    mysqli_query($con, $sqlItem);

    mysqli_close($con);
}

// Ensure tables exist immediately
setupBillingTables();


function createBill($patientId, $subTotal, $discount, $vat, $grandTotal, $paymentMethod)
{
    $con = getConnection();
    $sql = "INSERT INTO Bill (PatientID, SubTotal, Discount, VAT, GrandTotal, PaymentMethod, Status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Paid')";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "idddds", $patientId, $subTotal, $discount, $vat, $grandTotal, $paymentMethod);

    if (mysqli_stmt_execute($stmt)) {
        $billId = mysqli_insert_id($con);
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        return $billId;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return false;
}

function addBillItem($billId, $serviceName, $price)
{
    $con = getConnection();
    $sql = "INSERT INTO BillItem (BillID, ServiceName, Price) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "isd", $billId, $serviceName, $price);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $ok;
}

function getBillResultById($billId)
{
    $con = getConnection();
    // Get Bill Info + Patient Name
    $sql = "SELECT b.*, p.Name as PatientName, p.Contact 
            FROM Bill b
            JOIN Patient p ON b.PatientID = p.PatientID
            WHERE b.BillID = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $billId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $bill = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$bill) {
        mysqli_close($con);
        return null;
    }

    // Get Items
    $sqlItems = "SELECT * FROM BillItem WHERE BillID = ?";
    $stmtI = mysqli_prepare($con, $sqlItems);
    mysqli_stmt_bind_param($stmtI, "i", $billId);
    mysqli_stmt_execute($stmtI);
    $resI = mysqli_stmt_get_result($stmtI);

    $items = [];
    while ($row = mysqli_fetch_assoc($resI)) {
        $items[] = $row;
    }

    mysqli_stmt_close($stmtI);
    mysqli_close($con);

    $bill['Items'] = $items;
    return $bill;
}

/**
 * Standard list of services for the Cashier dropdown
 */
function getServiceList()
{
    return [
        ['name' => 'General Consultation', 'price' => 500.00],
        ['name' => 'Specialist Consultation', 'price' => 1000.00],
        ['name' => 'CBC Blood Test', 'price' => 300.00],
        ['name' => 'X-Ray (Chest)', 'price' => 600.00],
        ['name' => 'MRI Scan', 'price' => 5000.00],
        ['name' => 'Ultrasound', 'price' => 1200.00],
        ['name' => 'ECG', 'price' => 400.00],
        ['name' => 'Urine Analysis', 'price' => 150.00],
        ['name' => 'Vaccination', 'price' => 200.00]
    ];
}
