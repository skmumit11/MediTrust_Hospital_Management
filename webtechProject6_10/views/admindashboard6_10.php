<?php
// views/admindashboard6_10.php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login6_10.php");
  exit();
}
require_once '../models/dashboardModel6_10.php';
$stats = getDashboardStats();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Dashboard - Meditrust</title>
  <link rel="stylesheet" href="../assets/style_layoutUser6_10.css">
  <link rel="stylesheet" href="../assets/style_lab6_10.css">
  <link rel="stylesheet" href="../assets/style_admindashboard6_10.css">
  <script src="../assets/sidebar6_10.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

  <?php include_once 'layoutAdmin6_10.php'; ?>

  <div class="main-content" style="margin-top: 80px;">
    <div class="container-fluid">
      <div class="section-header">
        <div>
          <h1 style="font-size: 28px; margin-bottom: 5px;">Dashboard</h1>
          <p style="color: #666; margin: 0;">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['Name']); ?>!
          </p>
        </div>
        <div style="text-align: right; color: #888;">
          <?php echo date('l, d F Y'); ?>
        </div>
      </div>

      <!-- STATS CARDS -->
      <div class="dashboard-grid">
        <div class="stat-card blue">
          <div class="stat-icon"><i class="fas fa-users"></i></div>
          <div class="stat-content">
            <h3><?php echo number_format($stats['total_patients']); ?></h3>
            <p>Total Patients</p>
          </div>
        </div>

        <div class="stat-card purple">
          <div class="stat-icon"><i class="fas fa-flask"></i></div>
          <div class="stat-content">
            <h3><?php echo number_format($stats['total_tests']); ?></h3>
            <p>Lab Tests Done</p>
          </div>
        </div>

        <div class="stat-card green">
          <div class="stat-icon"><i class="fas fa-coins"></i></div>
          <div class="stat-content">
            <h3>৳<?php echo number_format($stats['today_revenue']); ?></h3>
            <p>Today's Revenue</p>
          </div>
        </div>
      </div>



    </div>
  </div>

</body>

</html>