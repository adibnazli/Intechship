<?php
session_start();
include("config/config.php");

if (!isset($_SESSION['studentID'])) {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

$studentID = $_SESSION['studentID'];

if (isset($_FILES['resume']) && $_FILES['resume']['error'] === 0) {
    $uploadDir = "uploads/resumes/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileTmp = $_FILES['resume']['tmp_name'];
    $fileName = basename($_FILES['resume']['name']);
    $cleanFileName = preg_replace("/[^A-Za-z0-9_\-\.]/", '_', $fileName);
$newFileName = $cleanFileName; // Don't add studentID or random number
$uploadPath = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        // Save path into database
        $sql = "UPDATE student SET Stud_ResumePath = ? WHERE StudentID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $uploadPath, $studentID);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Failed to upload file.";
    }
} else {
    echo "No file selected or upload error.";
}
?>
