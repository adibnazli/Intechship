<?php
session_start();
include('config/config.php');

$studentID = $_GET['id'] ?? '';

if ($studentID === '') {
    echo "<script>alert('Invalid student ID!'); window.location.href='adminApprove.php';</script>";
    exit;
}

$stmtGet = $conn->prepare("SELECT Stud_MatricNo FROM student WHERE StudentID = ?");
$stmtGet->bind_param("i", $studentID);
$stmtGet->execute();
$stmtGet->bind_result($matric);
$stmtGet->fetch();
$stmtGet->close();

//Delete related records from student_application first
$stmtApp = $conn->prepare("DELETE FROM student_application WHERE StudentID = ?");
$stmtApp->bind_param("i", $studentID);
$stmtApp->execute();


$stmtDel = $conn->prepare("DELETE FROM student WHERE StudentID = ?");
$stmtDel->bind_param("i", $studentID);
$stmtDel->execute();

if ($matric) {
    $stmtDummy = $conn->prepare("UPDATE dummy_student SET approve = NULL, password = NULL WHERE Stud_MatricNo = ?");
    $stmtDummy->bind_param("s", $matric);
    $stmtDummy->execute();
}

echo "<script>alert('Student deleted'); window.location.href='adminApprove.php';</script>";
?>
