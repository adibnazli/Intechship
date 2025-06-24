<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'AdminHeader.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Martel+Sans:wght@700&display=swap" rel="stylesheet">
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
            font-family: 'Martel Sans', 'Roboto', sans-serif;
            font-size: 2.8rem;
            font-weight: 700;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 30px;
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
            display: flex;
            align-items: center;
            padding: 16px 20px;
        }
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
        }
        .stat-icon img {
            width: 32px;
            height: 32px;
        }
        .stat-icon.blue { background: #09c1fc; }
        .stat-icon.orange { background: #ff9100; }
        .stat-icon.purple { background: #c300ff; }
        .stat-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 6px 0;
        }
        .stat-label {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .dashboard-contentbox {
            background: #f6f6f6;
            margin: 0 auto;
            padding: 40px;
            border-radius: 8px;
            max-width: 1700px;
        }
        .students-status-title {
            font-family: 'Martel Sans', 'Roboto', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 18px;
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
        .badge-review { background: #00b0ff; }
        .badge-offered { background: #a200ff; }
        .badge-completed { background: #00c853; }
        .badge-other { background: #bdbdbd; }
        @media (max-width: 1000px) {
            .dashboard-stats { flex-direction: column; gap: 24px; align-items: center; }
            .dashboard-contentbox { padding: 20px; }
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
                        <div class="stat-number">170</div>
                        <div class="stat-label">Students</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <img src="image/applied.png" alt="Students Applied">
                    </div>
                    <div class="stat-info">
                        <div class="stat-number">90</div>
                        <div class="stat-label">Students Applied</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <img src="image/successfull.png" alt="Successful">
                    </div>
                    <div class="stat-info">
                        <div class="stat-number">30</div>
                        <div class="stat-label">Succesful</div>
                    </div>
                </div>
            </div>
            <div class="students-table-container">
                <div class="students-status-title">Students Status</div>
                <form class="students-status-search" method="get" action="">
                    <input type="text" name="search" placeholder="Name">
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
                        <tr>
                            <td>Aisyah</td>
                            <td>Web Developer</td>
                            <td>TechNova</td>
                            <td>03/04/2024</td>
                            <td><span class="status-badge badge-review">In Review</span></td>
                            <td>No record</td>
                        </tr>
                        <tr>
                            <td>Danial</td>
                            <td>IT Support</td>
                            <td>CloudByte</td>
                            <td>13/04/2024</td>
                            <td><span class="status-badge badge-offered">Offered</span></td>
                            <td><a href="#">Download</a></td>
                        </tr>
                        <tr>
                            <td>Lee</td>
                            <td>UI Design</td>
                            <td>PixelSoft</td>
                            <td>14/04/2024</td>
                            <td><span class="status-badge badge-completed">Completed</span></td>
                            <td><a href="#">Download</a></td>
                        </tr>
                        <tr>
                            <td>Faris</td>
                            <td>Developer</td>
                            <td>Appsmith Co.</td>
                            <td>03/04/2024</td>
                            <td><span class="status-badge badge-offered">Offered</span></td>
                            <td><a href="#">Download</a></td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
                <div style="clear: both;"></div>
            </div>
        </div>
    </div>
</body>
</html>