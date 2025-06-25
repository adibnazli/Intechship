<?php
session_start();
include("config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = $_POST['company_name'];
    $company_registration = $_POST['company_registration'];
    $company_address = $_POST['company_address'];
    $company_email = $_POST['company_email'];
    $company_pass = $_POST['company_pass'];

    // Hash password
    $hashedPassword = password_hash($company_pass, PASSWORD_DEFAULT);

    $PicID = $_SESSION['PicID'];

    $check_sql = "SELECT * FROM employer WHERE Email = ? OR Comp_RegistrationNo = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $company_email, $company_registration);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        die("Email or Company Registration No already exists.");
    }
    $check_stmt->close();


    $sql = "INSERT INTO employer (Comp_Name, Address, Comp_RegistrationNo, PicID, Email, password) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiss", $company_name, $company_address, $company_registration, $PicID, $company_email, $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['company_registered'] = true;
        header("Location: company_registration.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
