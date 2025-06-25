<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    include("config/config.php");
}
include 'AdminHeader.php';

// 1. Students: Total students who use the website
$sql_total_students = "SELECT COUNT(*) AS total FROM student";
$result_total_students = mysqli_query($conn, $sql_total_students);
$total_students = mysqli_fetch_assoc($result_total_students)['total'] ?? 0;

// 2. Students Applied: Unique students who applied by themselves (have at least one application with App_Status NOT 'offered')
$sql_applied = "
    SELECT COUNT(DISTINCT sa.StudentID) AS total 
    FROM student_application sa 
    WHERE sa.App_Status != 'offered'
";
$result_applied = mysqli_query($conn, $sql_applied);
$total_applied = mysqli_fetch_assoc($result_applied)['total'] ?? 0;

// 3. Successful: Unique students who have at least one application with App_Status = 'accepted' (and not offered)
$sql_success = "
    SELECT COUNT(DISTINCT sa.StudentID) AS total 
    FROM student_application sa 
    WHERE sa.App_Status = 'accepted'
";
$result_success = mysqli_query($conn, $sql_success);
$total_success = mysqli_fetch_assoc($result_success)['total'] ?? 0;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $where = "WHERE s.Stud_Name LIKE '%$safe_search%'";
}

// Main table query: join all relevant tables
$sql_table = "
SELECT 
    sa.*, 
    s.Stud_Name AS student_name, 
    il.Int_Position AS position_applied, 
    e.Comp_Name AS company,
    sa.App_Date, 
    sa.App_Status
FROM student_application sa
JOIN student s ON sa.StudentID = s.StudentID
JOIN intern_listings il ON sa.InternshipID = il.InternshipID
JOIN employer e ON il.EmployerID = e.EmployerID
$where
ORDER BY sa.App_Date DESC";
$result_table = mysqli_query($conn, $sql_table);
if (!$result_table) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Martel+Sans:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #fff;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }
        .dashboard-container {
            max-width: 1400px;
            margin: 40px auto 0 auto;
            padding: 0 0 40px 0;
        }
        .dashboard-title {
            font-family: 'Roboto', sans-serif;
            font-size: 2.8rem;
            font-weight: 700;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 30px;
            letter-spacing: -1px;
        }
        .dashboard-contentbox {
            background: #f6f6f6;
            margin: 0 auto;
            padding: 40px;
            border-radius: 8px;
            max-width: 1700px;
        }
        .dashboard-stats {
            display: flex;
            justify-content: center;
            gap: 48px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            min-width: 270px;
            min-height: 110px;
            max-width: 330px;
            width: 100%;
            height: 120px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            position: relative;
            box-sizing: border-box;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 22px;
            flex-shrink: 0;
        }
        .stat-icon.blue { background: #04b7ed; }
        .stat-icon.orange { background: #ff8800; }
        .stat-icon.purple { background: #b620ff; }
        .stat-icon img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }
        .stat-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 4px 0;
        }
        .stat-label {
            font-size: 1.08rem;
            font-weight: 600;
            margin-top: 0;
        }
        .students-table-container {
            background: #fff;
            border-radius: 12px;
            padding: 24px 20px;
            margin: 0 auto;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            max-width: 850px;
            overflow-x: auto;
        }
        .students-status-title {
            font-family: 'Martel Sans', 'Roboto', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 18px;
        }
        .students-status-search {
            float: right;
            margin-bottom: 16px;
        }
        .students-status-search input[type="text"] {
            padding: 6px 30px 6px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .students-status-search .search-icon {
            margin-left: -24px;
            color: #999;
        }
        table.students-status {
            width: 100%;
            border-collapse: collapse;
        }
        table.students-status th, table.students-status td {
            padding: 10px 8px;
            text-align: left;
            font-size: 1rem;
        }
        table.students-status th {
            background: #fff;
            font-weight: bold;
        }
        table.students-status tr:not(:first-child):hover {
            background: #f1f1f1;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            color: #fff;
            font-size: 0.95em;
            font-weight: 600;
            display: inline-block;
        }
        .badge-pending { background: #FFD600; color: #333; }
        .badge-accepted { background: #00c853; }
        .badge-rejected { background: #d50000; }
        .badge-interview { background: #2979ff; }
        .badge-other { background: #bdbdbd; }
        @media (max-width: 1000px) {
            .dashboard-stats { flex-direction: column; gap: 24px; align-items: center; }
            .dashboard-contentbox { padding: 20px; }
            .stat-card { max-width: 100%; min-width: 220px; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-title">
            Dashboard
        </div>
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
                        <div class="stat-label">Successful</div>
                    </div>
                </div>
            </div>
            <div class="students-table-container">
                <div class="students-status-title">Students Status</div>
                <form class="students-status-search" method="get" action="">
                    <input type="text" name="search" placeholder="Name" value="<?php echo htmlspecialchars($search); ?>">
                    <span class="search-icon">&#128269;</span>
                </form>
                <table class="students-status">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Position Applied</th>
                            <th>Company</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Letter</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_table && mysqli_num_rows($result_table) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result_table)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['position_applied']); ?></td>
                                    <td><?php echo htmlspecialchars($row['company']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['App_Date'])); ?></td>
                                    <td>
                                        <?php
                                            $status = strtolower($row['App_Status']);
                                            switch ($status) {
                                                case 'pending':    $badge_class = 'badge-pending'; break;
                                                case 'accepted':   $badge_class = 'badge-accepted'; break;
                                                case 'rejected':   $badge_class = 'badge-rejected'; break;
                                                case 'interview':  $badge_class = 'badge-interview'; break;
                                                default:           $badge_class = 'badge-other'; break;
                                            }
                                        ?>
                                        <span class="status-badge <?php echo $badge_class; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        No record
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">No students found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div style="clear: both;"></div>
            </div>
        </div>
    </div>
</body>
</html>
<?php if (isset($conn)) mysqli_close($conn); ?>