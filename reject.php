<?php
include('config/config.php');

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("UPDATE student SET approve = 3 WHERE StudentID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<script>alert('Student Rejected.');window.location.href='adminApprove.php';</script>";
}
?>
