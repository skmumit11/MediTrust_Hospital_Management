<?php
session_start();
// Security Check
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}

require_once '../models/dashboardModel.php';
$stats = getDashboardStats();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Dashboard - Meditrust</title>
  <!-- Reuse the main layout CSS which has the premium Glassmorphism look -->
  <link rel="stylesheet" href="../assets/style_layoutUser.css">
  <link rel="stylesheet" href="../assets/style_lab.css">
  <script src="../assets/sidebar.js"></script>
  <style>
    /* Dashboard Specific Styles */
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 25px;
      margin-bottom: 40px;
    }

    .stat-card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      display: flex;
      align-items: center;
      transition: transform 0.2s;
      border-left: 5px solid transparent;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-card.blue {
      border-left-color: #4A90E2;
    }

    .stat-card.green {
      border-left-color: #2ECC71;
    }

    .stat-card.purple {
      border-left-color: #9B59B6;
    }

    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-right: 20px;
      background: #f8f9fa;
    }

    .stat-card.blue .stat-icon {
      color: #4A90E2;
      background: rgba(74, 144, 226, 0.1);
    }

    .stat-card.green .stat-icon {
      color: #2ECC71;
      background: rgba(46, 204, 113, 0.1);
    }

    .stat-card.purple .stat-icon {
      color: #9B59B6;
      background: rgba(155, 89, 182, 0.1);
    }

    .stat-content h3 {
      margin: 0;
      font-size: 28px;
      color: #2c3e50;
      font-weight: 700;
    }

    .stat-content p {
      margin: 5px 0 0;
      color: #7f8c8d;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      color: #2c3e50;
    }

    .actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
    }

    .action-btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 30px 20px;
      background: white;
      border-radius: 12px;
      text-decoration: none;
      color: #555;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      transition: 0.3s;
      border: 1px solid #eee;
    }

    .action-btn:hover {
      background: #386D44;
      color: white;
      border-color: #386D44;
      transform: translateY(-3px);
    }

    .action-btn i {
      font-size: 32px;
      margin-bottom: 15px;
    }

    .action-btn span {
      font-weight: 600;
    }
  </style>
  <!-- FontAwesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

  <?php include 'layoutAdmin_6_10.php'; ?>

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

      <!-- QUICK ACTIONS Section -->
      <h2
        style="font-size: 20px; margin-bottom: 20px; color: #444; border-left: 4px solid #386D44; padding-left: 10px;">
        Quick Actions</h2>

      <div class="actions-grid">
        <a href="billing/generateBill.php" class="action-btn">
          <i class="fas fa-file-invoice-dollar"></i>
          <span>New Invoice</span>
        </a>

        <a href="lab/testResultEntry.php" class="action-btn">
          <i class="fas fa-vial"></i>
          <span>Lab Entry</span>
        </a>

        <a href="insurance_create.php" class="action-btn">
          <i class="fas fa-file-medical"></i>
          <span>Insurance</span>
        </a>

        <a href="staff_add.php" class="action-btn">
          <i class="fas fa-user-plus"></i>
          <span>Add Staff</span>
        </a>
      </div>

    </div>
  </div>

</body>

</html>