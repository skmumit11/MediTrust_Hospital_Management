<?php
// views/staff_add.php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Logic to fetch dropdown data if not provided
if (!isset($users) || !isset($departments)) {
  require_once '../models/staffModel6_10.php';
  if (!isset($users)) {
    $users = getEligibleStaffUsers();
  }
  if (!isset($departments)) {
    $departments = getDepartmentList();
  }
}

$errors = isset($_SESSION['staff_errors']) ? $_SESSION['staff_errors'] : [];
$old = isset($_SESSION['staff_old']) ? $_SESSION['staff_old'] : [];
unset($_SESSION['staff_errors'], $_SESSION['staff_old']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Add Staff Profile - Meditrust</title>
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
        <h2>Add Staff Profile <span class="role-badge">(HR)</span></h2>

        <?php if (!empty($errors)) { ?>
          <div class="alert alert-danger">
            <ul>
              <?php foreach ($errors as $e) { ?>
                <li><?php echo htmlspecialchars($e); ?></li>
              <?php } ?>
            </ul>
          </div>
        <?php } ?>

        <form action="../controllers/staffController6_10.php?action=add" method="post">

          <div class="form-group">
            <label>Select User (Eligible for Staff Role)</label>
            <select name="staff_id" class="form-control" required>
              <option value="">-- Select User --</option>
              <?php foreach ($users as $u) {
                $sel = (isset($old['staff_id']) && (string) $old['staff_id'] === (string) $u['UserID']) ? 'selected' : '';
                ?>
                <option value="<?php echo (int) $u['UserID']; ?>" <?php echo $sel; ?>>
                  <?php echo htmlspecialchars($u['Name']); ?> (ID: <?php echo $u['UserID']; ?>)
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <label>Department</label>
            <select name="department_id" class="form-control" required>
              <option value="">-- Select Department --</option>
              <?php foreach ($departments as $d) {
                $sel = (isset($old['department_id']) && (string) $old['department_id'] === (string) $d['DepartmentID']) ? 'selected' : '';
                ?>
                <option value="<?php echo (int) $d['DepartmentID']; ?>" <?php echo $sel; ?>>
                  <?php echo htmlspecialchars($d['Name']); ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <label>Role Assignment / Job Title</label>
            <input type="text" name="role_assignment" class="form-control"
              placeholder="e.g. Senior Nurse, Lab Technician"
              value="<?php echo isset($old['role_assignment']) ? htmlspecialchars($old['role_assignment']) : ''; ?>"
              required>
          </div>

          <div class="form-group">
            <label>Contact Email (Will Update User Profile)</label>
            <input type="email" name="contact" class="form-control" placeholder="staff@meditrust.com"
              value="<?php echo isset($old['contact']) ? htmlspecialchars($old['contact']) : ''; ?>" required>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Profile</button>
            <a href="stafflist6_10.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>

    </div>
  </div>

</body>

</html>