<!-- SIDEBAR -->
<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
// Role checking removed as per request to revert to simple access
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <!-- <span class="logo-text">MediTrust</span> -->
        <!-- Sidebar toggle button inside sidebar -->
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    </div>

    <nav class="sidebar-menu">
        <a href="admindashboard6_10.php">Admin Dashboard</a>

        <a href="testResultEntry6_10.php">Lab Test Entry</a>
        <a href="reportManagement6_10.php">Lab Reports</a>

        <a href="stafflist6_10.php">Staff Profiles</a>
        <a href="staff_add6_10.php">Add Staff</a>
        <a href="insurance_create6_10.php">Insurance Entry</a>

        <a href="generateBill6_10.php">Billing (Cashier)</a>

        <a class="logout" href="../controllers/logoutCheck6_10.php">Logout</a>
    </nav>
</div>

<!-- TOPBAR / HEADER -->
<header class="topbar">
    <div class="topbar-left">
        <span class="topbar-title">MediTrust Hospital</span>
    </div>
    <div class="topbar-right">
        <!-- Topbar toggle button on the right -->
        <span style="margin-right: 15px; color: #555;">(
            <?php echo htmlspecialchars($role); ?>)
        </span>
        <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    </div>
</header>