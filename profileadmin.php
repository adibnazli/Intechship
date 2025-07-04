<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include('config/connect.php');
$program_desc = '';
if (!empty($_SESSION['Program_Desc'])) {
    $program_desc = trim($_SESSION['Program_Desc']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PIC | Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
body { background: #f4f4f4; margin: 0; font-family: 'Roboto', sans-serif; }
.dashboard-bg { background: #f4f4f4; min-height: 100vh; padding-top: 40px; }
.dashboard-subtitle { font-family: 'Roboto', sans-serif; font-weight: 700; font-size: 2rem; text-align: center; margin-top: 16px; margin-bottom: 30px; }
.dashboard-contentbox { background:rgb(236, 236, 236); border-radius: 0; padding: 60px 0 60px 0; max-width: 1450px; margin: 0 auto; }

/* --- Stats Card Row --- */
.stats-row {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 32px;
    margin-bottom: 56px;
    background: #f7f7f7;
    border-radius: 18px;
    padding: 32px 0 18px 0;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
}
.stats-card {
    background: #fff;
    border-radius: 16px;
    width: 220px;
    height: 110px;
    display: flex;
    flex-direction: row;
    align-items: center;
    border: 1.5px solid #e0e0e0;
    box-shadow: none;
    margin: 0;
    position: relative;
    padding: 0;
    justify-content: flex-start;
}
.stats-card:not(:last-child)::after { display: none; }
.stats-card .icon-img-bg {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    border-radius: 14px;
    margin-left: 18px;
    margin-right: 18px;
}
.stats-card .icon-img {
    width: 32px;
    height: 32px;
    object-fit: contain;
}
.stats-card .stats-text {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    height: 100%;
}
.stats-card .value {
    font-size: 2.1rem;
    font-weight: 900;
    margin-bottom: 0;
    margin-top: 2px;
    color: #111;
    line-height: 1.1;
}
.stats-card .desc {
    font-size: 1.08rem;
    color: #222;
    font-weight: 600;
    margin-bottom: 2px;
    margin-top: 2px;
}
@media (max-width: 1000px) {
    .stats-row { flex-direction: column; gap: 18px; padding: 18px 0; }
    .stats-card { width: 95vw; max-width: 350px; margin: 0 auto; }
    .stats-card:not(:last-child)::after { display: none; }
}
.center-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    max-width: 800px; /* increased from 600px */
    margin: 38px auto 0 auto;
    padding: 32px 32px 32px 32px;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.student-table-header {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 9px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}
.student-table-header form {
    margin: 0;
}
.student-table-header input[type="text"] {
    padding: 7px 12px;
    border-radius: 20px;
    border: 1.3px solid #e0e0e0;
    font-size: 1rem;
    outline: none;
    margin-right: 4px;
    background: #fafbff;
}
.student-table-header button {
    padding: 7px 12px;
    border-radius: 4px;
    border: 1.3px solid #bbb;
    background: #f2f2f2;
    font-weight: 500;
    cursor: pointer;
}
.student-table-header button:hover {
    background: #f6e7b0;
}
.table-responsive {
    width: 100%;
    overflow-x: auto;
    display: flex;
    justify-content: center;
}
table {
    width: 98%;
    max-width: 700px; /* increased from 500px */
    border-collapse: collapse;
    margin-top: 8px;
    margin-left: auto;
    margin-right: auto;
}
th, td {
    padding: 8px 10px;
    border-bottom: 1px solid #eee;
    text-align: left;
}
th {
    background: #fafafa;
    font-weight: 700;
}
tr:last-child td { border-bottom: none; }
tr:nth-child(even) td { background: #f9f9f9;}
.student-name {
    color: #2517e9;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
}
.student-name:hover {
    color: #FFB300;
}
.app-row-details {
    background: #fcfcfc;
}
.status-badge {
    display: inline-block;
    padding: 2px 12px;
    border-radius: 8px;
    font-size: 0.97rem;
    font-weight: 700;
    color: #333;
    margin-right: 2px;
}
.status-inreview { background: #ffe96b; }
.status-offered { background: #eb71ff; color:#222; }
.status-rejected { background: #ffb2b2; }
.status-accepted { background: #c6f98e; }
.status-pending { background: #FFD600; color: #222; } /* dark yellow */
.status-declined { background: #8e24aa; color: #fff; } /* purple */
@media (max-width: 900px) {
    .center-card { padding: 18px 5vw; }
    .stats-row { flex-direction: column; gap: 16px; }
    .stats-card:not(:last-child)::after {
        display: none;
    }
}
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<?php include 'AdminHeader.php'; ?>

<div class="dashboard-subtitle">Dashboard</div>
<div class="stats-row">
    <div class="stats-card">
        <span class="icon-img-bg" style="background:#00CFFF;"><img src="image/student.png" alt="Students Icon" class="icon-img"></span>
        <div class="stats-text">
            <div class="value" id="students-count">-</div>
            <div class="desc">Students</div>
        </div>
    </div>
    <div class="stats-card">
        <span class="icon-img-bg" style="background:#FF9800;"><img src="image/applied.png" alt="Applied Icon" class="icon-img"></span>
        <div class="stats-text">
            <div class="value" id="applied-count">-</div>
            <div class="desc">Students Applied</div>
        </div>
    </div>
    <div class="stats-card">
        <span class="icon-img-bg" style="background:#D500F9;"><img src="image/successfull.png" alt="Success Icon" class="icon-img"></span>
        <div class="stats-text">
            <div class="value" id="success-count">-</div>
            <div class="desc">Successful</div>
        </div>
    </div>
</div>

<!-- Students Table Card -->
<div class="center-card">
    <div class="student-table-header">
        <span>Students Status</span>
        <form id="student-search-form" style="display: flex; align-items: center;">
            <input type="text" name="search" id="search" placeholder="Search Name">
            <button type="submit"><span style="font-size:1.2em;">&#128269;</span></button>
        </form>
    </div>
    <div class="table-responsive">
        <table id="students-table">
            <thead>
                <tr>
                    <th style="width:48px;">No.</th>
                    <th>Name</th>
                    <th>Programme</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="3" style="text-align:center;">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>
<script>
function loadStats() {
    $.get('profileadmin_stats.php', function(data) {
        if (data && data.success) {
            $('#students-count').text(data.student_total);
            $('#applied-count').text(data.applied_total);
            // Show actual successful count, not percentage or dash
            $('#success-count').text(data.success_total !== undefined ? data.success_total : '-');
        } else {
            $('#students-count').text('-');
            $('#applied-count').text('-');
            $('#success-count').text('-');
        }
    }, 'json');
}
function loadTable(search='') {
    $.get('studentapplytableajax.php', {search: search}, function(data) {
        $('#students-table tbody').html(data);
    });
}
$(document).ready(function() {
    loadStats();
    loadTable();
    $('#student-search-form').on('submit', function(e) {
        e.preventDefault();
        loadTable($('#search').val());
    });

    // Delegate click handler for student name (dynamic rows)
    $('#students-table').on('click', '.student-name', function() {
        var tr = $(this).closest('tr');
        var sid = $(this).data('studentid');
        // Remove old detail rows if exist
        $('#students-table .app-row-details').remove();
        // If already expanded, collapse
        if(tr.hasClass('opened')) {
            tr.removeClass('opened');
            return;
        }
        // Remove old opened
        $('#students-table tr').removeClass('opened');
        tr.addClass('opened');
        // Insert new row after
        $('<tr class="app-row-details"><td colspan="3" style="background:#f9f9ff;"><div class="app-info-loading" style="text-align:center;">Loading application info...</div></td></tr>')
        .insertAfter(tr);
        $.get('student_application_info.php', {student_id: sid}, function(html) {
            tr.next('.app-row-details').find('td').html(html);
        });
    });
});
</script>
<?php if (isset($conn)) mysqli_close($conn); ?>
</body>
</html>