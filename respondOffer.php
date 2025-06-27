<?php
session_start();
include "config/config.php";
require 'uploads/phpmailer/PHPMailer.php';
require 'uploads/phpmailer/SMTP.php';
require 'uploads/phpmailer/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['studentID'])) exit('Access denied');
if ($_SERVER['REQUEST_METHOD']!=='POST') header("Location: progressStudent.php");

$appID   = intval($_POST['applicationID'] ?? 0);
$reply   = $_POST['response'] ?? '';
if (!$appID || !in_array($reply,['Accepted','Declined'])) exit('Invalid data');

$sql = "SELECT sa.ApplicationID, sa.App_Status, sa.StudentID,
               s.Stud_Name, s.Email AS Stud_Email,
               il.Int_Position,
               e.Comp_Name, e.Email AS Comp_Email
        FROM student_application sa
        JOIN student s        ON sa.StudentID   = s.StudentID
        JOIN intern_listings il ON sa.InternshipID = il.InternshipID
        JOIN employer e       ON il.EmployerID  = e.EmployerID
        WHERE sa.ApplicationID=? AND sa.StudentID=?";
$stmt=$conn->prepare($sql);
$stmt->bind_param("ii",$appID,$_SESSION['studentID']);
$stmt->execute();
$row=$stmt->get_result()->fetch_assoc();
if(!$row||$row['App_Status']!=='Offered'){echo"<script>alert('Offer no longer valid');history.back();</script>";exit;}

$conn->begin_transaction();
$upd=$conn->prepare("UPDATE student_application SET App_Status=? WHERE ApplicationID=?");
$upd->bind_param("si",$reply,$appID);
$upd->execute();

if($reply==='Accepted'){
    $decl="UPDATE student_application
           SET App_Status='Declined'
           WHERE StudentID=? AND App_Status='Offered' AND ApplicationID<>?";
    $stDecl=$conn->prepare($decl);
    $stDecl->bind_param("ii",$_SESSION['studentID'],$appID);
    $stDecl->execute();
}
$conn->commit();

$mail=new PHPMailer(true);
try{
    $mail->isSMTP();
    $mail->Host='sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth=true;
    $mail->Username='e219ddf02b800a';
    $mail->Password='690b2325ea3c33';
    $mail->Port=2525;

    $mail->setFrom('no-reply@intechship.com','InTechShip System');
    $mail->addAddress($row['Comp_Email'],$row['Comp_Name']);

    if($reply==='Accepted'){
        $mail->Subject="Offer Accepted - {$row['Int_Position']}";
        $mail->Body="<h2>Offer Accepted</h2>
            <p>Dear {$row['Comp_Name']} Team,</p>
            <p><strong>{$row['Stud_Name']}</strong> has accepted your internship offer for <strong>{$row['Int_Position']}</strong>.</p>
            <p>Student contact: {$row['Stud_Email']}</p>
            <p>Best regards,<br>InTechShip Team</p>";
    }else{
        $mail->Subject="Offer Declined - {$row['Int_Position']}";
        $mail->Body="<h2>Offer Declined</h2>
            <p>Dear {$row['Comp_Name']} Team,</p>
            <p><strong>{$row['Stud_Name']}</strong> has declined your internship offer for <strong>{$row['Int_Position']}</strong>.</p>
            <p>Best regards,<br>InTechShip Team</p>";
    }
    $mail->isHTML(true);
    $mail->send();
    $_SESSION['response_status']=$reply;
}catch(Exception $e){
    $_SESSION['response_status']=$reply;
    $_SESSION['email_error']=true;
}
header("Location: progressStudent.php?app=$appID");
