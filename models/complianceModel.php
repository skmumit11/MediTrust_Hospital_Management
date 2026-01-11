
<?php
require_once ('db.php');

function comp_getUserIdByUsername($username) {
    $conn = getConnection();
    $username = trim($username);
    $res = mysqli_query($conn, "SELECT UserID FROM `User` WHERE Username='$username'");
    $id = null;
    if ($res && $row = mysqli_fetch_assoc($res)) { $id = (int)$row['UserID']; }
    closeConnection($conn);
    return $id;
}

function comp_getAllUsers() {
    $conn = getConnection();
    $rows = [];
    $res = $conn->query("SELECT UserID, Username, Name, Role FROM `User` ORDER BY Name ASC");
    if ($res) { while ($x = $res->fetch_assoc()) { $rows[] = $x; } $res->free(); }
    closeConnection($conn);
    return $rows;
}

function comp_getAllPatients() {
    $conn = getConnection();
    $rows = [];
    $res = $conn->query("SELECT PatientID, Name FROM Patient ORDER BY Name ASC");
    if ($res) { while ($x = $res->fetch_assoc()) { $rows[] = $x; } $res->free(); }
    closeConnection($conn);
    return $rows;
}

function comp_getPasswordPolicy() {
    $conn = getConnection();
    $res = $conn->query("SELECT PolicyID, MinLength, RequireUppercase, RequireNumbers, ExpirationDays FROM PasswordPolicy ORDER BY PolicyID DESC LIMIT 1");
    $row = $res ? $res->fetch_assoc() : null;
    if ($res) $res->free();
    closeConnection($conn);
    return $row;
}

function comp_setPasswordPolicy($minLength, $reqUpper, $reqNum, $expirationDays) {
    $conn = getConnection();
    $minLength = (int)$minLength;
    $reqUpper = (int)$reqUpper;
    $reqNum = (int)$reqNum;
    $expirationDays = (int)$expirationDays;
    
    $sql = "INSERT INTO PasswordPolicy (MinLength, RequireUppercase, RequireNumbers, ExpirationDays) VALUES ($minLength, $reqUpper, $reqNum, $expirationDays)";
    mysqli_query($conn, $sql);
    $ok = (mysqli_insert_id($conn) > 0);
    closeConnection($conn);
    return $ok;
}

function comp_getPrivacyPolicies() {
    $conn = getConnection();
    $rows = [];
    $res = $conn->query("SELECT PolicyID, Title, Content, Version, EffectiveDate FROM PrivacyPolicy ORDER BY EffectiveDate DESC");
    if ($res) { while ($x = $res->fetch_assoc()) { $rows[] = $x; } $res->free(); }
    closeConnection($conn);
    return $rows;
}

function comp_setEncryptionSettings($atRestEnabled, $inTransitEnabled) {
    $conn = getConnection();
    $title = 'Security Settings';
    $content = 'data_at_rest=' . ($atRestEnabled ? 'Enabled' : 'Disabled') . ';' . 'data_in_transit=' . ($inTransitEnabled ? 'Enabled' : 'Disabled');
    $version = 'v' . date('YmdHis');
    $eff = date('Y-m-d');
    
    $title = trim($title);
    $content = trim($content);
    $version = trim($version);
    $eff = trim($eff);

    $sql = "INSERT INTO PrivacyPolicy (Title, Content, Version, EffectiveDate) VALUES ('$title', '$content', '$version', '$eff')";
    mysqli_query($conn, $sql);
    $ok = (mysqli_insert_id($conn) > 0);
    closeConnection($conn);
    return $ok;
}

function comp_getAuditLogs($userIdFilter, $actionFilter, $tableFilter, $limit) {
    $conn = getConnection();
    $q = "SELECT LogID, UserID, Action, TableAffected, RecordID, Timestamp, Details FROM AuditLog WHERE 1=1";
    
    if ($userIdFilter !== "") {
        $userIdFilter = (int)$userIdFilter;
        $q .= " AND UserID = $userIdFilter";
    }
    if ($actionFilter !== "") {
        $actionFilter = trim($actionFilter);
        $q .= " AND Action = '$actionFilter'";
    }
    if ($tableFilter !== "") {
        $tableFilter = trim($tableFilter);
        $q .= " AND TableAffected = '$tableFilter'";
    }
    
    $limit = (int)$limit;
    $q .= " ORDER BY Timestamp DESC LIMIT $limit";

    $res = mysqli_query($conn, $q);
    $rows = [];
    if ($res) {
        while ($x = mysqli_fetch_assoc($res)) { $rows[] = $x; }
    }
    closeConnection($conn);
    return $rows;
}

function comp_getRolePermissions() {
    $conn = getConnection();
    $rows = [];
    $res = $conn->query("SELECT RolePermissionID, Role, Module, Permission FROM RolePermission ORDER BY Role ASC, Module ASC");
    if ($res) { while ($x = $res->fetch_assoc()) { $rows[] = $x; } $res->free(); }
    closeConnection($conn);
    return $rows;
}

function comp_addRolePermission($role, $module, $permission) {
    $conn = getConnection();
    $role = trim($role);
    $module = trim($module);
    $permission = trim($permission);
    
    $sql = "INSERT INTO RolePermission (Role, Module, Permission) VALUES ('$role', '$module', '$permission')";
    mysqli_query($conn, $sql);
    $ok = (mysqli_insert_id($conn) > 0);
    closeConnection($conn);
    return $ok;
}

function comp_deleteRolePermission($rpId) {
    $conn = getConnection();
    $rpId = (int)$rpId;
    $sql = "DELETE FROM RolePermission WHERE RolePermissionID=$rpId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);
    closeConnection($conn);
    return $ok;
}

function comp_setMFA($userId, $method, $secret, $enabled) {
    $conn = getConnection();
    $userId = (int)$userId;
    
    $chkSql = "SELECT MFAID FROM MFA WHERE UserID=$userId";
    $res = mysqli_query($conn, $chkSql);
    
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $en = $enabled ? 1 : 0;
        $method = trim($method);
        $secret = trim($secret);
        
        $uSql = "UPDATE MFA SET Method='$method', Secret='$secret', Enabled=$en WHERE UserID=$userId";
        mysqli_query($conn, $uSql);
    } else {
        $en = $enabled ? 1 : 0;
        $method = trim($method);
        $secret = trim($secret);
        
        $iSql = "INSERT INTO MFA (UserID, Method, Secret, Enabled) VALUES ($userId, '$method', '$secret', $en)";
        mysqli_query($conn, $iSql);
    }
    closeConnection($conn);
    return true;
}

function comp_extendSession($userId) {
    $conn = getConnection();
    $userId = (int)$userId;
    
    $chkSql = "SELECT SessionID FROM Session WHERE UserID=$userId";
    $res = mysqli_query($conn, $chkSql);
    
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $uSql = "UPDATE Session SET LastActivity=NOW() WHERE UserID=$userId";
        mysqli_query($conn, $uSql);
    } else {
        $iSql = "INSERT INTO Session (UserID, LastActivity) VALUES ($userId, NOW())";
        mysqli_query($conn, $iSql);
    }
    closeConnection($conn);
    return true;
}

function comp_generateComplianceReport($generatedByUserId, $type) {
    $conn = getConnection();
    $generatedByUserId = (int)$generatedByUserId;
    $type = trim($type);
    $filePath = '/reports/' . strtolower($type) . '_' . date('Ymd_His') . '.pdf';
    $filePath = trim($filePath);
    
    $sql = "INSERT INTO ComplianceReport (GeneratedBy, GeneratedAt, Type, FilePath) VALUES ($generatedByUserId, NOW(), '$type', '$filePath')";
    mysqli_query($conn, $sql);
    $ok = (mysqli_insert_id($conn) > 0);
    closeConnection($conn);
    return $ok;
}

function comp_createAlertNotification($recipientUserId, $channel, $scheduledAt) {
    $conn = getConnection();
    $recipientUserId = (int)$recipientUserId;
    $channel = trim($channel);
    $scheduledAt = trim($scheduledAt);
    
    $sql = "INSERT INTO Notification (RecipientUserID, Channel, ScheduledAt, SentAt) VALUES ($recipientUserId, '$channel', '$scheduledAt', NULL)";
    mysqli_query($conn, $sql);
    $ok = (mysqli_insert_id($conn) > 0);
    closeConnection($conn);
    return $ok;
}

function comp_getConsents($limit = 200) {
    $conn = getConnection();
    $rows = [];
    $limit = (int)$limit;
    $res = $conn->query("SELECT ConsentID, PatientID, Purpose, GivenAt, PolicyVersion FROM Consent ORDER BY GivenAt DESC LIMIT $limit");
    if ($res) { while ($x = $res->fetch_assoc()) { $rows[] = $x; } $res->free(); }
    closeConnection($conn);
    return $rows;
}

function comp_addConsent($patientId, $purpose, $policyVersion) {
    $conn = getConnection();
    $patientId = (int)$patientId;
    $purpose = trim($purpose);
    $policyVersion = trim($policyVersion);
    
    $sql = "INSERT INTO Consent (PatientID, Purpose, GivenAt, PolicyVersion) VALUES ($patientId, '$purpose', NOW(), '$policyVersion')";
    mysqli_query($conn, $sql);
    $ok = (mysqli_insert_id($conn) > 0);
    closeConnection($conn);
    return $ok;
}

function comp_revokeConsent($consentId) {
    $conn = getConnection();
    $consentId = (int)$consentId;
    $sql = "DELETE FROM Consent WHERE ConsentID=$consentId";
    mysqli_query($conn, $sql);
    $ok = (mysqli_affected_rows($conn) > 0);
    closeConnection($conn);
    return $ok;
}
