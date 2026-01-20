<!DOCTYPE html>
<html>
<head><title>Operational Reports</title></head>
<body>
<h2>Operational Reports</h2>
<form method="get">
  DoctorID: <input type="number" name="doctorID">
  DeptID: <input type="number" name="deptID">
  From: <input type="date" name="dateFrom">
  To: <input type="date" name="dateTo">
  <button type="submit">Filter</button>
</form>

<table border="1">
<tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Department</th><th>Slot</th><th>Status</th></tr>
<?php while($r=mysqli_fetch_assoc($res)){ ?>
<tr>
  <td><?= $r['AppointmentID'] ?></td>
  <td><?= $r['Patient'] ?></td>
  <td><?= $r['Doctor'] ?></td>
  <td><?= $r['Department'] ?></td>
  <td><?= $r['Slot'] ?></td>
  <td><?= $r['Status'] ?></td>
</tr>
<?php } ?>
</table>

<a href="exportReport.php?doctorID=<?= $doctorID ?>&deptID=<?= $deptID ?>">Export PDF</a> |
<a href="exportReportExcel.php?doctorID=<?= $doctorID ?>&deptID=<?= $deptID ?>">Export Excel</a>
</body>
</html>
