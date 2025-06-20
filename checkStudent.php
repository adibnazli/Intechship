<?php
session_start();
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $Email = $_POST['Email'];
    $Stud_MatricNo = $_POST['matricno'];

    $sql = "SELECT * FROM student WHERE Email = '$Email' AND Stud_MatricNo = '$Stud_MatricNo'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['Stud_Name'] = $row['Stud_Name'] ?? '';
        $_SESSION['Email'] = $row['Email'] ?? '';
        $_SESSION['Stud_MatricNo'] = $row['Stud_MatricNo'] ?? '';

        if ($row['approve'] == 1) {
            echo "<script>alert('Student approved! Please log in.')</script>";
            session_unset();
            echo "<meta http-equiv='refresh' content='2;URL=login.html'>";
        } 
        else {
            echo "<script>window.location.href = 'register.php';</script>";
        }
    } 
    else {
        echo "<script>alert('Student Not found!')</script>";
        session_unset();
        echo "<meta http-equiv='refresh' content='2;URL=StudentCheck.php'>";
    }
}
?>
