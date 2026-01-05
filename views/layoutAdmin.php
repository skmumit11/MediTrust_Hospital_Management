
<?php /* views/layoutAdmin.php */ ?>
<link rel="stylesheet" href="../assets/style_layout.css">
<script src="../assets/sidebar.js"></script>

<div class="topbar">
  <div class="topbar-title">MediTrust Admin</div>
  <button class="menu-btn" aria-label="Open Menu">☰</button>
</div>

<div class="sidebar" id="adminSidebar">
  <div class="sidebar-header">
    <div class="logo-text">Menu</div>
    <button class="toggle-btn" aria-label="Close Menu">✕</button>
  </div>

  <div class="sidebar-menu">
    <a href="admindashboard.php" >Dashboard</a>
    <a href="appointmentAdd.php" >Add Appointment</a>
    <a href="doctorAdd.php" >Add Doctor</a>
    <a href="patientAdd.php" >Add Patient</a>
    <a href="bedAdd.php" >Add Bed</a>
    <a href="bedAllocationList.php" >Bed Allocation</a>
    <a href="../controllers/logoutCheck.php" class=" logout">Logout</a>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var sidebar = document.querySelector('.sidebar');
  var openBtn = document.querySelector('.menu-btn');
  var closeBtn = document.querySelector('.toggle-btn');
  if (openBtn) openBtn.addEventListener('click', function(){ sidebar.classList.add('active'); });
  if (closeBtn) closeBtn.addEventListener('click', function(){ sidebar.classList.remove('active'); });
});
</script>
``
