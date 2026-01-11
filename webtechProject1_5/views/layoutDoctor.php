
<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    </div>

    <nav class="sidebar-menu">
        <a href="home.php">🏠 Home</a>

        <!-- Recommended: add dashboard link -->
        <a href="../controllers/DoctorDashboardController.php">🩺 Doctor Dashboard</a>

        <!-- Keep placeholder or connect to your future appointment page/controller -->
        <!-- <a href="#">📅 Appointments</a> -->

        <!-- IMPORTANT: use ONE logout controller consistently -->
        <a href="../controllers/logoutCheck.php" class="logout">Logout</a>
        <!-- If you already use logoutCheck.php instead, then use:
        <a href="../controllers/logoutCheck.php" class="logout">Logout</a>
        -->
    </nav>
</div>

<!-- TOPBAR / HEADER -->
<header class="topbar">
    <div class="topbar-left">
        <span class="topbar-title">MediTrust Hospital</span>
    </div>
    <div class="topbar-right">
        <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    </div>
</header>
