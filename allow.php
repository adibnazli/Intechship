<?php
include('config/config.php');

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("UPDATE student SET approve = NULL WHERE StudentID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<script>alert('Student allowed to Sign up.');window.location.href='adminApprove.php';</script>";
}
?>
