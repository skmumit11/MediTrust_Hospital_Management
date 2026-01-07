<!-- layoutSecuruty.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MediTrust - Security & Compliance</title>
    <link rel="stylesheet" href="../assets/style_monitoring_reporting.css">
    <script src="../assets/sidebar.js"></script>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    </div>

    <nav class="sidebar-menu">
        <a href="admindashboard.php">Dashboard</a>
        <a href="style_monitoring_reporting.php">Monitoring & Reporting</a>
        <a href="#">Users</a>
        <a href="#">Audit Logs</a>
        <a href="#">Roles & Permissions</a>
        <a href="#">Compliance Reports</a>
        <a href="#">Privacy Policy</a>
        <a href="../controllers/logoutCheck.php" class="logout">Logout</a>
    </nav>
</div>

<!-- TOPBAR / HEADER -->
<header class="topbar">
    <div class="topbar-left">
        <span class="topbar-title">MediTrust - Security</span>
    </div>
    <div class="topbar-right">
        <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    </div>
</header>

<!-- MAIN CONTENT START -->
<div class="main-content">
