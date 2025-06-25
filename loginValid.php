<?php
session_start();
include("config/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['Email'] = $_POST['Email'] ?? '';
    $_SESSION['password'] = $_POST['password'] ?? '';
}

if (!empty($_SESSION['Email']) && !empty($_SESSION['password'])) {
    $Email = $_SESSION['Email'];
    $password = $_SESSION['password'];

    $tables = ['student', 'person_in_charge', 'academic_unit', 'employer'];
    $user = null;
    $matchedTable = null;

    foreach ($tables as $table) {
        $sql = "SELECT * FROM `$table` WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $Email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $matchedTable = $table;
            break;
        }
    }

    
    // If user found in DB
    if ($user) {
        if(str_ends_with($Email, '@student.utem.edu.my') && isset($user['approve']) && $user['approve'] == 3){
            echo "<script>alert('You are not eligible to join Intechship');</script>";
            session_unset();
            echo "<meta http-equiv='refresh' content='2;URL=login.html'>";
            exit;
        }

        if (str_ends_with($Email, '@student.utem.edu.my') ||str_ends_with($Email, '@university.edu') ||str_ends_with($Email, '@employer.my') ||str_ends_with($Email, '@academic.my')
        ) {
            if (empty($user['password']) && str_ends_with($Email, '@student.utem.edu.my')) {
                echo "<script>alert('Your account is not approved yet! Please register first');</script>";
                session_unset();
                echo "<meta http-equiv='refresh' content='2;URL=login.html'>";
                exit;
            }

            // Now verify password
            if (password_verify($password, $user['password'])) {
                if (str_ends_with($Email, '@student.utem.edu.my')) {
                    $_SESSION['Stud_Name'] = $user['Stud_Name'];
                    $_SESSION['studentID'] = $user['StudentID'];
                    if ($user['approve'] == 1) {
                        header("Location: Profile.php");
                    } else {
                        echo "<script>alert('Your account is not approved yet! Please register first');</script>";
                        session_unset();
                        echo "<meta http-equiv='refresh' content='2;URL=login.html'>";
                    }

                } elseif (str_ends_with($Email, '@university.edu')) {
                    $_SESSION['PicID'] = $user['PicID'];
                    $_SESSION['Pic_Name'] = $user['Pic_Name'];

                    header("Location: adminApprove.php");

                } elseif (str_ends_with($Email, '@employer.my')) {
                    $_SESSION['EmployerID'] = $user['EmployerID'];
                    $_SESSION['Comp_Name'] = $user['Comp_Name'];
                    header("Location: PostListing.php");

                } elseif (str_ends_with($Email, '@academic.my')) {
                    header("Location: AdminRegistration.php");
                }
                exit;

            } else {
                echo "<script>alert('Wrong Password');</script>";
                session_unset();
                echo "<meta http-equiv='refresh' content='2;URL=login.html'>";
                exit;
            }

        } 

    } else {
            echo "<script>alert('Wrong Domain Name or Email Address');</script>";
            session_unset();
            echo "<meta http-equiv='refresh' content='2;URL=login.html'>";
            exit;
        }
}
?>
