<!DOCTYPE html>
<html>

<head>
    <title>Operational Reports - Meditrust</title>
    <link rel="stylesheet" href="../assets/style_layoutUser16_18.css">
</head>

<body>

    <?php include 'layoutAdmin16_18.php'; ?>

    <div class="main-content">
        <div class="dashboard-header">
            <h2>Operational Reports</h2>
            <p>Filter and view appointment analytics and status</p>
        </div>

        <div class="form-container">
            <form method="get"
                style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
                <div class="form-group" style="margin:0;">
                    <label>Doctor ID</label>
                    <input type="number" name="doctorID" class="form-control"
                        value="<?= htmlspecialchars($_GET['doctorID'] ?? '') ?>" placeholder="Any">
                </div>
                <div class="form-group" style="margin:0;">
                    <label>Dept ID</label>
                    <input type="number" name="deptID" class="form-control"
                        value="<?= htmlspecialchars($_GET['deptID'] ?? '') ?>" placeholder="Any">
                </div>
                <div class="form-group" style="margin:0;">
                    <label>From Date</label>
                    <input type="date" name="dateFrom" class="form-control"
                        value="<?= htmlspecialchars($_GET['dateFrom'] ?? '') ?>">
                </div>
                <div class="form-group" style="margin:0;">
                    <label>To Date</label>
                    <input type="date" name="dateTo" class="form-control"
                        value="<?= htmlspecialchars($_GET['dateTo'] ?? '') ?>">
                </div>
                <button type="submit" class="btn-primary" style="height: 42px;">Filter Results</button>
            </form>
        </div>

        <div class="card" style="width:100%; box-sizing:border-box; background:#fff;">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Department</th>
                        <th>Slot</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($res && mysqli_num_rows($res) > 0): ?>
                        <?php while ($r = mysqli_fetch_assoc($res)) { ?>
                            <tr>
                                <td><?= $r['AppointmentID'] ?></td>
                                <td><?= $r['Patient'] ?></td>
                                <td><?= $r['Doctor'] ?></td>
                                <td><?= $r['Department'] ?></td>
                                <td><?= $r['Slot'] ?></td>
                                <td>
                                    <span
                                        style="padding: 4px 8px; border-radius: 4px; background: #eafaf1; color: #386D44; font-weight: 500;">
                                        <?= $r['Status'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #666; padding: 20px;">No records found
                                matching criteria.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div style="margin-top: 20px; text-align: right;">
                <button class="btn-primary" onclick="window.print()">🖨️ Print Report</button>
            </div>
        </div>
    </div>

</body>

</html>