<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . "/config/config.php");

$program_desc = '';
if (!empty($_SESSION['Program_Desc'])) {
    $program_desc = trim($_SESSION['Program_Desc']);
}
$likeValue = $program_desc . '%';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($program_desc !== '') {
    if ($search !== '') {
        $sql_students = "SELECT s.StudentID, s.Stud_Name, s.Stud_Programme FROM student s
            WHERE s.Stud_Programme LIKE ? AND s.Stud_Name LIKE ?
            ORDER BY s.Stud_Name";
        $stmt = $conn->prepare($sql_students);
        $searchName = "%$search%";
        $stmt->bind_param("ss", $likeValue, $searchName);
    } else {
        $sql_students = "SELECT s.StudentID, s.Stud_Name, s.Stud_Programme FROM student s
            WHERE s.Stud_Programme LIKE ?
            ORDER BY s.Stud_Name";
        $stmt = $conn->prepare($sql_students);
        $stmt->bind_param("s", $likeValue);
    }
    $stmt->execute();
    $result_students = $stmt->get_result();
    $stmt->close();
} else {
    $result_students = false;
}
if ($result_students && $result_students->num_rows > 0):
    $num = 1;
    while ($student = $result_students->fetch_assoc()): ?>
        <tr>
            <td><?php echo $num++; ?></td>
            <td class="student-name" data-studentid="<?php echo $student['StudentID']; ?>">
                <?php echo htmlspecialchars($student['Stud_Name']); ?>
            </td>
            <td><?php echo htmlspecialchars($student['Stud_Programme']); ?></td>
        </tr>
    <?php endwhile;
else: ?>
    <tr>
        <td colspan="3" style="text-align:center;">No students found.</td>
    </tr>
<?php endif;
if (isset($conn)) mysqli_close($conn);
?>