
<?php
require_once __DIR__ . '/db.php';

function comp_getUserIdByUsername($username) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT UserID FROM `User` WHERE Username=?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $r = $stmt->get_result();
    $id = null;
    if ($row = $r->fetch_assoc()) { $id = (int)$row['UserID']; }
    $stmt->close();
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
    $stmt = $conn->prepare("INSERT INTO PasswordPolicy (MinLength, RequireUppercase, RequireNumbers, ExpirationDays) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiii', $minLength, $reqUpper, $reqNum, $expirationDays);
    $stmt->execute();
    $ok = ($stmt->insert_id > 0);
    $stmt->close();
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
    $stmt = $conn->prepare("INSERT INTO PrivacyPolicy (Title, Content, Version, EffectiveDate) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $title, $content, $version, $eff);
    $stmt->execute();
    $ok = ($stmt->insert_id > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}

function comp_getAuditLogs($userIdFilter, $actionFilter, $tableFilter, $limit) {
    $conn = getConnection();
    $q = "SELECT LogID, UserID, Action, TableAffected, RecordID, Timestamp, Details FROM AuditLog WHERE 1=1";
    $binds = [];
    $types = "";

    if ($userIdFilter !== "") {
        $q .= " AND UserID = ?";
        $types .= "i";
        $binds[] = (int)$userIdFilter;
    }
    if ($actionFilter !== "") {
        $q .= " AND Action = ?";
        $types .= "s";
        $binds[] = $actionFilter;
    }
    if ($tableFilter !== "") {
        $q .= " AND TableAffected = ?";
        $types .= "s";
        $binds[] = $tableFilter;
    }
    $q .= " ORDER BY Timestamp DESC LIMIT ?";

    $types .= "i";
    $binds[] = (int)$limit;

    $stmt = $conn->prepare($q);
    if (count($binds) === 1) {
        $stmt->bind_param($types, $binds[0]);
    } else if (count($binds) === 2) {
        $stmt->bind_param($types, $binds[0], $binds[1]);
    } else if (count($binds) === 3) {
        $stmt->bind_param($types, $binds[0], $binds[1], $binds[2]);
    } else if (count($binds) === 4) {
        $stmt->bind_param($types, $binds[0], $binds[1], $binds[2], $binds[3]);
    }

    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($x = $res->fetch_assoc()) { $rows[] = $x; }
    $stmt->close();
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
    $stmt = $conn->prepare("INSERT INTO RolePermission (Role, Module, Permission) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $role, $module, $permission);
    $stmt->execute();
    $ok = ($stmt->insert_id > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}

function comp_deleteRolePermission($rpId) {
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM RolePermission WHERE RolePermissionID=?");
    $stmt->bind_param('i', $rpId);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}

function comp_setMFA($userId, $method, $secret, $enabled) {
    $conn = getConnection();
    $chk = $conn->prepare("SELECT MFAID FROM MFA WHERE UserID=?");
    $chk->bind_param('i', $userId);
    $chk->execute();
    $res = $chk->get_result();
    if ($row = $res->fetch_assoc()) {
        $chk->close();
        $u = $conn->prepare("UPDATE MFA SET Method=?, Secret=?, Enabled=? WHERE UserID=?");
        $en = $enabled ? 1 : 0;
        $u->bind_param('ssii', $method, $secret, $en, $userId);
        $u->execute();
        $u->close();
    } else {
        $chk->close();
        $i = $conn->prepare("INSERT INTO MFA (UserID, Method, Secret, Enabled) VALUES (?, ?, ?, ?)");
        $en = $enabled ? 1 : 0;
        $i->bind_param('issi', $userId, $method, $secret, $en);
        $i->execute();
        $i->close();
    }
    closeConnection($conn);
    return true;
}

function comp_extendSession($userId) {
    $conn = getConnection();
    $chk = $conn->prepare("SELECT SessionID FROM Session WHERE UserID=?");
    $chk->bind_param('i', $userId);
    $chk->execute();
    $res = $chk->get_result();
    if ($row = $res->fetch_assoc()) {
        $chk->close();
        $u = $conn->prepare("UPDATE Session SET LastActivity=NOW() WHERE UserID=?");
        $u->bind_param('i', $userId);
        $u->execute();
        $u->close();
    } else {
        $chk->close();
        $i = $conn->prepare("INSERT INTO Session (UserID, LastActivity) VALUES (?, NOW())");
        $i->bind_param('i', $userId);
        $i->execute();
        $i->close();
    }
    closeConnection($conn);
    return true;
}

function comp_generateComplianceReport($generatedByUserId, $type) {
    $conn = getConnection();
    $filePath = '/reports/' . strtolower($type) . '_' . date('Ymd_His') . '.pdf';
    $stmt = $conn->prepare("INSERT INTO ComplianceReport (GeneratedBy, GeneratedAt, Type, FilePath) VALUES (?, NOW(), ?, ?)");
    $stmt->bind_param('iss', $generatedByUserId, $type, $filePath);
    $stmt->execute();
    $ok = ($stmt->insert_id > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}

function comp_createAlertNotification($recipientUserId, $channel, $scheduledAt) {
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO Notification (RecipientUserID, Channel, ScheduledAt, SentAt) VALUES (?, ?, ?, NULL)");
    $stmt->bind_param('iss', $recipientUserId, $channel, $scheduledAt);
    $stmt->execute();
    $ok = ($stmt->insert_id > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}

function comp_getConsents($limit = 200) {
    $conn = getConnection();
    $rows = [];
    $res = $conn->query("SELECT ConsentID, PatientID, Purpose, GivenAt, PolicyVersion FROM Consent ORDER BY GivenAt DESC LIMIT " . (int)$limit);
    if ($res) { while ($x = $res->fetch_assoc()) { $rows[] = $x; } $res->free(); }
    closeConnection($conn);
    return $rows;
}

function comp_addConsent($patientId, $purpose, $policyVersion) {
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO Consent (PatientID, Purpose, GivenAt, PolicyVersion) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param('iss', $patientId, $purpose, $policyVersion);
    $stmt->execute();
    $ok = ($stmt->insert_id > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}

function comp_revokeConsent($consentId) {
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM Consent WHERE ConsentID=?");
    $stmt->bind_param('i', $consentId);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();
    closeConnection($conn);
    return $ok;
}
