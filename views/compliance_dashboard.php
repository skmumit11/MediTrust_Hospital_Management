
<?php
if (!isset($_SESSION)) { session_start(); }
require_once __DIR__ . '/../controllers/complianceController.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>MediTrust — Security & Compliance</title>
  <link rel="stylesheet" href="../assets/style_layout.css">
  <link rel="stylesheet" href="../assets/style_admindashboard.css">
  <link rel="stylesheet" href="../assets/style_layoutAdmin.css">
  <script scr="../assets/sidebar.js"></script>
</head>
<body>

<?php  include __DIR__ . '/layoutCompliance.php'; ?>

<div class="main-content">
  <section class="hero-container">
    <h1>Security & Compliance</h1>
    <p class="hero-subtitle">Monitor, enforce, and report compliance across the system.</p>
  </section>

  <?php if ($message !== "") { ?>
    <div class="<?php echo ($messageType === "success") ? "alert alert-success" : "alert alert-error"; ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php } ?>

  <section class="panel">
    <h2>Session Monitoring</h2>
    <p><b>Auto Logout Timer:</b> <span id="timerDisplay"><?php echo (int)$autoLogoutMinutes; ?>:00</span> minutes</p>
    <script>
        (function(){
            let minutes = <?php echo (int)$autoLogoutMinutes; ?>;
            let seconds = 0;
            const display = document.getElementById('timerDisplay');
            
            const interval = setInterval(function(){
                if(seconds === 0){
                    if(minutes === 0){
                        clearInterval(interval);
                        alert("Session expired. Logging out...");
                        window.location.href = "../controllers/logoutCheck.php";
                        return;
                    }
                    minutes--;
                    seconds = 59;
                } else {
                    seconds--;
                }
                
                const mStr = minutes < 10 ? "0" + minutes : minutes;
                const sStr = seconds < 10 ? "0" + seconds : seconds;
                display.textContent = mStr + ":" + sStr;
            }, 1000);
        })();
    </script>
    <form method="post" action="">
      <input type="hidden" name="action" value="extend_session">
      <button type="submit" class="btn btn-sm">Extend Session</button>
    </form>
  </section>

  <section class="panel">
    <h2>Policies</h2>
    <p>Privacy Policy: ../views/privacy.phpView Policies</a></p>
    <h3>Password Policy</h3>
    <form method="post" action="">
      <input type="hidden" name="action" value="update_password_policy">
      <input type="number" name="MinLength" placeholder="Min length" value="<?php echo (int)($passwordPolicy['MinLength'] ?? 8); ?>">
      <label><input type="checkbox" name="RequireUppercase" <?php echo (isset($passwordPolicy['RequireUppercase']) && (int)$passwordPolicy['RequireUppercase']===1)?'checked':''; ?>> Require Uppercase</label>
      <label><input type="checkbox" name="RequireNumbers"  <?php echo (isset($passwordPolicy['RequireNumbers']) && (int)$passwordPolicy['RequireNumbers']===1)?'checked':''; ?>> Require Numbers</label>
      <input type="number" name="ExpirationDays" placeholder="Expiration (days)" value="<?php echo (int)($passwordPolicy['ExpirationDays'] ?? 90); ?>">
      <button type="submit" class="btn btn-sm">Save Policy</button>
    </form>

    <h3>Encryption Settings</h3>
    <form method="post" action="">
      <input type="hidden" name="action" value="set_encryption">
      <label><input type="checkbox" name="AtRest"> Data at Rest</label>
      <label><input type="checkbox" name="InTransit"> Data in Transit</label>
      <button type="submit" class="btn btn-sm">Apply</button>
    </form>

    <h3>Privacy Policy Versions</h3>
    <table class="table">
      <tr><th>Title</th><th>Version</th><th>Effective</th><th>Content</th></tr>
      <?php foreach ($privacyPolicies as $pp) { ?>
        <tr>
          <td><?php echo htmlspecialchars($pp['Title']); ?></td>
          <td><?php echo htmlspecialchars($pp['Version']); ?></td>
          <td><?php echo htmlspecialchars($pp['EffectiveDate']); ?></td>
          <td><?php echo htmlspecialchars($pp['Content']); ?></td>
        </tr>
      <?php } ?>
    </table>
  </section>

  <section class="panel">
    <h2>Audit Logs</h2>
    <form method="post" action="">
      <input type="hidden" name="action" value="audit_filter">
      <input type="number" name="audit_user_id" placeholder="UserID" value="<?php echo htmlspecialchars($audit_user_id); ?>">
      <input type="text" name="audit_action" placeholder="Action" value="<?php echo htmlspecialchars($audit_action); ?>">
      <input type="text" name="audit_table" placeholder="Table" value="<?php echo htmlspecialchars($audit_table); ?>">
      <button type="submit" class="btn btn-sm">Filter</button>
    </form>
    <table class="table">
      <tr><th>ID</th><th>UserID</th><th>Action</th><th>Table</th><th>Record</th><th>Time</th><th>Details</th></tr>
      <?php foreach ($audit_logs as $al) { ?>
        <tr>
          <td><?php echo (int)$al['LogID']; ?></td>
          <td><?php echo htmlspecialchars($al['UserID']); ?></td>
          <td><?php echo htmlspecialchars($al['Action']); ?></td>
          <td><?php echo htmlspecialchars($al['TableAffected']); ?></td>
          <td><?php echo htmlspecialchars($al['RecordID']); ?></td>
          <td><?php echo htmlspecialchars($al['Timestamp']); ?></td>
          <td><?php echo htmlspecialchars($al['Details']); ?></td>
        </tr>
      <?php } ?>
    </table>
  </section>

  <section class="panel">
    <h2>RBAC</h2>
    <form method="post" action="" style="margin-bottom:12px;">
      <input type="hidden" name="action" value="rbac_add">
      <input type="text" name="Role" placeholder="Role (e.g., Doctor)">
      <input type="text" name="Module" placeholder="Module (e.g., Appointment)">
      <select name="Permission">
        <option>Read</option><option>Write</option><option>Update</option><option>Delete</option>
      </select>
      <button type="submit" class="btn btn-sm">Add</button>
    </form>
    <table class="table">
      <tr><th>ID</th><th>Role</th><th>Module</th><th>Permission</th><th>Action</th></tr>
      <?php foreach ($rolePermissions as $rp) { ?>
        <tr>
          <td><?php echo (int)$rp['RolePermissionID']; ?></td>
          <td><?php echo htmlspecialchars($rp['Role']); ?></td>
          <td><?php echo htmlspecialchars($rp['Module']); ?></td>
          <td><?php echo htmlspecialchars($rp['Permission']); ?></td>
          <td>
            <form method="post" action="">
              <input type="hidden" name="action" value="rbac_delete">
              <input type="hidden" name="RolePermissionID" value="<?php echo (int)$rp['RolePermissionID']; ?>">
              <button type="submit" class="btn btn-sm">Delete</button>
            </form>
          </td>
        </tr>
      <?php } ?>
    </table>
  </section>

  <section class="panel">
    <h2>Password & Authentication (MFA)</h2>
    <form method="post" action="">
      <input type="hidden" name="action" value="set_mfa">
      <select name="UserID">
        <option value="">-- Select User --</option>
        <?php foreach ($usersList as $u) { ?>
          <option value="<?php echo (int)$u['UserID']; ?>">
            <?php echo htmlspecialchars($u['Name']) . " (" . htmlspecialchars($u['Username']) . ") - " . htmlspecialchars($u['Role']); ?>
          </option>
        <?php } ?>
      </select>
      <select name="Method">
        <option>Email</option><option>SMS</option><option>AuthenticatorApp</option>
      </select>
      <input type="text" name="Secret" placeholder="Secret">
      <label><input type="checkbox" name="Enabled"> Enable</label>
      <button type="submit" class="btn btn-sm">Apply</button>
    </form>
  </section>

  <section class="panel">
    <h2>Compliance Reporting</h2>
    <form method="post" action="">
      <input type="hidden" name="action" value="generate_compliance">
      <select name="ReportType"><option>HIPAA</option><option>GDPR</option></select>
      <button type="submit" class="btn btn-sm">Generate Report</button>
    </form>
  </section>

  <section class="panel">
    <h2>Alerts & Notifications</h2>
    <form method="post" action="">
      <input type="hidden" name="action" value="create_alert">
      <select name="RecipientUserID">
        <option value="">-- Select User --</option>
        <?php foreach ($usersList as $u) { ?>
          <option value="<?php echo (int)$u['UserID']; ?>"><?php echo htmlspecialchars($u['Name']); ?></option>
        <?php } ?>
      </select>
      <select name="Channel"><option>Email</option><option>SMS</option><option selected>App</option></select>
      <button type="submit" class="btn btn-sm">Create Alert</button>
    </form>
  </section>

  <section class="panel">
    <h2>User Consent Management</h2>
    <form method="post" action="" style="margin-bottom:12px;">
      <input type="hidden" name="action" value="consent_add">
      <select name="PatientID">
        <option value="">-- Select Patient --</option>
        <?php foreach ($patientsList as $p) { ?>
          <option value="<?php echo (int)$p['PatientID']; ?>"><?php echo htmlspecialchars($p['Name']); ?></option>
        <?php } ?>
      </select>
      <input type="text" name="Purpose" placeholder="Purpose (e.g., Telemedicine)">
      <input type="text" name="PolicyVersion" placeholder="Policy Version (e.g., v1.0)" value="v1.0">
      <button type="submit" class="btn btn-sm">Add Consent</button>
    </form>

    <table class="table">
      <tr><th>ID</th><th>PatientID</th><th>Purpose</th><th>Given At</th><th>Policy Version</th><th>Action</th></tr>
      <?php foreach ($consents as $c) { ?>
        <tr>
          <td><?php echo (int)$c['ConsentID']; ?></td>
          <td><?php echo (int)$c['PatientID']; ?></td>
          <td><?php echo htmlspecialchars($c['Purpose']); ?></td>
          <td><?php echo htmlspecialchars($c['GivenAt']); ?></td>
          <td><?php echo htmlspecialchars($c['PolicyVersion']); ?></td>
          <td>
            <form method="post" action="">
              <input type="hidden" name="action" value="consent_revoke">
              <input type="hidden" name="ConsentID" value="<?php echo (int)$c['ConsentID']; ?>">
              <button type="submit" class="btn btn-sm">Revoke</button>
            </form>
          </td>
        </tr>
      <?php } ?>
    </table>
  </section>
</div>

<div class="footer">
 <?php include ('footer.php');?>
</div>

</body>
</html>
