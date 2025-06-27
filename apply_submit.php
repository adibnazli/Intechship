<?php
session_start();
include('config/connect.php');

// Check if student is logged in
if (!isset($_SESSION['studentID'])) {
  header("Location: login.php"); // redirect to login page if not logged in
  exit;
}

$studentID = $_SESSION['studentID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['internship_id'])) {
  $internshipID = intval($_POST['internship_id']);

  // Check if already applied
  $check = "SELECT * FROM student_application WHERE StudentID = ? AND InternshipID = ?";
  $checkStmt = $conn->prepare($check);
  $checkStmt->bind_param("ii", $studentID, $internshipID);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();

  if ($checkResult->num_rows > 0) {
    // Already applied
    header("Location: InternshipSearch.php?status=already_applied");
    exit;
  } else {
    // Insert application
    $insert = "INSERT INTO student_application (StudentID, InternshipID, App_Date, App_Status) 
               VALUES (?, ?, NOW(), 'Pending')";
    $insertStmt = $conn->prepare($insert);

    if ($insertStmt) {
      $insertStmt->bind_param("ii", $studentID, $internshipID);
      if ($insertStmt->execute()) {
        header("Location: InternshipSearch.php?status=success");
        exit;
      } else {
        header("Location: InternshipSearch.php?status=fail");
        exit;
      }
    } else {
      header("Location: InternshipSearch.php?status=sql_error");
      exit;
    }
  }
} else {
  header("Location: InternshipSearch.php?status=invalid_request");
  exit;
}
?>
