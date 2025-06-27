<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . "/config/config.php");
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
body {
    background: #f4f4f4;
    margin: 0;
    font-family: 'Roboto', Arial, sans-serif;
}
.dashboard-title {
    text-align: center;
    font-size: 2.3rem;
    font-weight: 700;
    margin-top: 32px;
    margin-bottom: 12px;
    letter-spacing: -1.5px;
}
.stats-row {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 30px;
    margin-top: 10px;
    position: relative;
}
.stats-card {
    background: #fff;
    border-radius: 16px;
    min-width: 200px;
    padding: 20px 26px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    display: flex;
    flex-direction: column;
    align-items: center;
    font-weight: 700;
    position: relative;
    border: 2px solid #e0e0e0;
    /* z-index to show above divider */
    z-index: 1;
}
/* Add vertical dividers between the 3 cards */
.stats-card:not(:last-child)::after {
    content: "";
    position: absolute;
    right: -15px;
    top: 30%;
    height: 40%;
    width: 3px;
    background: #e0e0e0;
    border-radius: 2px;
    z-index: 2;
}
.stats-card .icon-img {
    width: 48px;
    height: 48px;
    object-fit: contain;
    margin-bottom: 7px;
}
.stats-card .value { font-size: 2.2rem; font-weight: 900; }
.stats-card .desc { font-size: 1.1rem; color: #222; font-weight: 600;}
.center-card {
    margin: 0 auto;
    margin-bottom: 60px;
    max-width: 700px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    padding: 32px 36px 26px 36px;
}
.student-table-header {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 9px;
    display: flex;
    align-items: center;
    justify-content: space-between;
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
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
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
    text-decoration: underline;
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

<div class="dashboard-title">Dashboard</div>

<!-- Stats Row -->
<div class="stats-row">
    <div class="stats-card students" style="flex-direction:row;align-items:center;min-width:260px;">
        <img src="image/student.png" alt="Students Icon" style="width:48px;height:48px;margin-right:18px;">
        <div style="display:flex;flex-direction:column;align-items:flex-start;">
            <div class="value" id="students-count">-</div>
            <div class="desc">Students</div>
        </div>
    </div>
    <div class="stats-card applied" style="flex-direction:row;align-items:center;min-width:260px;">
        <img src="image/applied.png" alt="Applied Icon" style="width:48px;height:48px;margin-right:18px;">
        <div style="display:flex;flex-direction:column;align-items:flex-start;">
            <div class="value" id="applied-count">-</div>
            <div class="desc">Students Applied</div>
        </div>
    </div>
    <div class="stats-card success" style="flex-direction:row;align-items:center;min-width:260px;">
        <img src="image/successfull.png" alt="Success Icon" style="width:48px;height:48px;margin-right:18px;">
        <div style="display:flex;flex-direction:column;align-items:flex-start;">
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
            $('#success-count').text(data.success_total);
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