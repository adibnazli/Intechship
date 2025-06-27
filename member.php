<?php
session_start();
include('config/connect.php');

if (empty($_SESSION['Email']) || empty($_SESSION['Stud_MatricNo'])) {
    die('Session expired');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $Email      = $_SESSION['Email'];
    $MatricNo   = $_SESSION['Stud_MatricNo'];
    $password   = $_POST['password']   ?? '';
    $repassword = $_POST['repassword'] ?? '';

    if ($password !== $repassword) {
        echo "<script>alert('Passwords do not match!');</script>";
        echo "<meta http-equiv='refresh' content='0;URL=register.php'>";
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    //kemas kini update
    $upd = $conn->prepare("
        UPDATE dummy_student
        SET password = ?, approve = 0
        WHERE Email = ? AND Stud_MatricNo = ?
    ");
    $upd->bind_param('sss', $hashed, $Email, $MatricNo);
    $upd->execute();

    //masukkan data ke dalam jadual student
    $ins = $conn->prepare("
        INSERT INTO student (Stud_Name, Stud_MatricNo, Stud_Phone, Stud_Programme,Email, password, Approve, Identity)
        SELECT Stud_Name, Stud_MatricNo, Stud_Phone, Stud_Programme,Email, ?, 0, Identity
        FROM dummy_student
        WHERE Email = ? ON DUPLICATE KEY UPDATE
            Stud_Phone     = VALUES(Stud_Phone),
            Stud_Programme = VALUES(Stud_Programme),
            password       = VALUES(password),
            Approve        = VALUES(Approve),
            Identity       = VALUES(Identity)
    ");
    
    $ins->bind_param('ss', $hashed, $Email);
    $ins->execute();

    //prompt
    if ($upd->affected_rows > 0 || $ins->affected_rows > 0) {
        echo "<script>alert('Registration successful! Awaiting admin approval');</script>";
        echo "<meta http-equiv='refresh' content='0;URL=login.html'>";
    } else {
        echo "<script>alert('Registration failed');</script>";
        echo "<meta http-equiv='refresh' content='0;URL=register.php'>";
    }
}
?>
