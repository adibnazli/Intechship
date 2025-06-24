<?php
include('config/config.php');

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("UPDATE student SET approve = 1 WHERE StudentID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<script>alert('Student approved.');window.location.href='adminApprove.php';</script>";
}
?>
