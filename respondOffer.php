<?php
session_start();
include('config/connect.php');

if (!isset($_SESSION['studentID'])) { header('Location: login.html'); exit; }

require 'uploads/phpmailer/PHPMailer.php';
require 'uploads/phpmailer/SMTP.php';
require 'uploads/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: progressStudent.php'); exit; }

$applicationID = intval($_POST['applicationID'] ?? 0);
$response      = $_POST['response'] ?? '';

if (!$applicationID || !in_array($response, ['Accepted', 'Declined'])) { die('Invalid request'); }

$sql = "SELECT sa.ApplicationID, sa.App_Status,
               s.Stud_Name, s.Email AS Stud_Email,
               il.Int_Position,
               e.Comp_Name, e.Email AS Comp_Email
        FROM student_application sa
        JOIN student s        ON sa.StudentID   = s.StudentID
        JOIN intern_listings il ON sa.InternshipID = il.InternshipID
        JOIN employer e       ON il.EmployerID  = e.EmployerID
        WHERE sa.ApplicationID = ? AND sa.StudentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $applicationID, $_SESSION['studentID']);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();

if (!$app || $app['App_Status'] !== 'Offered') {
    echo "<script>alert('Offer not available');history.back();</script>";
    exit;
}

$conn->begin_transaction();

$upd = $conn->prepare("UPDATE student_application SET App_Status = ? WHERE ApplicationID = ?");
$upd->bind_param('si', $response, $applicationID);
$upd->execute();

if ($response === 'Accepted') {
    $decl = $conn->prepare(
        "UPDATE student_application
         SET App_Status = 'Declined'
         WHERE StudentID = ? AND App_Status = 'Offered' AND ApplicationID <> ?"
    );
    $decl->bind_param('ii', $_SESSION['studentID'], $applicationID);
    $decl->execute();
}

$conn->commit();

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'e219ddf02b800a';
    $mail->Password   = '690b2325ea3c33';
    $mail->Port       = 2525;
    $mail->setFrom('no-reply@intechship.com', 'InTechShip System');
    $mail->addAddress($app['Comp_Email'], $app['Comp_Name']);

    if ($response === 'Accepted') {
        $mail->Subject = "Offer Accepted - {$app['Int_Position']}";
        $mail->Body    = "
            <h2>Offer Accepted</h2>
            <p>{$app['Stud_Name']} has accepted the internship offer for <strong>{$app['Int_Position']}</strong>.</p>
            <p>Contact: {$app['Stud_Email']}</p>";
    } else {
        $mail->Subject = "Offer Declined - {$app['Int_Position']}";
        $mail->Body    = "
            <h2>Offer Declined</h2>
            <p>{$app['Stud_Name']} has declined the internship offer for <strong>{$app['Int_Position']}</strong>.</p>";
    }
    $mail->isHTML(true);
    $mail->send();
} catch (Exception $e) { $_SESSION['email_error'] = true; }

$_SESSION['response_status'] = $response;
header("Location: progressStudent.php?app=$applicationID");
