<?php
session_start();
include('config/connect.php');

if (!isset($_SESSION['studentID'])) {
    die("Access denied.");
}

$studentID = $_SESSION['studentID'];

// Fetch current resume path
$sql = "SELECT Stud_ResumePath FROM student WHERE StudentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if ($student && !empty($student['Stud_ResumePath'])) {
    $filePath = $student['Stud_ResumePath'];
    if (file_exists($filePath)) {
        unlink($filePath); // delete file
    }

    // Update DB to remove path
    $update = $conn->prepare("UPDATE student SET Stud_ResumePath = NULL WHERE StudentID = ?");
    $update->bind_param("i", $studentID);
    if ($update->execute()) {
        echo "<script>alert('Resume deleted successfully.'); window.location.href='Profile.php';</script>";
    } else {
        echo "Failed to update database.";
    }
    $update->close();
} else {
    echo "<script>alert('No resume to delete.'); window.location.href='Profile.php';</script>";
}

$conn->close();
?>
