<?php
// views/staff_edit6_10.php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($staffProfile) || !isset($departments)) {
  header("Location: ../views/stafflist6_10.php");
  exit();
}
$errors = isset($_SESSION['staff_errors']) ? $_SESSION['staff_errors'] : [];
unset($_SESSION['staff_errors']);
?>
<!DOCTYPE html>
<html>

<head>
  <title>Edit Staff</title>
  <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
  <link rel="stylesheet" href="../assets/style_lab6_10.css">
  <script src="../assets/sidebar6_10.js"></script>
  <style>
    .form-card {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      max-width: 600px;
      margin: 20px auto;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #2c3e50;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 14px;
      background: #f9f9f9;
    }

    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #386D44;
      background: white;
    }

    .btn-submit {
      background: #386D44;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
      width: 100%;
      transition: background 0.3s;
    }

    .btn-submit:hover {
      background: #2e5a36;
    }

    .btn-cancel {
      display: block;
      text-align: center;
      margin-top: 15px;
      color: #7f8c8d;
      text-decoration: none;
    }
  </style>
  <script>
    function validateForm() {
      let role = document.getElementById('role').value;
      let email = document.getElementById('email').value;

      if (role == "" || email == "") {
        alert("Null value!");
        return false;
      }
      if (email.indexOf('@') == -1 || email.indexOf('.') == -1) {
        alert("Invalid email!");
        return false;
      }
      return true;
    }
  </script>
</head>

<body>
  <?php include 'layoutAdmin6_10.php'; ?>

  <div class="main-content">
    <div class="container-fluid">

      <div class="form-card">
        <h2 style="text-align:center; color:#2c3e50; margin-bottom:25px;">Edit Staff Profile</h2>

        <?php foreach ($errors as $e)
          echo "<div style='color:red; margin-bottom:15px; text-align:center;'>{$e}</div>"; ?>

        <form action="../controllers/staffController6_10.php?action=edit_submit" method="post"
          onsubmit="return validateForm()">
          <input type="hidden" name="staff_id" value="<?= $staffProfile['StaffID'] ?>">

          <div class="form-group">
            <label>Name:</label>
            <input type="text" value="<?= $staffProfile['StaffName'] ?>" disabled
              style="background:#e9ecef; cursor:not-allowed;">
          </div>

          <div class="form-group">
            <label>Department:</label>
            <select name="department_id">
              <?php foreach ($departments as $d) {
                $sel = ($d['DepartmentID'] == $staffProfile['DepartmentID']) ? 'selected' : '';
                ?>
                <option value="<?= $d['DepartmentID'] ?>" <?= $sel ?>><?= $d['Name'] ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <label>Role Assignment:</label>
            <input type="text" name="role" id="role" value="<?= $staffProfile['RoleAssignment'] ?>">
          </div>

          <div class="form-group">
            <label>Contact Email:</label>
            <input type="text" name="email" id="email" value="<?= $staffProfile['ContactEmail'] ?>">
          </div>

          <button type="submit" class="btn-submit">Update Profile</button>
          <a href="stafflist6_10.php" class="btn-cancel">Cancel</a>
        </form>
      </div>

    </div>
  </div>
</body>

</html>