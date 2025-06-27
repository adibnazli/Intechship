<?php
session_start();
include('config/connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $Email    = $_POST['Email'] ?? '';
    $Identity = $_POST['idno'] ?? ''; 

    if (!str_ends_with(strtolower($Email), '@student.utem.edu.my')) {
        echo "<script>alert('Only student.utem.edu.my emails allowed');</script>";
        echo "<meta http-equiv='refresh' content='0;URL=StudentCheck.php'>";
        exit;
    }

    $sql  = "SELECT * FROM dummy_student WHERE Email = ? AND Identity = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $Email, $Identity);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (isset($row['approve']) && $row['approve'] == 3) {
            echo "<script>alert('Your account request was rejected. Please contact the PIC for further information.');</script>";
            echo "<meta http-equiv='refresh' content='0;URL=StudentCheck.php'>";
            exit;
        }

        if (isset($row['approve']) && $row['approve'] == 0) {
            echo "<script>alert('Your account is still pending approval.');</script>";
            echo "<meta http-equiv='refresh' content='0;URL=StudentCheck.php'>";
            exit;
        }

        $_SESSION['Stud_Name']     = $row['Stud_Name'] ?? '';
        $_SESSION['Email']         = $row['Email'] ?? '';
        $_SESSION['Stud_MatricNo'] = $row['Stud_MatricNo'] ?? '';

        $check  = $conn->prepare("SELECT 1 FROM student WHERE Email = ?");
        $check->bind_param("s", $Email);
        $check->execute();
        $exists = $check->get_result()->num_rows;

        if ($exists) {
            echo "<script>alert('Account already exists! Please login');</script>";
            echo "<meta http-equiv='refresh' content='0;URL=login.html'>";
        } else {
            header("Location: register.php");
        }
        exit;
    } else {
        echo "<script>alert('Student not found');</script>";
        echo "<meta http-equiv='refresh' content='0;URL=StudentCheck.php'>";
        exit;
    }
}
?>
