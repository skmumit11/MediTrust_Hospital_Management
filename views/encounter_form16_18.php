<!DOCTYPE html>
<html>

<head>
  <title>New Encounter - Meditrust</title>
  <link rel="stylesheet" href="../assets/style_layoutUser16_18.css">
</head>

<body>

  <?php include 'layoutAdmin16_18.php'; ?>

  <div class="main-content">
    <div class="dashboard-header">
      <h2>New Patient Encounter</h2>
      <p>Record vitals, diagnosis, and prescriptions</p>
    </div>

    <?php if (!empty($msg)): ?>
      <div
        style="background: #eafaf1; border-left: 4px solid #386D44; padding: 15px; margin-bottom: 20px; border-radius: 4px; color: #2c5836;">
        <?= $msg ?>
      </div>
    <?php endif; ?>

    <div class="form-container">
      <form method="post">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <div class="form-group">
            <label>Patient ID</label>
            <input type="number" name="patientID" class="form-control" required placeholder="Patient ID">
          </div>
          <div class="form-group">
            <label>Doctor ID</label>
            <input type="number" name="doctorID" class="form-control" required placeholder="Doctor ID">
          </div>
        </div>

        <div class="form-group">
          <label>Vitals (BP, Temp, Weight, etc.)</label>
          <textarea name="vitals" class="form-control" placeholder="e.g. BP: 120/80, Temp: 98.6F"></textarea>
        </div>

        <div class="form-group">
          <label>Diagnosis (ICD Code / Description)</label>
          <input type="text" name="diagnosisICD" class="form-control" placeholder="e.g. Acute Bronchitis">
        </div>

        <div class="form-group">
          <label>Allergies</label>
          <input type="text" name="allergies[]" class="form-control"
            placeholder="e.g. Penicillin (comma separate if needed handled by logic)">
        </div>

        <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
          <h4 style="margin-top:0; color: #386D44;">Prescription</h4>
          <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 10px;">
            <div class="form-group">
              <label>Medicine</label>
              <input type="text" name="medicine[]" class="form-control" placeholder="Medicine Name">
            </div>
            <div class="form-group">
              <label>Dosage</label>
              <input type="text" name="dosage[]" class="form-control" placeholder="e.g. 500mg">
            </div>
            <div class="form-group">
              <label>Duration</label>
              <input type="text" name="duration[]" class="form-control" placeholder="e.g. 5 days">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Attachments (File URL or Description)</label>
          <input type="text" name="attachments" class="form-control" placeholder="Path to X-Ray or Lab Report">
        </div>

        <div style="text-align: right;">
          <button type="button" class="btn" style="margin-right: 10px; border:none; background:#eee;">Cancel</button>
          <button type="submit" class="btn-primary">Save Encounter Record</button>
        </div>
      </form>
    </div>
  </div>

</body>

</html>