<?php
session_start();
include("config/config.php");

if (!isset($_SESSION['studentID'])) {
    die("Access denied.");
}

$studentID = $_SESSION['studentID'];
$skills = $_POST['skills'] ?? '';
$locations = $_POST['locations'] ?? '';
$allowance = $_POST['allowance'] ?? null;
$allowanceType = $_POST['allowance_type'] ?? null;

// ===== HANDLE RESUME UPLOAD =====
$resumePath = '';
if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
    $uploadDir = "uploads/resumes/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // create directory if not exist
    }

    $fileTmp = $_FILES['resume']['tmp_name'];
    $fileName = basename($_FILES['resume']['name']);
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

    // Rename file to avoid conflict
   // Use original file name (with studentID prepended for uniqueness)
$cleanFileName = preg_replace("/[^A-Za-z0-9_\-\.]/", '_', $fileName); // sanitize
$newFileName = $studentID . "_" . $cleanFileName;
$uploadPath = $uploadDir . $newFileName;


    if (move_uploaded_file($fileTmp, $uploadPath)) {
        $resumePath = $uploadPath;
    } else {
        echo "Failed to upload resume.";
        exit;
    }
}

// ===== UPDATE STUDENT PROFILE =====
$query = "UPDATE student SET 
    Stud_Skills = ?, 
    Pref_Location = ?, 
    Preferred_Allowance = ?, 
    Allowance_Type = ?" .
    ($resumePath ? ", Stud_ResumePath = ?" : "") . 
    " WHERE StudentID = ?";

$stmt = $conn->prepare($query);

if ($resumePath) {
    $stmt->bind_param("sssssi", $skills, $locations, $allowance, $allowanceType, $resumePath, $studentID);
} else {
    $stmt->bind_param("ssdsi", $skills, $locations, $allowance, $allowanceType, $studentID);
}

if ($stmt->execute()) {
    echo "<script>alert('Profile updated successfully!'); window.location.replace('Profile.php');</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
