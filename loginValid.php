<?php
session_start();
include('config/connect.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit;
}

$Email    = $_POST['Email']    ?? '';
$password = $_POST['password'] ?? '';

$tables = ['student', 'person_in_charge', 'academic_unit', 'employer', 'dummy_student'];
$user   = null;
$source = null;

foreach ($tables as $tbl) {
    $sql  = "SELECT * FROM `$tbl` WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $user   = $res->fetch_assoc();
        $source = $tbl;
        break;
    }
}

if (!$user) {
    echo "<script>alert('Account not found');</script>";
    echo "<meta http-equiv='refresh' content='0;URL=login.html'>";
    exit;
}

function endsWith($haystack, $needle) {
    return substr($haystack, -strlen($needle)) === $needle;
}

if (endsWith($Email, '@student.utem.edu.my')) {
    if ($source === 'student' || $source === 'dummy_student') {
        $approve = $user['approve'] ?? null;

        if ($approve === null) {
            echo "<script>alert('Account not registered. Please register first');</script>";
            echo "<meta http-equiv='refresh' content='0;URL=login.html'>";
            exit;
        }
        if ($approve == 3) {
            echo "<script>alert('You are not eligible to join Intechship');</script>";
            echo "<meta http-equiv='refresh' content='0;URL=login.html'>";
            exit;
        }
        if ($approve == 0) {
            echo "<script>alert('Your account is pending approval');</script>";
            echo "<meta http-equiv='refresh' content='0;URL=login.html'>";
            exit;
        }
    }
}

if (!password_verify($password, $user['password'])) {
    echo "<script>alert('Wrong Password');</script>";
    echo "<meta http-equiv='refresh' content='0;URL=login.html'>";
    exit;
}

if ($source === 'dummy_student') {
    $sql = "INSERT INTO student (Stud_Name, Stud_MatricNo, Stud_Phone, Stud_Programme,Email, Approve, Identity, Stud_protype, password) VALUES (?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE
            Stud_Name     = VALUES(Stud_Name),
            Stud_MatricNo = VALUES(Stud_MatricNo),
            Stud_Phone    = VALUES(Stud_Phone),
            Stud_Programme= VALUES(Stud_Programme),
            Approve       = VALUES(Approve),
            Identity      = VALUES(Identity),
            Stud_protype  = VALUES(Stud_protype),
            password      = VALUES(password)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss",
        $user['Stud_Name'],
        $user['Stud_MatricNo'],
        $user['Stud_Phone'],
        $user['Stud_Programme'],
        $user['Email'],
        $user['approve'],
        $user['Identity'],
        $user['Stud_protype'],
        $user['password']
    );
    $stmt->execute();

    $source = 'student';
}

$_SESSION['Email'] = $Email;

if (endsWith($Email, '@student.utem.edu.my')) 
{
    $_SESSION['Stud_Name']  = $user['Stud_Name'];
    $_SESSION['studentID']  = $user['StudentID'] ?? null;
    header("Location: Profile.php");
} elseif (endsWith($Email, '@university.edu')) 
{
    $_SESSION['PicID']        = $user['PicID'];
    $_SESSION['Pic_Name']     = $user['Pic_Name'];
    $_SESSION['Program_Desc'] = $user['Program_Desc'];
    header("Location: profileadmin.php");
} elseif (endsWith($Email, '@employer.my')) 
{
    $_SESSION['EmployerID'] = $user['EmployerID'];
    $_SESSION['Comp_Name']  = $user['Comp_Name'];
    header("Location: PostListing.php");
} elseif (endsWith($Email, '@academic.my')) 
{
    $_SESSION['academicID'] = $user['academicID'];
    $_SESSION['Name']       = $user['Name'];
    header("Location: AdminRegistration.php");
}

exit;
?>
