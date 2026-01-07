<!DOCTYPE html>
<html>

<head>
  <title>Admin Dashboard - Meditrust</title>
  <link rel="stylesheet" href="../assets/style_layoutUser16_18.css">
</head>

<body>

  <?php include 'layoutAdmin16_18.php'; ?>

  <div class="main-content">
    <div class="dashboard-header">
      <span class="date-display"><?= date("l, d F Y") ?></span>
      <h2>Dashboard</h2>
      <p>Welcome back, Admin User!</p>
    </div>

    <!-- Stats Row -->
    <div class="stats-container">
      <!-- Patients Card -->
      <div class="stat-card blue">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
          <h3><?= $kpis['opd'] + $kpis['ipd'] ?></h3>
          <p>TOTAL PATIENTS</p>
        </div>
      </div>

      <!-- Lab Tests Card -->
      <div class="stat-card purple">
        <div class="stat-icon">🧪</div>
        <div class="stat-info">
          <h3><?= 7 ?></h3>
          <p>LAB TESTS DONE</p>
        </div>
      </div>

      <!-- Revenue Card -->
      <div class="stat-card green">
        <div class="stat-icon">৳</div>
        <div class="stat-info">
          <h3>৳<?= $kpis['revenue'] ?? 0 ?></h3>
          <p>TODAY'S REVENUE</p>
        </div>
      </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="section-title">Quick Actions</div>

    <div class="actions-grid">
      <a href="bill_create.php" class="action-card">
        <div class="action-icon">📄</div>
        <span>New Invoice</span>
      </a>

      <a href="lab_entry.php" class="action-card">
        <div class="action-icon">🔬</div>
        <span>Lab Entry</span>
      </a>

      <a href="insurance.php" class="action-card">
        <div class="action-icon">✚</div>
        <span>Insurance</span>
      </a>

      <a href="staff_add.php" class="action-card">
        <div class="action-icon">👤+</div>
        <span>Add Staff</span>
      </a>
    </div>
  </div>

</body>

</html>