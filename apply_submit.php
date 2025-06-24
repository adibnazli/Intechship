<?php
session_start();
include("config/config.php");

// Check if student is logged in
if (!isset($_SESSION['studentID'])) {
  echo "Please log in to apply.";
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
    echo "You have already applied for this internship.";
  } else {
    // Insert application (no employerID included)
    $insert = "INSERT INTO student_application (StudentID, InternshipID, App_Date, App_Status) 
               VALUES (?, ?, NOW(), 'Pending')";
    $insertStmt = $conn->prepare($insert);

    if ($insertStmt) {
      $insertStmt->bind_param("ii", $studentID, $internshipID);

      if ($insertStmt->execute()) {
        echo "Application submitted successfully!";
        // header("Location: application_success.php");
      } else {
        echo "Failed to apply. Please try again.";
      }
    } else {
      echo "SQL error: " . $conn->error;
    }
  }
} else {
  echo "Invalid request.";
}
?>
