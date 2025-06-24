<?php
session_start();
include('config/config.php');

$Stud_Name = $_SESSION['Stud_Name'] ?? '';
$Email = $_SESSION['Email'] ?? '';
$Stud_MatricNo = $_SESSION['Stud_MatricNo'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];

    if ($password !== $repassword) {
        echo "<script>alert('Passwords do not match!!!');</script>";
        echo "<meta http-equiv='refresh' content='2;URL=register.php'>";
    } else {
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE student SET password = ?, approve = 0 WHERE Email = ? AND Stud_MatricNo = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $hashedPassword, $Email, $Stud_MatricNo);
        
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!!! Please wait for Admin approval.'); window.location.href='login.html';</script>";

        } else {
            echo "<script>alert('Error: Cannot register');</script>";
        }
    }
}
?>