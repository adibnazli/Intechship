<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    include("config/config.php");
}
include 'AdminHeader.php';

// --- Dashboard Analytics Data ---

// Program filter
$program_desc = '';
if (!empty($_SESSION['Program_Desc'])) {
    $program_desc = trim($_SESSION['Program_Desc']);
}
$likeValue = $program_desc . '%';
$student_filter_sql = ($program_desc !== '') ? " WHERE Stud_Programme LIKE '" . mysqli_real_escape_string($conn, $likeValue) . "'" : '';

// Total Users (from student table)
$sql_total_user = "SELECT COUNT(*) AS total FROM student" . $student_filter_sql;
$result_total_user = mysqli_query($conn, $sql_total_user);
$total_users = mysqli_fetch_assoc($result_total_user)['total'] ?? 0;

// Total Applications (filtered)
$sql_total_app = "SELECT COUNT(*) AS total FROM student_application sa JOIN student s ON sa.StudentID = s.StudentID" . $student_filter_sql;
$result_total_app = mysqli_query($conn, $sql_total_app);
$total_applications = mysqli_fetch_assoc($result_total_app)['total'] ?? 0;

// Get all students (filtered)
$students = [];
$res_students = mysqli_query($conn, "SELECT StudentID FROM student" . $student_filter_sql);
if ($res_students) {
    while ($row = mysqli_fetch_assoc($res_students)) {
        $students[] = $row['StudentID'];
    }
}

// Completion Rate (Accepted / Total Students * 100, filtered)
$students_with_accepted = 0;
if (!empty($students)) {
    foreach ($students as $sid) {
        $statuses = [];
        $res = mysqli_query($conn, "SELECT App_Status FROM student_application WHERE StudentID = $sid");
        while ($row = $res ? mysqli_fetch_assoc($res) : false) {
            $statuses[] = $row['App_Status'];
        }
        $statuses = array_unique($statuses);
        if (in_array('Accepted', $statuses)) {
            $students_with_accepted++;
        }
    }
}
$completion_rate = count($students) > 0 ? round(($students_with_accepted / count($students)) * 100) : 0;

// Top Application Companies (filtered)
$sql_top_companies = "
    SELECT e.Comp_Name AS company, COUNT(*) AS applications
    FROM student_application sa
    JOIN intern_listings il ON sa.InternshipID = il.InternshipID
    JOIN employer e ON il.EmployerID = e.EmployerID
    JOIN student s ON sa.StudentID = s.StudentID
    " . $student_filter_sql . "
    GROUP BY e.Comp_Name
    ORDER BY applications DESC
    LIMIT 4";
$result_top_companies = mysqli_query($conn, $sql_top_companies);
$top_companies = [];
while ($row = mysqli_fetch_assoc($result_top_companies)) {
    $top_companies[] = $row;
}

// Pie Chart: Each student only in one status (Pending, In Progress, Completed)
$pie_status_labels = [
    'Pending',
    'In Progress',
    'Completed'
];
$pie_status_counts = [
    'Pending' => 0,
    'In Progress' => 0,
    'Completed' => 0
];
$pie_status_colors = [
    'Pending' => '#FFD600',
    'In Progress' => '#2979ff',
    'Completed' => '#00C853'
];

foreach ($students as $sid) {
    $statuses = [];
    $res = mysqli_query($conn, "SELECT App_Status FROM student_application WHERE StudentID = $sid");
    while ($row = mysqli_fetch_assoc($res)) {
        $statuses[] = $row['App_Status'];
    }
    $statuses = array_unique($statuses);
    if (in_array('Accepted', $statuses)) {
        // If any application is Accepted, student is Completed
        $pie_status_counts['Completed']++;
    } elseif (in_array('Pending', $statuses)) {
        // If no Accepted, but has Pending, student is Pending
        $pie_status_counts['Pending']++;
    } elseif (in_array('Declined', $statuses)) {
        // If Declined but also has Accepted, student is Completed (already handled above)
        // If Declined but no Accepted, student is In Progress
        if (in_array('Accepted', $statuses)) {
            // Already handled above, skip
            continue;
        } else {
            $pie_status_counts['In Progress']++;
        }
    } elseif (array_intersect(['In Review', 'Interview', 'Offered', 'Rejected'], $statuses)) {
        // If has any In Progress status, student is In Progress
        $pie_status_counts['In Progress']++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PIC | Data Collection</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Righteous&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Roboto', sans-serif; background: #fafafa; margin: 0; }
        .main-content {
            max-width: 1200px; margin: 0 auto; background: #f4f4f4;
        }
        .analytics-title {
            font-size: 2rem; font-weight: 700; text-align: center; margin: 45px 0 28px 0;
        }
        .analytics-row {
            display: flex; justify-content: center; gap: 48px; margin-bottom: 38px;
        }
        .analytics-card {
            background: #fff; border-radius: 10px; width: 310px; min-height: 150px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .analytics-top-row {
            width: 100%;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 6px;
            justify-content: center;
        }
        .analytics-card .analytics-icon {
            flex-shrink: 0;
            margin-top: 7px;
        }
        .analytics-card .analytics-icon img {
            height: 32px; width: 32px; object-fit: contain; display: block;
        }
        .analytics-info {
            display: flex; flex-direction: column; justify-content: center;
            align-items: flex-start;
        }
        .analytics-info .analytics-label {
            font-size: 1.24rem; font-weight: 400; color: #111; margin-top: 0; margin-bottom: 0;
            font-family: 'Roboto', sans-serif;
        }
        .analytics-value {
            font-size: 2.8rem; font-weight: 700; color: #111; text-align: center; width: 100%;
            font-family: 'Roboto', sans-serif;
        }
        .analytics-value .percent {
            font-size: 1.7rem; vertical-align: super; font-weight: 700;
        }
        .analytics-section-row {
            display: flex; gap: 36px; margin: 0 auto 36px auto; justify-content: center;
        }
        .analytics-section {
            background: #fff; border-radius: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            padding: 25px 32px 30px 32px; min-width: 480px; flex: 1;
            display: flex;
            flex-direction: column;
        }
        .analytics-status-flex {
            display: flex;
            align-items: flex-start;
            gap: 36px;
        }
        .analytics-section .analytics-status-title {
            font-family: 'Roboto', sans-serif;
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 18px;
            margin-top: 0;
            width: 100%;
            text-align: left;
            letter-spacing: -0.5px;
        }
        .analytics-section .pie-legend-list {
            margin-top: 0;
            padding-left: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            height: 240px; /* smaller to allow larger chart */
            justify-content: space-between;
        }
        .pie-legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }
        .pie-legend-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            margin-right: 14px;
            flex-shrink: 0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05); 
        }
        .pie-legend-label {
            font-size: 1.13rem;
            color: #222;
            font-family: 'Roboto', sans-serif;
        }
        .analytics-section .pie-chart-wrapper {
            flex: 1;
            min-width: 290px; /* larger for bigger pie */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }
        .top-company-table {
            width: 100%; border-collapse: collapse; background: #fff;
        }
        .top-company-table th, .top-company-table td {
            padding: 10px 10px; text-align: left; font-size: 1.05rem;
        }
        .top-company-table th { font-weight: bold; }
        .top-company-table tr:not(:first-child):hover { background: #f6f6f6; }
        @media (max-width: 1200px) {
            .analytics-section-row { flex-direction: column; gap: 24px; }
            .main-content { max-width: 98vw;}
            .analytics-row { flex-direction: column; gap: 24px; align-items: center; }
            .analytics-card { min-width: 220px; width: 90vw;}
            .analytics-section { min-width: 90vw;}
        }
        @media (max-width: 900px) {
            .analytics-status-flex { flex-direction: column; align-items: flex-start; gap: 0; }
            .pie-chart-wrapper { margin-bottom: 20px; }
            .analytics-section .pie-legend-list { height: auto; }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="analytics-title">Analytics</div>
        <div class="analytics-row">
            <div class="analytics-card">
                <div class="analytics-top-row">
                    <span class="analytics-icon"><img src="image/totapps.png" alt="Total Applications"></span>
                    <div class="analytics-info">
                        <span class="analytics-label">Total Applications</span>
                    </div>
                </div>
                <div class="analytics-value"><?php echo $total_applications; ?></div>
            </div>
            <div class="analytics-card">
                <div class="analytics-top-row">
                    <span class="analytics-icon"><img src="image/rate.png" alt="Completion Rate"></span>
                    <div class="analytics-info">
                        <span class="analytics-label">Completion Rate</span>
                    </div>
                </div>
                <div class="analytics-value">
                    <?php echo $completion_rate; ?><span class="percent">%</span>
                </div>
            </div>
            <div class="analytics-card">
                <div class="analytics-top-row">
                    <span class="analytics-icon"><img src="image/totuser.png" alt="Total Users"></span>
                    <div class="analytics-info">
                        <span class="analytics-label">Total User</span>
                    </div>
                </div>
                <div class="analytics-value"><?php echo $total_users; ?></div>
            </div>
        </div>
        <div class="analytics-section-row">
            <div class="analytics-section">
                <div class="analytics-status-title">Applications by Status</div>
                <div class="analytics-status-flex">
                    <div class="pie-chart-wrapper">
                        <canvas id="appStatusPie" width="340" height="340"></canvas>
                    </div>
                    <ul class="pie-legend-list">
                        <?php foreach ($pie_status_labels as $i => $label): ?>
                            <li class="pie-legend-item">
                                <span class="pie-legend-dot" style="background:<?php echo $pie_status_colors[$label]; ?>;"></span>
                                <span class="pie-legend-label"><?php echo $label; ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="analytics-section">
                <h3>Top Application Company</h3>
                <table class="top-company-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Company</th>
                            <th>Applications</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_companies as $i => $company): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo htmlspecialchars($company['company']); ?></td>
                                <td><?php echo $company['applications']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php for ($j = count($top_companies); $j < 4; $j++): ?>
                            <tr>
                                <td><?php echo $j + 1; ?></td>
                                <td>-</td>
                                <td>0</td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Custom percent labels in the middle of each segment
        Chart.register({
            id: 'showPercentLabels',
            afterDraw: function(chart) {
                if (chart.config.type !== 'doughnut') return;
                const ctx = chart.ctx;
                const dataset = chart.data.datasets[0];
                const total = dataset.data.reduce((a, b) => a + b, 0);
                const meta = chart.getDatasetMeta(0);
                ctx.save();
                meta.data.forEach(function(element, i) {
                    const percent = total ? Math.round(dataset.data[i] / total * 100) : 0;
                    if (percent > 0) {
                        const model = element;
                        const midAngle = (model.startAngle + model.endAngle) / 2;
                        const radius = (model.outerRadius + model.innerRadius) / 2;
                        const x = chart.width/2 + Math.cos(midAngle) * radius;
                        const y = chart.height/2 + Math.sin(midAngle) * radius;
                        ctx.fillStyle = "#222";
                        ctx.font = "bold 1.14em Roboto";
                        ctx.textAlign = "center";
                        ctx.textBaseline = "middle";
                        ctx.fillText(percent + "%", x, y);
                    }
                });
                ctx.restore();
            }
        });

        const ctx = document.getElementById('appStatusPie').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($pie_status_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($pie_status_counts)); ?>,
                    backgroundColor: <?php echo json_encode(array_values($pie_status_colors)); ?>,
                    borderWidth: 0,
                }]
            },
            options: {
                cutout: '60%',
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>
</html>
<?php if (isset($conn)) mysqli_close($conn); ?>