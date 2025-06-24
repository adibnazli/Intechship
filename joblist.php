<?php
session_start();
include("config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $position = $_POST['job_title'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $allowance = $_POST['allowance'];
    $details = $_POST['job_details'];
    $programmes = isset($_POST['programme']) ? implode(', ', $_POST['programme']) : ' ';
    $qualification = $_POST['qualification'];

    $employerID = $_SESSION['EmployerID'];

    $sql = "INSERT INTO intern_listings (Int_Position, Int_State, Int_City, Int_Qualification, Int_Programme, Int_Allowance, Int_Details, EmployerID) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssdsi", $position, $state, $city, $qualification, $programmes, $allowance, $details, $employerID);

    if ($stmt->execute()) {
        echo "Job posted successfully!";
        header("Location: PostListing.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
