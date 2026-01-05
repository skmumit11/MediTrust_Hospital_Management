<?php
session_start();
require_once "../models/db.php"; // include the db connection

$conn = getConnection(); // get mysqli connection

// Fetch Audit Logs
$auditLogs = $conn->query("
    SELECT a.LogID, u.Name AS UserName, a.Action, a.TableAffected, a.RecordID, a.Timestamp 
    FROM AuditLog a 
    JOIN User u ON a.UserID = u.UserID
    ORDER BY a.Timestamp DESC 
    LIMIT 50
");

// Fetch Privacy Policies
$privacyPolicies = $conn->query("SELECT * FROM PrivacyPolicy ORDER BY EffectiveDate DESC");

// Fetch Password Policy
$passwordPolicy = $conn->query("SELECT * FROM PasswordPolicy LIMIT 1")->fetch_assoc();

// Fetch Role Permissions
$rolePermissions = $conn->query("SELECT * FROM RolePermission ORDER BY Role, Module");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Compliance & Security Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Auto logout timer (10 min default)
        let timer = 10 * 60; 
        function startTimer() {
            let interval = setInterval(() => {
                timer--;
                document.getElementById('timer').innerText = Math.floor(timer/60) + " min " + (timer%60) + " sec";
                if(timer <= 0){
                    clearInterval(interval);
                    alert('Session expired! Logging out.');
                    window.location.href = 'logout.php';
                }
            }, 1000);
        }
        function extendSession() {
            timer = 10 * 60; // reset timer
            alert('Session extended by 10 minutes');
        }
        window.onload = startTimer;
    </script>
</head>
<body class="p-4">

    <h2>🛡 Compliance & Security Dashboard</h2>

    <div class="mb-3">
        <strong>Auto Logout Timer:</strong> <span id="timer"></span>
        <button class="btn btn-sm btn-primary ms-2" onclick="extendSession()">Extend Session</button>
    </div>

    <!-- Privacy & Password Policy -->
    <div class="card mb-4">
        <div class="card-header">Policies</div>
        <div class="card-body">
            <h5>Privacy Policies:</h5>
            <ul>
                <?php while($policy = $privacyPolicies->fetch_assoc()): ?>
                    <li>
                        <a href="view_policy.php?id=<?php echo $policy['PolicyID']; ?>">
                            <?php echo $policy['Title'] . " (Version: " . $policy['Version'] . ")"; ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
            <h5>Password Policy:</h5>
            <p>Minimum Length: <?php echo $passwordPolicy['MinLength']; ?>, 
               Uppercase Required: <?php echo $passwordPolicy['RequireUppercase'] ? 'Yes':'No'; ?>,
               Numbers Required: <?php echo $passwordPolicy['RequireNumbers'] ? 'Yes':'No'; ?>,
               Expiration (days): <?php echo $passwordPolicy['ExpirationDays']; ?>
            </p>
        </div>
    </div>

    <!-- Audit Logs -->
    <div class="card mb-4">
        <div class="card-header">Audit Logs (Last 50)</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>LogID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>RecordID</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $auditLogs->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['LogID']; ?></td>
                            <td><?php echo $row['UserName']; ?></td>
                            <td><?php echo $row['Action']; ?></td>
                            <td><?php echo $row['TableAffected']; ?></td>
                            <td><?php echo $row['RecordID']; ?></td>
                            <td><?php echo $row['Timestamp']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Role-Based Access Control -->
    <div class="card mb-4">
        <div class="card-header">Role-Based Access Control</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Module</th>
                        <th>Permission</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($rp = $rolePermissions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $rp['Role']; ?></td>
                            <td><?php echo $rp['Module']; ?></td>
                            <td><?php echo $rp['Permission']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Compliance Reports -->
    <div class="card mb-4">
        <div class="card-header">Compliance Reports</div>
        <div class="card-body">
            <a href="generate_report.php?type=HIPAA" class="btn btn-sm btn-success">Generate HIPAA Report</a>
            <a href="generate_report.php?type=GDPR" class="btn btn-sm btn-success">Generate GDPR Report</a>
        </div>
    </div>

</body>
</html>