<?php
session_start();
include("config/config.php");

// Check if student is logged in
if (!isset($_SESSION['StudentID'])) {
  echo "Please log in to apply.";
  exit;
}

$studentID = $_SESSION['StudentID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['internship_id'])) {
  $internshipID = intval($_POST['internship_id']);

  // Get employer ID from internship listing
  $query = "SELECT EmployerID FROM intern_listings WHERE InternshipID = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $internshipID);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $employerID = $row['EmployerID'];

    // Check if already applied
    $check = "SELECT * FROM student_application WHERE StudentID = ? AND InternshipID = ?";
    $checkStmt = $conn->prepare($check);
    $checkStmt->bind_param("ii", $studentID, $internshipID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
      echo "You have already applied for this internship.";
    } else {
      // Insert application
      $insert = "INSERT INTO student_application (StudentID, InternshipID, App_Date, App_Status, EmployerID) 
                 VALUES (?, ?, NOW(), 'Pending', ?)";
      $insertStmt = $conn->prepare($insert);
      $insertStmt->bind_param("iii", $studentID, $internshipID, $employerID);

      if ($insertStmt->execute()) {
        echo "Application submitted successfully!";
        // You can redirect if you want:
        // header("Location: application_success.php");
      } else {
        echo "Failed to apply. Please try again.";
      }
    }
  } else {
    echo "Invalid internship ID.";
  }
} else {
  echo "Invalid request.";
}
?>
