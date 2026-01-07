
<?php
if (!isset($_SESSION)) { session_start(); }

require_once ('../models/complianceModel.php');

$message = "";
$messageType = "success";

$AUTO_LOGOUT_MINUTES = 10;

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$currentUserId = ($username !== '') ? comp_getUserIdByUsername($username) : null;

$usersList = comp_getAllUsers();
$patientsList = comp_getAllPatients();
$passwordPolicy = comp_getPasswordPolicy();
$privacyPolicies = comp_getPrivacyPolicies();
$rolePermissions = comp_getRolePermissions();
$consents = comp_getConsents(200);

$audit_user_id = isset($_POST['audit_user_id']) ? trim($_POST['audit_user_id']) : "";
$audit_action  = isset($_POST['audit_action']) ? trim($_POST['audit_action']) : "";
$audit_table   = isset($_POST['audit_table']) ? trim($_POST['audit_table']) : "";
$audit_logs = comp_getAuditLogs($audit_user_id, $audit_action, $audit_table, 200);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'extend_session') {
        if ($currentUserId !== null) {
            comp_extendSession($currentUserId);
            $message = "Session extended.";
            $messageType = "success";
        } else {
            $message = "Login required.";
            $messageType = "error";
        }
    }

    else if ($action === 'update_password_policy') {
        $minLen = (int)($_POST['MinLength'] ?? 8);
        $reqUpper = isset($_POST['RequireUppercase']) ? 1 : 0;
        $reqNum   = isset($_POST['RequireNumbers']) ? 1 : 0;
        $expDays  = (int)($_POST['ExpirationDays'] ?? 90);
        $ok = comp_setPasswordPolicy($minLen, $reqUpper, $reqNum, $expDays);
        $message = $ok ? "Password policy updated." : "Failed to update password policy.";
        $messageType = $ok ? "success" : "error";
        $passwordPolicy = comp_getPasswordPolicy();
    }

    else if ($action === 'set_mfa') {
        $uid = (int)($_POST['UserID'] ?? 0);
        $method = $_POST['Method'] ?? 'Email';
        $secret = $_POST['Secret'] ?? 'SECRET';
        $enabled = isset($_POST['Enabled']) ? 1 : 0;
        $ok = comp_setMFA($uid, $method, $secret, $enabled);
        $message = $ok ? "MFA updated." : "Failed to update MFA.";
        $messageType = $ok ? "success" : "error";
    }

    else if ($action === 'set_encryption') {
        $atRest = isset($_POST['AtRest']) ? 1 : 0;
        $inTransit = isset($_POST['InTransit']) ? 1 : 0;
        $ok = comp_setEncryptionSettings($atRest, $inTransit);
        $message = $ok ? "Encryption settings saved." : "Failed to save encryption settings.";
        $messageType = $ok ? "success" : "error";
        $privacyPolicies = comp_getPrivacyPolicies();
    }

    else if ($action === 'rbac_add') {
        $role = $_POST['Role'] ?? '';
        $module = $_POST['Module'] ?? '';
        $perm = $_POST['Permission'] ?? '';
        $ok = comp_addRolePermission($role, $module, $perm);
        $message = $ok ? "Permission added." : "Failed to add permission.";
        $messageType = $ok ? "success" : "error";
        $rolePermissions = comp_getRolePermissions();
    }

    else if ($action === 'rbac_delete') {
        $rpid = (int)($_POST['RolePermissionID'] ?? 0);
        $ok = comp_deleteRolePermission($rpid);
        $message = $ok ? "Permission deleted." : "Failed to delete permission.";
        $messageType = $ok ? "success" : "error";
        $rolePermissions = comp_getRolePermissions();
    }

    else if ($action === 'generate_compliance') {
        $type = $_POST['ReportType'] ?? 'HIPAA';
        $uid = $currentUserId ?? 0;
        $ok = comp_generateComplianceReport($uid, $type);
        $message = $ok ? "Report generated." : "Failed to generate report.";
        $messageType = $ok ? "success" : "error";
    }

    else if ($action === 'create_alert') {
        $rid = (int)($_POST['RecipientUserID'] ?? 0);
        $channel = $_POST['Channel'] ?? 'App';
        $when = date('Y-m-d H:i:s');
        $ok = comp_createAlertNotification($rid, $channel, $when);
        $message = $ok ? "Alert created." : "Failed to create alert.";
        $messageType = $ok ? "success" : "error";
    }

    else if ($action === 'consent_add') {
        $pid = (int)($_POST['PatientID'] ?? 0);
        $purpose = $_POST['Purpose'] ?? '';
        $policyVersion = $_POST['PolicyVersion'] ?? 'v1.0';
        $ok = comp_addConsent($pid, $purpose, $policyVersion);
        $message = $ok ? "Consent recorded." : "Failed to record consent.";
        $messageType = $ok ? "success" : "error";
        $consents = comp_getConsents(200);
    }

    else if ($action === 'consent_revoke') {
        $cid = (int)($_POST['ConsentID'] ?? 0);
        $ok = comp_revokeConsent($cid);
        $message = $ok ? "Consent revoked." : "Failed to revoke consent.";
        $messageType = $ok ? "success" : "error";
        $consents = comp_getConsents(200);
    }

    $audit_user_id = isset($_POST['audit_user_id']) ? trim($_POST['audit_user_id']) : "";
    $audit_action  = isset($_POST['audit_action']) ? trim($_POST['audit_action']) : "";
    $audit_table   = isset($_POST['audit_table']) ? trim($_POST['audit_table']) : "";
    $audit_logs = comp_getAuditLogs($audit_user_id, $audit_action, $audit_table, 200);
}

$autoLogoutMinutes = $AUTO_LOGOUT_MINUTES;
