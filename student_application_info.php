<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . "/config/config.php");

$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
if ($student_id <= 0) {
    echo '<div style="color:red;">Invalid student ID.</div>';
    exit;
}

// Get student info
$stmt = $conn->prepare("SELECT Stud_Name, Stud_Programme FROM student WHERE StudentID = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($stud_name, $stud_programme);
if (!$stmt->fetch()) {
    echo '<div style="color:red;">Student not found.</div>';
    $stmt->close();
    exit;
}
$stmt->close();

// Custom CSS for table style (to match the dashboard look)
?>
<style>
.student-app-table-container {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.06);
    padding: 32px 28px 20px 28px;
    max-width: 600px;
    margin: 32px auto;
}
.student-app-title {
    font-size: 1.5em;
    font-weight: 700;
    letter-spacing: -.5px;
    margin-bottom: 16px;
}
.student-app-name {
    color: #2176FF;
    font-weight: 600;
    font-size: 1.08em;
    margin-bottom: 12px;
    display: block;
    text-decoration: none;
}
.student-app-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
    font-size: 1em;
    margin-top: 8px;
}
.student-app-table th {
    background: none;
    font-weight: 600;
    color: #222;
    text-align: left;
    padding: 0 0 12px 0;
    border-bottom: 2px solid #F0F0F0;
}
.student-app-table td {
    background: #F7F7F7;
    border-radius: 6px;
    padding: 10px 12px;
    vertical-align: middle;
    font-size: 1em;
    color: #222;
}
.status-badge {
    display: inline-block;
    padding: 4px 14px;
    border-radius: 12px;
    font-size: 0.93em;
    font-weight: 600;
    color: #222;
    background: #F0F0F0;
    border: none;
}
.status-in-review {
    background: #ff9800;
    color: #fff;
}
.status-offered {
    background: #E5D0FF;
    color: #7C1CD8;
}
.status-rejected {
    background: #FFD1D1;
    color: #D10000;
}
.status-accepted {
    background: #D1FFD9;
    color: #2B834B;
}
.status-pending {
    background: #FFD600;
    color: #222;
}
.status-declined {
    background: #8e24aa;
    color: #fff;
}
.status-other {
    background: #E0E0E0;
    color: #555;
}
@media (max-width: 700px) {
    .student-app-table-container {
        padding: 16px 6px 10px 6px;
        max-width: 100vw;
    }
}
</style>
<div class="student-app-table-container">
    <div class="student-app-title">Student Status</div>
    <span class="student-app-name"><?= htmlspecialchars($stud_name) ?></span>
    <table class="student-app-table">
        <tr>
            <th>Position Applied</th>
            <th>Company</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
<?php
// Get applications
$sql = "SELECT il.Int_Position, e.Comp_Name, sa.App_Status, sa.App_Date
        FROM student_application sa
        JOIN intern_listings il ON sa.InternshipID = il.InternshipID
        JOIN employer e ON il.EmployerID = e.EmployerID
        WHERE sa.StudentID = ?
        ORDER BY sa.App_Date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Status badge color logic
        $status = htmlspecialchars($row['App_Status']);
        $badgeClass = "status-other";
        if (strcasecmp($status, "Pending") === 0) $badgeClass = "status-pending";
        else if (strcasecmp($status, "Declined") === 0) $badgeClass = "status-declined";
        else if (stripos($status, "review") !== false) $badgeClass = "status-in-review";
        else if (stripos($status, "offered") !== false) $badgeClass = "status-offered";
        else if (stripos($status, "reject") !== false) $badgeClass = "status-rejected";
        else if (stripos($status, "accept") !== false) $badgeClass = "status-accepted";

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Int_Position']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Comp_Name']) . "</td>";
        echo "<td>" . date("d/m/Y", strtotime($row['App_Date'])) . "</td>";
        echo "<td><span class='status-badge $badgeClass'>" . $status . "</span></td>";
        echo "</tr>";
    }
} else {
    echo '<tr><td colspan="4" style="text-align:center;background:transparent;color:#999;">No applications found for this student.</td></tr>';
}
$stmt->close();
if (isset($conn)) mysqli_close($conn);
?>
    </table>
</div>