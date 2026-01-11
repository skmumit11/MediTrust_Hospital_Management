<?php
// views/stafflist6_10.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($staffList) || !is_array($staffList)) {
  require_once '../models/staffModel6_10.php';
  $staffList = getStaffProfileList();
}

$errors = isset($_SESSION['staff_errors']) ? $_SESSION['staff_errors'] : [];
$success = isset($_SESSION['staff_success']) ? $_SESSION['staff_success'] : '';
unset($_SESSION['staff_errors'], $_SESSION['staff_success']);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Staff List</title>
  <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
  <link rel="stylesheet" href="../assets/style_lab6_10.css">
  <script src="../assets/sidebar6_10.js"></script>
  <style>
    .staff-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top:20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
    th { background-color: #f8f9fa; color: #2c3e50; font-weight: 600; }
    .btn-edit { color: #3498db; text-decoration: none; font-weight: 500; margin-right: 10px; }
    .btn-delete { background: none; border: none; color: #e74c3c; cursor: pointer; font-weight: 500; font-size: 16px; }
    .btn-add { display: inline-block; background: #386D44; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; }
  </style>
</head>
<body>
  <?php include 'layoutAdmin6_10.php'; ?>

  <div class="main-content">
    <div class="container-fluid">
      
      <div class="staff-card">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <h2 style="margin:0; color:#2c3e50;">Staff Management</h2>
          <a href="staff_add6_10.php" class="btn-add">+ Add New Staff</a>
        </div>
        
        <?php if (!empty($success)) echo "<div style='color:green; margin-top:10px;'>{$success}</div>"; ?>
        <?php if (!empty($errors)) foreach ($errors as $e) echo "<div style='color:red; margin-top:10px;'>{$e}</div>"; ?>

        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Contact</th>
              <th>Department</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($staffList)) { ?>
              <tr><td colspan="6" style="text-align:center;">No staff found.</td></tr>
            <?php } else { ?>
              <?php foreach ($staffList as $s) { ?>
                <tr>
                  <td><?=$s['StaffID']?></td>
                  <td><?=$s['StaffName']?></td>
                  <td><?=$s['ContactEmail']?></td>
                  <td><?=$s['DepartmentName']?></td>
                  <td><?=$s['RoleAssignment']?></td>
                  <td>
                    <a href="../controllers/staffController6_10.php?action=edit&id=<?=$s['StaffID']?>" class="btn-edit">Edit</a>
                    <form action="../controllers/staffController6_10.php?action=delete" method="post" style="display:inline;" onsubmit="return confirm('Delete?');">
                      <input type="hidden" name="staff_id" value="<?=$s['StaffID']?>">
                      <button type="submit" class="btn-delete">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php } ?>
            <?php } ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</body>
</html>