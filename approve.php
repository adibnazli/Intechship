<?php
session_start();
include('config/connect.php');

$studentID = $_GET['id'] ?? '';
$picID = $_SESSION['PicID'] ?? null; //ID person_in_charge yang sedang login

if ($studentID !== '' && $picID !== null) {

    //set approve = 1 dan simpan PicID yang luluskan student
    $stmt1 = $conn->prepare("UPDATE student SET approve = 1, PicID = ? WHERE StudentID = ?");
    $stmt1->bind_param("ii", $picID, $studentID);
    $stmt1->execute();

    //dapatkan Stud_MatricNo
    $stmtGet = $conn->prepare("SELECT Stud_MatricNo FROM student WHERE StudentID = ?");
    $stmtGet->bind_param("i", $studentID);
    $stmtGet->execute();
    $stmtGet->bind_result($matric);
    $stmtGet->fetch();
    $stmtGet->close();

    //Update dummy_student approve = 1 berdasarkan matric
    if ($matric) {
        $stmt2 = $conn->prepare("UPDATE dummy_student SET approve = 1 WHERE Stud_MatricNo = ?");
        $stmt2->bind_param("s", $matric);
        $stmt2->execute();
    }

    echo "<script>alert('Student approved!'); window.location.href='adminApprove.php';</script>";
} else {
    echo "<script>alert('Invalid student ID or PIC not found!'); window.location.href='adminApprove.php';</script>";
}
?>
