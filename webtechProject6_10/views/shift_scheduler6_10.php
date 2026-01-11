<?php
// views/shift_scheduler6_10.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Fix for Undefined Variables:
if (!isset($staffList) || !isset($recentShifts)) {
    require_once '../models/shiftModel6_10.php';
    if (!isset($staffList)) { $staffList = getStaffSelectorList(); }
    if (!isset($recentShifts)) { $recentShifts = getRecentShifts(); }
}

$errors  = isset($_SESSION['shift_errors']) ? $_SESSION['shift_errors'] : [];
$success = isset($_SESSION['shift_success']) ? $_SESSION['shift_success'] : '';
$old     = isset($_SESSION['shift_old']) ? $_SESSION['shift_old'] : [];
unset($_SESSION['shift_errors'], $_SESSION['shift_success'], $_SESSION['shift_old']);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Shift Scheduler</title>
  <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
  <link rel="stylesheet" href="../assets/style_lab6_10.css">
  <script src="../assets/sidebar6_10.js"></script>
  <style>
     .shift-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top: 20px; }
  </style>
  <script>
    function validateForm() {
       let staff = document.getElementById('staff').value;
       let type = document.getElementById('type').value;
       let start = document.getElementById('start').value;
       let end = document.getElementById('end').value;

       if(staff == "" || type == "" || start == "" || end == "") {
           alert("All fields are required");
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
      
      <div class="shift-card">
        <h2 style="margin-top:0; color:#2c3e50;">Shift Assignment</h2>
        
        <?php if($success) echo "<div style='color:green; margin-bottom:10px;'>$success</div>"; ?>
        <?php foreach($errors as $e) echo "<div style='color:red; margin-bottom:5px;'>$e</div>"; ?>

        <form action="../controllers/shiftController6_10.php" method="post" onsubmit="return validateForm()">
          <div class="form-group" style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px; font-weight:600;">Staff:</label>
            <select name="staff_id" id="staff" style="width:100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
              <option value="">--Select--</option>
              <?php foreach ($staffList as $s) { 
                 $sel = (isset($old['staff_id']) && $old['staff_id'] == $s['StaffID']) ? 'selected' : '';
              ?>
                <option value="<?=$s['StaffID']?>" <?=$sel?>><?=$s['StaffName']?> (<?=$s['DepartmentName']?>)</option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px; font-weight:600;">Shift Type:</label>
            <select name="shift_type" id="type" style="width:100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
               <option value="">--Select--</option>
               <option value="Morning">Morning</option>
               <option value="Evening">Evening</option>
               <option value="Night">Night</option>
            </select>
          </div>

          <div class="form-row" style="display:flex; gap:20px;">
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600;">Start:</label>
              <input type="datetime-local" name="start_time" id="start" style="width:100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600;">End:</label>
              <input type="datetime-local" name="end_time" id="end" style="width:100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
          </div>
          
          <br>
          <input type="submit" value="Save Shift" style="background:#386D44; color:white; border:none; padding:10px 20px; border-radius:6px; cursor:pointer; font-size:16px;">
        </form>
      </div>

      <div class="shift-card">
        <h3 style="margin-top:0; color:#2c3e50;">Recent Shifts</h3>
        <table style="width:100%; border-collapse:collapse; margin-top:10px;">
          <tr style="background:#f8f9fa;">
            <th style="padding:10px; border:1px solid #eee; text-align:left;">ID</th>
            <th style="padding:10px; border:1px solid #eee; text-align:left;">Staff</th>
            <th style="padding:10px; border:1px solid #eee; text-align:left;">Type</th>
            <th style="padding:10px; border:1px solid #eee; text-align:left;">Start</th>
            <th style="padding:10px; border:1px solid #eee; text-align:left;">End</th>
          </tr>
          <?php foreach ($recentShifts as $r) { ?>
            <tr>
              <td style="padding:10px; border:1px solid #eee;"><?=$r['ShiftID']?></td>
              <td style="padding:10px; border:1px solid #eee;"><?=$r['StaffName']?></td>
              <td style="padding:10px; border:1px solid #eee;"><?=$r['ShiftType']?></td>
              <td style="padding:10px; border:1px solid #eee;"><?=$r['StartTime']?></td>
              <td style="padding:10px; border:1px solid #eee;"><?=$r['EndTime']?></td>
            </tr>
          <?php } ?>
        </table>
      </div>

    </div>
  </div>
</body>
</html>