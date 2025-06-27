<?php
include('config/config.php');

$studentID = $_GET['id'] ?? '';

if ($studentID !== '') {

    /*Reject dalam jadual student */
    $stmt1 = $conn->prepare(
        "UPDATE student SET approve = 3 WHERE StudentID = ?"
    );
    $stmt1->bind_param("i", $studentID);
    $stmt1->execute();

    /*Dapatkan Matric No untuk ID ni*/
    $stmtGet = $conn->prepare(
        "SELECT Stud_MatricNo FROM student WHERE StudentID = ?"
    );
    $stmtGet->bind_param("i", $studentID);
    $stmtGet->execute();
    $stmtGet->bind_result($matric);     // $matric akan ada nilainya
    $stmtGet->fetch();
    $stmtGet->close();

    /* Reject dalam dummy_student ikut Matric*/
    if ($matric) {
        $stmt2 = $conn->prepare(
            "UPDATE dummy_student SET approve = 3 WHERE Stud_MatricNo = ?"
        );
        $stmt2->bind_param("s", $matric);
        $stmt2->execute();
    }

    echo "<script>alert('Student rejected.'); window.location.href='adminApprove.php';</script>";
} else {
    echo "<script>alert('Invalid student ID.'); window.location.href='adminApprove.php';</script>";
}
?>
