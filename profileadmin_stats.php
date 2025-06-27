<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . "/config/config.php");
header('Content-Type: application/json');

// Check DB connection
if (!isset($conn) || $conn === false) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed.'
    ]);
    exit;
}
if ($conn->connect_errno) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection error: ' . $conn->connect_error
    ]);
    exit;
}

$program_desc = '';
if (!empty($_SESSION['Program_Desc'])) {
    $program_desc = trim($_SESSION['Program_Desc']);
}
$likeValue = $program_desc . '%';

$student_total = 0;
$applied_total = 0;
$success_total = 0;

if ($program_desc !== '') {
    // Total students
    $sql_count = "SELECT COUNT(*) FROM student WHERE Stud_Programme LIKE ?";
    $stmt = $conn->prepare($sql_count);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("s", $likeValue);
    $stmt->execute();
    $stmt->bind_result($student_total);
    $stmt->fetch();
    $stmt->close();

    // Total applied
    $sql_applied = "SELECT COUNT(DISTINCT sa.StudentID) 
                    FROM student_application sa
                    JOIN student s ON sa.StudentID = s.StudentID
                    WHERE s.Stud_Programme LIKE ?";
    $stmt = $conn->prepare($sql_applied);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("s", $likeValue);
    $stmt->execute();
    $stmt->bind_result($applied_total);
    $stmt->fetch();
    $stmt->close();

    // Total successful
    $sql_success = "SELECT COUNT(DISTINCT sa.StudentID)
                    FROM student_application sa
                    JOIN student s ON sa.StudentID = s.StudentID
                    WHERE s.Stud_Programme LIKE ? AND sa.App_Status = 'Accepted'";
    $stmt = $conn->prepare($sql_success);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("s", $likeValue);
    $stmt->execute();
    $stmt->bind_result($success_total);
    $stmt->fetch();
    $stmt->close();
}

echo json_encode([
    'success' => true,
    'student_total' => $student_total,
    'applied_total' => $applied_total,
    'success_total' => $success_total
]);
if (isset($conn)) mysqli_close($conn);
?>