<?php
// views/staff_edit.php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Variables passed from controller: $staffProfile, $departments
if (!isset($staffProfile) || !isset($departments)) {
  // If accessed directly without data, redirect to list
  header("Location: ../views/stafflist6_10.php");
  exit();
}

$errors = isset($_SESSION['staff_errors']) ? $_SESSION['staff_errors'] : [];
unset($_SESSION['staff_errors']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit Staff Profile</title>
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

  <?php include 'layoutAdmin6_10.php'; ?>

  <div class="main-content">
    <div class="container">
      <div class="card">
        <h2>Edit Staff Profile <span class="role-badge">(HR)</span></h2>

        <?php if (!empty($errors)) { ?>
          <div class="alert alert-danger">
            <ul><?php foreach ($errors as $e) { ?>
                <li><?php echo htmlspecialchars($e); ?></li><?php } ?>
            </ul>
          </div>
        <?php } ?>

        <form action="../controllers/staffController6_10.php?action=edit" method="post">
          <!-- ID is fixed -->
          <input type="hidden" name="staff_id" value="<?php echo (int) $staffProfile['StaffID']; ?>">

          <div class="form-group">
            <label>Staff Name (Read Only)</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($staffProfile['StaffName']); ?>"
              readonly disabled>
          </div>

          <div class="form-group">
            <label>Department</label>
            <select name="department_id" class="form-control" required>
              <?php foreach ($departments as $d) {
                $sel = ($d['DepartmentID'] == $staffProfile['DepartmentID']) ? 'selected' : '';
                ?>
                <option value="<?php echo (int) $d['DepartmentID']; ?>" <?php echo $sel; ?>>
                  <?php echo htmlspecialchars($d['Name']); ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <label>Role Assignment</label>
            <input type="text" name="role_assignment" class="form-control"
              value="<?php echo htmlspecialchars($staffProfile['RoleAssignment']); ?>" required>
          </div>

          <div class="form-group">
            <label>Contact Email</label>
            <input type="email" name="contact" class="form-control"
              value="<?php echo htmlspecialchars($staffProfile['ContactEmail']); ?>" required>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="../views/stafflist6_10.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>