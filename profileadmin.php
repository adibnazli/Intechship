<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . "/config/config.php");

// Dashboard statistics
$sql_total_students = "SELECT COUNT(*) AS total FROM student";
$result_total_students = mysqli_query($conn, $sql_total_students);
$total_students = mysqli_fetch_assoc($result_total_students)['total'] ?? 0;

$sql_applied = "SELECT COUNT(DISTINCT sa.StudentID) AS total FROM student_application sa WHERE sa.App_Status != 'offered'";
$result_applied = mysqli_query($conn, $sql_applied);
$total_applied = mysqli_fetch_assoc($result_applied)['total'] ?? 0;

$sql_success = "SELECT COUNT(DISTINCT sa.StudentID) AS total FROM student_application sa WHERE sa.App_Status = 'accepted'";
$result_success = mysqli_query($conn, $sql_success);
$total_success = mysqli_fetch_assoc($result_success)['total'] ?? 0;

// Students list
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $where = "WHERE s.Stud_Name LIKE '%$safe_search%'";
}
$sql_students = "SELECT DISTINCT s.StudentID, s.Stud_Name FROM student s JOIN student_application sa ON sa.StudentID = s.StudentID $where ORDER BY s.Stud_Name";
$result_students = mysqli_query($conn, $sql_students);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin | Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="employerheader.css">
<style>
body { background: #f4f4f4; margin: 0; font-family: 'Roboto', sans-serif; }
.dashboard-bg { background: #f4f4f4; min-height: 100vh; padding-top: 40px; }
.dashboard-subtitle { font-family: 'Roboto', sans-serif; font-weight: 700; font-size: 2rem; text-align: center; margin-top: 16px; margin-bottom: 30px; }
.dashboard-contentbox { background: #f4f4f4; border-radius: 0; padding: 60px 0 60px 0; max-width: 1450px; margin: 0 auto; }
.dashboard-stats { display: flex; justify-content: center; gap: 40px; margin-bottom: 56px; }
.stat-card {
    background: #fff;
    border-radius: 18px;
    min-width: 260px;
    max-width: 330px;
    width: 330px;
    height: 120px;
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 0 28px;
    box-shadow: 0 3px 14px rgba(0,0,0,0.06);
    position: relative;
}
.stat-icon {
    width: 60px; height: 60px; border-radius: 13px;
    display: flex; align-items: center; justify-content: center;
    margin-right: 28px;
    flex-shrink: 0;
}
.stat-icon img { width: 48px; height: 48px; object-fit: contain; }
.stat-icon.blue { background: #04b7ed; }
.stat-icon.orange { background: #ff9800; }
.stat-icon.purple { background: #b620ff; }
.stat-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
}
.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 2px;
    color: #111;
    line-height: 1;
}
.stat-label {
    font-size: 1.09rem;
    font-weight: 500;
    color: #222;
    margin-top: 1px;
    line-height: 1.3;
}
.students-table-outer {
    display: flex;
    justify-content: center;
}
.students-table-container {
    background: #fff;
    border-radius: 15px;
    padding: 22px 25px 30px 25px;
    margin: 0 auto;
    box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    max-width: 600px;
    width: 100%;
    position: relative;
}
.students-status-title { font-family: 'Roboto', sans-serif; font-size: 1.47rem; font-weight: 700; margin-bottom: 12px; }
.students-status-search { position: absolute; top: 26px; right: 34px; display: flex; align-items: center; gap: 0; }
.students-status-search input[type="text"] { border-radius: 22px; border: 1.5px solid #e0e0e0; padding: 7px 34px 7px 14px; font-size: 1.02rem; outline: none; width: 136px; background: #fafafa; }
.students-status-search .search-icon { margin-left: -27px; color: #aaa; font-size: 1.2rem; }
table.students-status { width: 100%; border-collapse: collapse; margin-top: 28px; }
table.students-status th, table.students-status td { padding: 7px 6px; font-size: 1rem; vertical-align: top; }
.student-name-link {
    color: #2461d4;
    text-decoration: none;
    cursor: pointer;
    font-size: 1.22rem;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.student-name-link td {
    white-space: nowrap;
}

.student-name-link:hover { text-decoration: underline; }
.applications-table { width: 99%; border-collapse: collapse; margin: 12px 0 0 0; }
.applications-table th, .applications-table td { padding: 6px 11px; font-size: 1rem; white-space: nowrap; }
.status-badge {
    border-radius: 9px;
    font-size: 0.92em;
    font-weight: 700;
    padding: 3px 14px;
    display: inline-block;
    margin-right: 2px;
}
.badge-pending { background: #ffe066; color: #333; }
.badge-accepted { background: #00c853; color: #fff; }
.badge-rejected { background: #d50000; color: #fff; }
.badge-interview { background: #2979ff; color: #fff; }
.badge-other { background: #bdbdbd; color: #fff; }
.badge-offered { background: #b620ff; color: #fff; }
.badge-inreview { background: #ffe066; color: #333; }
.badge-declined { background: #bdbdbd; color: #fff; }
.badge-new { background: #ffe066; color: #333; }
.badge-special { background: #e600ff; color: #fff; }
.student-apps-row td {
    background: #fafafa;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
    padding: 20px 12px 22px 36px;
    border-top: 1px solid #ececec;
    animation: fadeIn 0.3s;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px);}
    to { opacity: 1; transform: translateY(0);}
}
@media (max-width:1100px){
    .dashboard-stats{flex-direction:column;gap:24px;align-items:center;}
    .dashboard-contentbox{padding:20px;}
    .stat-card{max-width:100%;min-width:220px;width:100%;height:auto;}
    .students-table-container{max-width:100%;}
    .students-table-outer{padding:0;}
}
</style>
</head>
<body>
<?php include(__DIR__ . "/AdminHeader.php"); ?>
<div class="dashboard-bg">
<h1 class="dashboard-subtitle">Dashboard</h1>
<div class="dashboard-contentbox">
  <div class="dashboard-stats">
    <div class="stat-card">
      <div class="stat-icon blue">
        <img src="image/student.png" alt="Students">
      </div>
      <div class="stat-info">
        <div class="stat-number"><?php echo $total_students; ?></div>
        <div class="stat-label">Students</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange">
        <img src="image/applied.png" alt="Students Applied">
      </div>
      <div class="stat-info">
        <div class="stat-number"><?php echo $total_applied; ?></div>
        <div class="stat-label">Students Applied</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon purple">
        <img src="image/successfull.png" alt="Successful">
      </div>
      <div class="stat-info">
        <div class="stat-number"><?php echo $total_success; ?></div>
        <div class="stat-label">Succesful</div>
      </div>
    </div>
  </div>
  <div class="students-table-outer">
    <div class="students-table-container">
      <div class="students-status-title">Students Status</div>
      <form class="students-status-search" method="get" action="">
        <input type="text" name="search" placeholder="Name" value="<?php echo htmlspecialchars($search); ?>">
        <span class="search-icon">&#128269;</span>
      </form>
      <table class="students-status">
        <thead>
          <tr><th>Name</th><th style="width:70%"></th></tr>
        </thead>
        <tbody>
        <?php if ($result_students && mysqli_num_rows($result_students) > 0): ?>
          <?php while($student = mysqli_fetch_assoc($result_students)): ?>
            <tr class="student-row" data-expanded="false">
              <td>
                <a class="student-name-link" href="javascript:void(0);" onclick="event.stopPropagation(); toggleApplications(<?php echo $student['StudentID']; ?>, this.parentNode.parentNode)">
                  <?php echo htmlspecialchars($student['Stud_Name']); ?>
                </a>
              </td>
              <td></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="2" style="text-align:center;">No students found.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
      <div style="clear:both;"></div>
    </div>
  </div>
</div>
</div>
<script>
function toggleApplications(studentId, rowElem) {
    document.querySelectorAll('.student-apps-row').forEach(r => r.remove());
    if (rowElem.getAttribute('data-expanded') === 'true') {
        rowElem.setAttribute('data-expanded', 'false');
        return;
    }
    document.querySelectorAll('.student-row').forEach(r => r.setAttribute('data-expanded', 'false'));
    rowElem.setAttribute('data-expanded', 'true');

    fetch('studentapplytableajax.php?fetch_student_id=' + studentId)
    .then(response => response.text())
    .then(html => {
        var tr = rowElem;
        var newRow = document.createElement('tr');
        newRow.className = 'student-apps-row';
        var newCell = document.createElement('td');
        newCell.colSpan = tr.children.length;
        newCell.innerHTML = html;
        newRow.appendChild(newCell);
        tr.parentNode.insertBefore(newRow, tr.nextSibling);
    });
}
</script>
<?php if (isset($conn)) mysqli_close($conn); ?>
</body>
</html>