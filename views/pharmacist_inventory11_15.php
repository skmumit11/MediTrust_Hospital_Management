<?php
session_start();
require_once('../models/pharmacyModel11_15.php');

$medicines = getAllMedicines();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pharmacist - Inventory Management</title>
    <link rel="stylesheet" href="../assets/style_layoutUser.css">
</head>
<body>
    <div class="container">
        <h1>Medicine Inventory Handler</h1>
        
        <?php if(isset($_SESSION['success'])){ echo "<p style='color:green'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>
        <?php if(isset($_SESSION['errors'])){ foreach($_SESSION['errors'] as $e) echo "<p style='color:red'>$e</p>"; unset($_SESSION['errors']); } ?>
        
        <form action="../controllers/pharmacyController11_15.php" method="post">
            <fieldset>
                <legend>Add Medicine</legend>
                <table>
                    <tr>
                        <td>Medicine Name:</td>
                        <td><input type="text" name="name" required></td>
                    </tr>
                    <tr>
                        <td>Batch No:</td>
                        <td><input type="text" name="batch_no" required></td>
                    </tr>
                    <tr>
                        <td>Expiry Date:</td>
                        <td><input type="date" name="expiry_date" required></td>
                    </tr>
                    <tr>
                        <td>Quantity:</td>
                        <td><input type="number" name="quantity" required></td>
                    </tr>
                    <tr>
                        <td>Price:</td>
                        <td><input type="number" step="0.01" name="price" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" name="add_medicine" value="Add Medicine"></td>
                    </tr>
                </table>
            </fieldset>
        </form>
        
        <br>
        <h2>Current Stock</h2>
        <table border="1" style="width:100%; text-align:left;">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Batch No</th>
                <th>Expiry</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
            <?php if(!empty($medicines)){ 
                foreach($medicines as $m){ ?>
            <tr>
                <td><?php echo $m['MedicineID']; ?></td>
                <td><?php echo $m['name']; ?></td> <!-- Mocked in Model -->
                <td><?php echo $m['BatchNo']; ?></td>
                <td><?php echo $m['ExpiryDate']; ?></td>
                <td><?php echo $m['Quantity']; ?></td>
                <td><?php echo $m['Price']; ?></td>
            </tr>
            <?php }
            } else { echo "<tr><td colspan='6'>No medicines found.</td></tr>"; } ?>
        </table>
        
        <br>
        <a href="login11_15.php">Logout</a>
    </div>
</body>
</html>
