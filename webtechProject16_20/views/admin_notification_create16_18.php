<!DOCTYPE html>
<html>

<head>
  <title>Notification System - Meditrust</title>
  <link rel="stylesheet" href="../assets/style_layoutUser16_18.css">
</head>

<body>

  <?php include 'layoutAdmin16_18.php'; ?>

  <div class="main-content">
    <div class="dashboard-header">
      <h2>Notification System</h2>
      <p>Schedule and manage alerts for patients and doctors</p>
    </div>

    <?php if (!empty($msg)): ?>
      <div
        style="padding: 15px; margin-bottom: 20px; border-radius: 4px; color: #fff; background-color: <?= strpos($msg, 'Error') !== false ? '#e74c3c' : '#2ecc71' ?>;">
        <?= $msg ?>
      </div>
    <?php endif; ?>

    <div style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 30px;">
      <!-- Left Column: Scheduler Form -->
      <div class="form-container" style="flex: 1; min-width: 350px; margin: 0;">
        <h3 class="section-title" style="border:none; margin-bottom:20px;">Schedule New Notification</h3>
        <form method="post">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Row 1 -->
            <div class="form-group">
              <label>Recipient UserID</label>
              <input type="number" name="recipientUserID" class="form-control" required placeholder="e.g. 101">
            </div>

            <div class="form-group">
              <label>Channel</label>
              <select name="channel" class="form-control">
                <option>Email</option>
                <option>SMS</option>
                <option>App</option>
              </select>
            </div>

            <!-- Row 2 -->
            <div class="form-group">
              <label>Date</label>
              <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
              <label>Time (12-Hour)</label>
              <div style="display: flex; gap: 10px;">
                <input type="number" name="hour" class="form-control" placeholder="Hour" min="1" max="12" required>
                <select name="ampm" class="form-control" style="width: 80px;">
                  <option value="AM">AM</option>
                  <option value="PM">PM</option>
                </select>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-primary" style="margin-top: 10px;">Schedule Notification</button>
        </form>
      </div>

      <!-- Right Column: Doctor List -->
      <div class="card" style="flex: 0 0 300px; box-sizing:border-box; background:#fff; margin: 0;">
        <h3 class="section-title" style="border:none;">Doctors Reference List</h3>
        <p style="color:#666; margin-bottom:15px;">Use these IDs for the recipient field.</p>
        <table class="styled-table" style="margin-top: 0;">
          <thead>
            <tr>
              <th>Doctor ID</th>
              <th>Name</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($doctors && mysqli_num_rows($doctors) > 0) {
              while ($d = mysqli_fetch_assoc($doctors)) { ?>
                <tr>
                  <td><strong><?= $d['DoctorID'] ?></strong></td>
                  <td><?= $d['Name'] ?></td>
                </tr>
              <?php }
            } else { ?>
              <tr>
                <td colspan="2" style="text-align:center;">No doctors found.</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>



    <!-- Sent Notifications -->
    <div class="card" style="width:100%; box-sizing:border-box; background:#fff; margin-top:30px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 class="section-title" style="border:none; margin:0;">Sent Log</h3>
        <form method="post" onsubmit="return confirm('Are you sure you want to clear the entire sent log?');"
          style="margin:0;">
          <button type="submit" name="clear_log" class="btn-primary"
            style="width: auto; padding: 8px 15px; font-size: 14px; background: #e74c3c;">Clear Log</button>
        </form>
      </div>
      <table class="styled-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Recipient</th>
            <th>Channel</th>
            <th>Scheduled At</th>
            <th>Sent At</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($sent && mysqli_num_rows($sent) > 0) {
            $i = 1;
            while ($s = mysqli_fetch_assoc($sent)) { ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= $s['Recipient'] ?></td>
                <td><?= $s['Channel'] ?></td>
                <td><?= $s['ScheduledAt'] ?></td>
                <td><?= $s['SentAt'] ?></td>
              </tr>
            <?php }
          } else { ?>
            <tr>
              <td colspan="5" style="text-align:center;">No sent notifications found.</td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

</body>

</html>