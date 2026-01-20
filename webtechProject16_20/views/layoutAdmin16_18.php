<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        Admin
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    </div>

    <nav class="sidebar-menu">


        <!--<a href="admindashboard16_18.php">Admin</a>-->
        <!-- Feature 17: Notifications -->
        <a href="adminNotificationController16_18.php">📩 Notification System</a>
        <!-- Feature 18: Reports & Analytics -->
        <a href="analyticsReportController16_18.php">📊 Operational Reports</a>
        <a href="dashboardController16_18.php">📈 Dashboards</a>
        <!-- Feature 16: Electronic Medical Records -->
        <a href="../controllers/encounterController16_18.php">🩺 New Encounter</a>
        <a href="../controllers/emrReportController16_18.php">📄 EMR Report</a>
        <a class="logout" href="../../webtechProject1_5/controllers/logoutCheck.php">Logout</a>
    </nav>
</div>

<!-- TOPBAR / HEADER -->
<header class="topbar">
    <div class="topbar-left">
        <span class="topbar-title">MediTrust Hospital</span>
    </div>
    <div class="topbar-right">
        <!-- Topbar toggle button on the right -->
        <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    </div>
</header>
<script src="../assets/sidebar16_18.js"></script>