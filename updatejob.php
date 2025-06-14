<?php
include("config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['internship_id'];
    $position = $_POST['job_title'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $programmes = isset($_POST['programme']) ? implode(',', $_POST['programme']) : '';
    $allowance = $_POST['allowance'];
    $details = $_POST['job_details'];

    $sql = "UPDATE intern_listings SET Int_Position=?, Int_State=?, Int_City=?, Int_Programme=?, Int_Allowance=?, Int_Details=? WHERE InternshipID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdsi", $position, $state, $city, $programmes, $allowance, $details, $id);

    if ($stmt->execute()) {
        header("Location: PostListing.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
