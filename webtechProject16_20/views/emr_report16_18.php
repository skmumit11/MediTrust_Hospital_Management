<!DOCTYPE html>
<html>

<head>
  <title>EMR Report - Meditrust</title>
  <link rel="stylesheet" href="../assets/style_layoutUser16_18.css">
</head>

<body>

  <?php include 'layoutAdmin16_18.php'; ?>

  <div class="main-content">
    <div class="dashboard-header">
      <h2>EMR Report</h2>
      <p>Electronic Medical Records Timeline</p>
    </div>

    <div class="form-container">
      <form method="get" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
        <div class="form-group" style="margin:0;">
          <label>Patient ID</label>
          <input type="number" name="patientID" class="form-control"
            value="<?= htmlspecialchars($_GET['patientID'] ?? '') ?>" required placeholder="Enter Patient ID">
        </div>
        <div class="form-group" style="margin:0;">
          <label>From Date</label>
          <input type="date" name="dateFrom" class="form-control"
            value="<?= htmlspecialchars($_GET['dateFrom'] ?? '') ?>">
        </div>
        <div class="form-group" style="margin:0;">
          <label>To Date</label>
          <input type="date" name="dateTo" class="form-control" value="<?= htmlspecialchars($_GET['dateTo'] ?? '') ?>">
        </div>
        <button type="submit" class="btn-primary" style="height: 42px;">Search Records</button>
      </form>
    </div>

    <?php if (isset($_GET['patientID'])): ?>
      <div class="card" style="width:100%; box-sizing:border-box; background:#fff;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
          <h3 style="margin: 0; color: #444;">Patient History</h3>
          <a href="exportEmrPdf.php?patientID=<?= htmlspecialchars($_GET['patientID']) ?>" class="btn-primary"
            style="padding: 8px 16px; font-size: 14px; text-decoration: none;">Download PDF</a>
        </div>

        <table class="styled-table">
          <thead>
            <tr>
              <th>Date & Time</th>
              <th>Vitals</th>
              <th>Diagnosis</th>
              <th>Prescription</th>
              <th>Attachments</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($res && mysqli_num_rows($res) > 0) { ?>
              <?php while ($r = mysqli_fetch_assoc($res)) { ?>
                <tr>
                  <td style="white-space:nowrap;"><?= $r['VisitDate'] ?></td>
                  <td><?= $r['Vitals'] ?></td>
                  <td>
                    <span style="font-weight: 600; color: #333;"><?= $r['DiagnosisICD'] ?></span>
                  </td>
                  <td>
                    <div style="background: #f9f9f9; padding: 5px; border-radius: 4px; font-size: 0.9em;">
                      <?= $r['Medicine'] . ' ' . $r['Dosage'] . ' (' . $r['Duration'] . ')' ?>
                    </div>
                  </td>
                  <td>
                    <?php if ($r['Attachments']): ?>
                      <a href="#" style="color: #386D44; text-decoration: underline;">View File</a>
                    <?php else: ?>
                      <span style="color:#ccc;">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="5" style="text-align: center; padding: 30px; color: #666;">No medical records found for this
                  period.</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</body>

</html>