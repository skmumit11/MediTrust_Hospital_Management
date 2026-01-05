
<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$errors  = isset($_SESSION['shift_errors']) ? $_SESSION['shift_errors'] : [];
$success = isset($_SESSION['shift_success']) ? $_SESSION['shift_success'] : '';
$old     = isset($_SESSION['shift_old']) ? $_SESSION['shift_old'] : [];

unset($_SESSION['shift_errors']);
unset($_SESSION['shift_success']);
unset($_SESSION['shift_old']);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Shift Scheduler</title>
  ../assets/style_admin.css
</head>
<body>

<div class="container">
  <h2>Shift Assignment [HR/Nurse]</h2>

  <?php if (!empty($success)) { ?>
    <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
  <?php } ?>

  <?php if (!empty($errors)) { ?>
    <div class="alert error"><ul>
      <?php foreach ($errors as $e) { ?><li><?php echo htmlspecialchars($e); ?></li><?php } ?>
    </ul></div>
  <?php } ?>

  ../controllers/shiftController.php

    <label>Staff Selector</label>
    <select name="staff_id" required>
      <option value="">-- Select Staff --</option>
      <?php foreach ($staffList as $s) { ?>
        <option value="<?php echo (int)$s['StaffID']; ?>"
          <?php echo (isset($old['staff_id']) && (string)$old['staff_id']===(string)$s['StaffID']) ? 'selected' : ''; ?>>
          <?php echo htmlspecialchars($s['StaffName'] . " (" . $s['DepartmentName'] . ")"); ?>
        </option>
      <?php } ?>
    </select>

    <label>Shift Type</label>
    <?php
      $types = ['Morning','Evening','Night','OnCall'];
      $selectedType = isset($old['shift_type']) ? $old['shift_type'] : '';
    ?>
    <select name="shift_type" required>
      <option value="">-- Select Shift --</option>
      <?php foreach ($types as $t) { ?>
        <option value="<?php echo htmlspecialchars($t); ?>" <?php echo ($selectedType === $t) ? 'selected' : ''; ?>>
          <?php echo htmlspecialchars($t); ?>
        </option>
      <?php } ?>
    </select>

    <label>Start Date & Time</label>
    <input type="datetime-local" name="start_time"
           value="<?php echo isset($old['start_time']) ? htmlspecialchars($old['start_time']) : ''; ?>" required>

    <label>End Date & Time</label>
    <input type="datetime-local" name="end_time"
           value="<?php echo isset($old['end_time']) ? htmlspecialchars($old['end_time']) : ''; ?>" required>

    <button type="submit">Save Shift</button>
  </form>

  <hr>

  <h3>Recent Assigned Shifts</h3>
  <table border="1" cellpadding="8" cellspacing="0" style="width:100%;">
    <tr>
      <th>ID</th>
      <th>Staff</th>
      <th>Shift Type</th>
      <th>Start</th>
      <th>End</th>
    </tr>
    <?php if (!empty($recentShifts)) { ?>
      <?php foreach ($recentShifts as $r) { ?>
        <tr>
          <td><?php echo (int)$r['ShiftID']; ?></td>
          <td><?php echo htmlspecialchars($r['StaffName']); ?></td>
          <td><?php echo htmlspecialchars($r['ShiftType']); ?></td>
          <td><?php echo htmlspecialchars($r['StartTime']); ?></td>
          <td><?php echo htmlspecialchars($r['EndTime']); ?></td>
        </tr>
      <?php } ?>
    <?php } else { ?>
      <tr><td colspan="5">No shifts assigned yet.</td></tr>
    <?php } ?>
  </table>
</div>

</body>
</html>
