<?php
// views/stafflist.php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Logic to fetch staff list if not provided by controller
if (!isset($staffList) || !is_array($staffList)) {
  require_once '../models/staffModel6_10.php';
  $staffList = getStaffProfileList();
}

if (!isset($staffList)) {
  $staffList = [];
}

$errors = isset($_SESSION['staff_errors']) ? $_SESSION['staff_errors'] : [];
$success = isset($_SESSION['staff_success']) ? $_SESSION['staff_success'] : '';
unset($_SESSION['staff_errors'], $_SESSION['staff_success']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Staff Profiles - Meditrust</title>
  <!-- Admin Sidebar Styles -->
    <link rel="stylesheet" href="../assets/style_admindashboard6_10.css">
    <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
    <link rel="stylesheet" href="../assets/style_lab6_10.css">
    <script src="../assets/sidebar6_10.js"></script>

  <style>
    body {
      padding-bottom: 50px;
    }

    .main-content {
      margin-left: 30px;
      padding: 20px;
      transition: margin 0.3s;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>

<body>

  <!-- Include Sidebar -->
  <?php include 'layoutAdmin6_10.php'; ?>

  <div class="main-content">
    <div class="container">

      <div class="card">
        <div class="header-flex">
          <h2>Staff Profiles <span class="role-badge">(HR / Admin)</span></h2>
          <div class="header-actions">
            <a href="staff_add6_10.php" class="btn btn-primary">
              + Add New Staff
            </a>
          </div>
        </div>

        <?php if (!empty($success)) { ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php } ?>

        <?php if (!empty($errors)) { ?>
          <div class="alert alert-danger">
            <ul>
              <?php foreach ($errors as $e) { ?>
                <li><?php echo htmlspecialchars($e); ?></li>
              <?php } ?>
            </ul>
          </div>
        <?php } ?>

        <div class="table-responsive">
          <table class="styled-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Department</th>
                <th>Role</th>
                <th style="width: 150px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($staffList)) { ?>
                <tr>
                  <td colspan="6" class="no-data">No staff profiles found.</td>
                </tr>
              <?php } else { ?>
                <?php foreach ($staffList as $s) { ?>
                  <tr>
                    <td>#<?php echo (int) $s['StaffID']; ?></td>
                    <td>
                      <span class="patient-name"><?php echo htmlspecialchars($s['StaffName']); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($s['ContactEmail']); ?></td>
                    <td><span class="tag-test"><?php echo htmlspecialchars($s['DepartmentName']); ?></span></td>
                    <td><?php echo htmlspecialchars($s['RoleAssignment']); ?></td>
                    <td>
                      <a href="../controllers/staffController6_10.php?action=edit&id=<?php echo (int) $s['StaffID']; ?>"
                        class="link-action" style="color: var(--accent); margin-right: 10px;">Edit</a>

                      <form action="../controllers/staffController6_10.php?action=delete" method="post" style="display:inline;">
                        <input type="hidden" name="staff_id" value="<?php echo (int) $s['StaffID']; ?>">
                        <button type="submit" class="link-action"
                          style="background:none; border:none; color:var(--danger); cursor:pointer; padding:0; font:inherit;"
                          onclick="return confirm('Are you sure you want to delete this staff profile?')">
                          Delete
                        </button>
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
  </div>

</body>

</html>