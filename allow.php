<?php
include('config/config.php');

$studentID = $_GET['id'] ?? '';

if ($studentID !== '') {

    // Step 1: Set student table to approve = 0
    $stmt1 = $conn->prepare("UPDATE student SET approve = 0 WHERE StudentID = ?");
    $stmt1->bind_param("i", $studentID);
    $stmt1->execute();

    // Step 2: Get Stud_MatricNo based on StudentID
    $stmtGet = $conn->prepare("SELECT Stud_MatricNo FROM student WHERE StudentID = ?");
    $stmtGet->bind_param("i", $studentID);
    $stmtGet->execute();
    $stmtGet->bind_result($matric);
    $stmtGet->fetch();
    $stmtGet->close();

    // Step 3: Update dummy_student using MatricNo
    if ($matric) {
        $stmt2 = $conn->prepare("UPDATE dummy_student SET approve = 0 WHERE Stud_MatricNo = ?");
        $stmt2->bind_param("s", $matric);
        $stmt2->execute();
    }

    echo "<script>alert('Student set to pending.'); window.location.href='adminApprove.php';</script>";
} else {
    echo "<script>alert('Invalid student ID.'); window.location.href='adminApprove.php';</script>";
}
?>
