<?php
session_start();
include('config/connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employerID = $_POST['employer_id'];
    $name = $_POST['company_name'];
    $regNo = $_POST['company_registration'];
    $address = $_POST['company_address'];
    $email = $_POST['company_email'];

    $sql = "UPDATE employer 
            SET Comp_Name = ?, Comp_RegistrationNo = ?, Address = ?, Email = ? 
            WHERE EmployerID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $regNo, $address, $email, $employerID);

    if ($stmt->execute()) {
        $_SESSION['company_updated'] = true;
        header("Location: company_registration.php");
    } else {
        echo "Error updating company: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
