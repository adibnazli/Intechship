<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    include("config/config.php");
}
include 'AdminHeader.php';

// --- Dashboard Analytics Data ---

// Total Applications
$sql_total_app = "SELECT COUNT(*) AS total FROM student_application";
$result_total_app = mysqli_query($conn, $sql_total_app);
$total_applications = mysqli_fetch_assoc($result_total_app)['total'] ?? 0;

// Completion Rate (Accepted / Total Applications * 100)
$sql_accepted = "SELECT COUNT(*) AS total FROM student_application WHERE App_Status = 'Accepted'";
$result_accepted = mysqli_query($conn, $sql_accepted);
$total_accepted = mysqli_fetch_assoc($result_accepted)['total'] ?? 0;
$completion_rate = $total_applications > 0 ? round(($total_accepted / $total_applications) * 100) : 0;

// Total Users (from student table)
$sql_total_user = "SELECT COUNT(*) AS total FROM student";
$result_total_user = mysqli_query($conn, $sql_total_user);
$total_users = mysqli_fetch_assoc($result_total_user)['total'] ?? 0;

// Applications by Status for Pie Chart
$status_labels = ["In Review", "Interview", "Offered", "Accepted", "Rejected"];
$status_colors = [
    "In Review" => "#FFD600",
    "Interview" => "#FF9100",
    "Offered" => "#B620FF",
    "Accepted" => "#00C853",
    "Rejected" => "#D50000"
];
$status_counts = [];
foreach ($status_labels as $status) {
    $sql = "SELECT COUNT(*) AS total FROM student_application WHERE App_Status = '$status'";
    $result = mysqli_query($conn, $sql);
    $status_counts[] = (int)(mysqli_fetch_assoc($result)['total'] ?? 0);
}

// Top Application Companies
$sql_top_companies = "
    SELECT e.Comp_Name AS company, COUNT(*) AS applications
    FROM student_application sa
    JOIN intern_listings il ON sa.InternshipID = il.InternshipID
    JOIN employer e ON il.EmployerID = e.EmployerID
    GROUP BY e.Comp_Name
    ORDER BY applications DESC
    LIMIT 4";
$result_top_companies = mysqli_query($conn, $sql_top_companies);
$top_companies = [];
while ($row = mysqli_fetch_assoc($result_top_companies)) {
    $top_companies[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>InTechShip Admin | Data Collection</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Righteous&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Roboto', sans-serif; background: #fafafa; margin: 0; }
        .main-content {
            max-width: 1200px; margin: 0 auto; background: #f4f4f4;
        }
        .page-title-row {
            margin: 35px 0 0 0; display: flex; align-items: center;
        }
        .intechship-title {
            font-family: 'Righteous', cursive; font-size: 45px; font-weight: 400; margin-right: 36px;
            letter-spacing: 1px;
        }
        .intechship-title .red { color: #dd1111; }
        .intechship-title .black { color: #222; }
        .intechship-title .space { margin-right: 30px; }
        .analytics-title {
            font-size: 2rem; font-weight: 700; text-align: center; margin: 30px 0 28px 0;
        }
        .analytics-row {
            display: flex; justify-content: center; gap: 48px; margin-bottom: 38px;
        }
        .analytics-card {
            background: #fff; border-radius: 15px; min-width: 240px; min-height: 110px;
            display: flex; flex-direction: column; align-items: center; padding: 24px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .analytics-card .analytics-label {
            font-size: 1.05rem; font-weight: 400; color: #222; margin-bottom: 6px;
        }
        .analytics-card .analytics-value {
            font-size: 2.4rem; font-weight: 700; color: #111;
        }
        .analytics-card .analytics-icon {
            margin-bottom: 8px;
            display: block;
        }
        .analytics-card .analytics-icon img {
            height: 27px; margin-right: 7px; vertical-align: middle;
        }
        .analytics-section-row {
            display: flex; gap: 36px; margin: 0 auto 36px auto; justify-content: center;
        }
        .analytics-section {
            background: #fff; border-radius: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            padding: 25px 32px 30px 32px; min-width: 400px; flex: 1;
        }
        .analytics-section h3 {
            font-weight: 700; font-size: 1.35rem; margin: 0 0 20px 0;
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
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="page-title-row">
            <span class="intechship-title">
                
            </span>
        </div>
        <div class="analytics-title">Analytics</div>
        <div class="analytics-row">
            <div class="analytics-card">
                <span class="analytics-icon"><img src="image/statistics.png" alt="Total Apps"></span>
                <span class="analytics-label">Total Applications</span>
                <span class="analytics-value"><?php echo $total_applications; ?></span>
            </div>
            <div class="analytics-card">
                <span class="analytics-icon"><img src="image/completion.png" alt="Completion Rate"></span>
                <span class="analytics-label">Completion Rate</span>
                <span class="analytics-value"><?php echo $completion_rate; ?>%</span>
            </div>
            <div class="analytics-card">
                <span class="analytics-icon"><img src="image/users.png" alt="Total Users"></span>
                <span class="analytics-label">Total User</span>
                <span class="analytics-value"><?php echo $total_users; ?></span>
            </div>
        </div>
        <div class="analytics-section-row">
            <div class="analytics-section">
                <h3>Applications by Status</h3>
                <canvas id="appStatusPie" width="340" height="160"></canvas>
                <div style="margin-top:14px;">
                <?php foreach ($status_labels as $i => $label): ?>
                    <span style="display:inline-block;width:17px;height:17px;background:<?php echo $status_colors[$label]; ?>;border-radius:100px;margin-right:6px;vertical-align:middle;"></span>
                    <span style="margin-right:22px;font-size:1.08em;"><?php echo $label; ?> (<?php echo $status_counts[$i]; ?>)</span>
                <?php endforeach; ?>
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
        const ctx = document.getElementById('appStatusPie').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($status_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($status_counts); ?>,
                    backgroundColor: <?php echo json_encode(array_values($status_colors)); ?>,
                    borderWidth: 0,
                }]
            },
            options: {
                cutout: '65%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>
<?php if (isset($conn)) mysqli_close($conn); ?>