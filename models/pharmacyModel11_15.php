<?php
require_once('db11_15.php');

function addMedicine($name, $batch_no, $expiry_date, $quantity, $price) {
    $con = getConnection();
    // Table: MedicineInventory (MedicineID, BatchNo, ExpiryDate, Quantity, Price). 
    // DOES NOT HAVE 'Name'?? 
    // Schema: MedicineID INT, BatchNo VARCHAR(50) UNIQUE, ExpiryDate DATE, Quantity INT, Price DECIMAL.
    // Where is the medicine Name? 
    // Is it 'BatchNo' intended to be the ID?
    // Or maybe I missed it.
    // Reviewing schema: `CREATE TABLE IF NOT EXISTS MedicineInventory ... BatchNo, ExpiryDate...`
    // No Name column!
    // But `Prescription` table has `Medicine VARCHAR(100)`.
    // Maybe `MedicineInventory` is linked to another table? No `MedicineID` FK in schema.
    // This schema seems to lack Medicine Name in Inventory.
    // I will Insert into MedicineInventory WITHOUT Name? That seems wrong.
    // Maybe the user forgot it.
    // I will assume Name is missing and just insert available fields, or mention it.
    // Actually, I'll check `Prescription` table: `Medicine VARCHAR(100)`.
    // It seems `MedicineInventory` is just for stock tracking by Batch?
    // I will just use BatchNo for Name in my View if Name is missing? 
    // Or maybe I can assume 'Name' was omitted by mistake.
    // I'll try to insert Name if I can, but query will fail. 
    // I'll stick to provided schema: Insert BatchNo, Expiry, Qty, Price.
    
    $batch_no = mysqli_real_escape_string($con, $batch_no);
    $expiry_date = mysqli_real_escape_string($con, $expiry_date);
    $quantity = (int)$quantity;
    $price = (float)$price;
    
    $sql = "INSERT INTO MedicineInventory (BatchNo, ExpiryDate, Quantity, Price) VALUES ('$batch_no', '$expiry_date', $quantity, $price)";
    
    return mysqli_query($con, $sql);
}

function getAllMedicines() {
    $con = getConnection();
    $sql = "SELECT * FROM MedicineInventory ORDER BY MedicineID DESC";
    $result = mysqli_query($con, $sql);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Mock name
            $row['name'] = "Unknown (No Name Col)";
            $data[] = $row;
        }
    }
    return $data;
}
?>
