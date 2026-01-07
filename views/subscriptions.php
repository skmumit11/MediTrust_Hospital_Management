
<?php
// views/subscriptions.php
if (!isset($_SESSION)) { session_start(); }
require_once ('../controllers/subscriptionController.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>MediTrust — Subscriptions</title>
    <link rel="stylesheet" href="../assets/style_patientdashboard.css">
    <link rel="stylesheet" href="../assets/style_layoutUser.css">
    <script src="../assets/sidebar.js"></script>
</head>
<body>

<?php if (file_exists('layoutPatient.php')) { include ('layoutPatient.php'); } ?>

<div class="main-content">
  <section class="hero-container">
    <h1>Subscriptions</h1>
    <p class="hero-subtitle">Choose a package, pay securely, and manage your plan.</p>
  </section>

  <?php if (isset($message) && $message !== "") { ?>
    <div class="<?php echo ($messageType === "success") ? "alert alert-success" : "alert alert-error"; ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php } ?>

  <section class="panel">
    <h2>Available Packages</h2>
    <div class="packages-grid">
      <?php foreach ($plansList as $pl) { ?>
        <div class="pkg-card">
          <div class="pkg-name"><?php echo htmlspecialchars($pl['PlanName']); ?></div>
          <div class="pkg-price">Price: <?php echo number_format((float)$pl['Price'], 2); ?></div>
          <div class="pkg-duration">Duration: <?php echo (int)$pl['Duration']; ?> days</div>
          <form method="post" action="">
            <input type="hidden" name="action" value="pay_subscription">
            <input type="hidden" name="PlanID" value="<?php echo (int)$pl['PlanID']; ?>">
            <div class="card-form">
              <input type="text" name="CardHolder" placeholder="Card Holder Name" required>
              <input type="text" name="CardNumber" placeholder="Card Number" required>
              <input type="text" name="CardExpiry" placeholder="Expiry (YYYY-MM-DD)" required>
              <input type="text" name="CVV" placeholder="CVV" required>
            </div>
            <button type="submit" class="btn btn-sm">Select & Pay</button>
          </form>
        </div>
      <?php } ?>
    </div>
  </section>

  <section class="panel">
    <h2>Active Subscription</h2>
    <?php if ($currentActive) { ?>
      <p><b>Package:</b>
        <?php
          $ap = sub_getPlanById((int)$currentActive['PlanID']);
          echo htmlspecialchars($ap ? $ap['PlanName'] : '');
        ?>
      </p>
      <p><b>Expiry:</b> <?php echo htmlspecialchars($currentActive['ExpiryDate']); ?></p>
      <form method="post" action="">
        <input type="hidden" name="action" value="cancel_subscription">
        <input type="hidden" name="SubscriptionID" value="<?php echo (int)$currentActive['SubscriptionID']; ?>">
        <button type="submit" class="btn btn-sm">Cancel Subscription</button>
      </form>
    <?php } else { ?>
      <p>No active subscription.</p>
    <?php } ?>

    <button class="btn" onclick="window.location.href='patientdashboard.php'" >Back</button>
  </section>
</div>

<div class="footer">
  <?php include ('footer.php'); ?>
</div>

</body>
</html>
