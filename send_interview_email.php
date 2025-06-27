<?php
session_start();
include('config/connect.php');

require 'uploads/phpmailer/PHPMailer.php';
require 'uploads/phpmailer/SMTP.php';
require 'uploads/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Only get minimal inputs first (appid is enough to check)
$email = $_POST['email'] ?? '';
$company = $_POST['company'] ?? '';
$appid = isset($_POST['appid']) ? (int) $_POST['appid'] : 0;

// Step 1: Check if application status allows sending interview email
$check = $conn->prepare("SELECT App_Status FROM student_application WHERE ApplicationID = ?");
$check->bind_param("i", $appid);
$check->execute();
$statusResult = $check->get_result();

if ($statusRow = $statusResult->fetch_assoc()) {
    $currentStatus = $statusRow['App_Status'];

    if ($currentStatus === 'Pending') {
        echo "❌ You must review the student's resume before inviting them to interview.";
        exit;
    }

    if (in_array($currentStatus, ['Offered', 'Rejected', 'Interview'])) {
        echo "❌ An email has already been sent for this application ($currentStatus).";
        exit;
    }
} else {
    echo "❌ Invalid Application ID.";
    exit;
}

// Step 2: Now safe to collect interview details
$date = $_POST['date'] ?? '[Insert Date Here]';
$time = $_POST['time'] ?? '[Insert Time Here]';
$location = $_POST['location'] ?? '[Insert Location Here]';

// Step 3: Get student name
$sql = "SELECT student.Stud_Name FROM student_application 
        JOIN student ON student_application.StudentID = student.StudentID 
        WHERE student_application.ApplicationID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appid);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $studentName = $row['Stud_Name'];
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '19bd4b9b2eef33';
        $mail->Password = '52964182414a6b';
        $mail->Port = 2525;

        $mail->setFrom('no-reply@yourcompany.com', "$company Recruitment");
        $mail->addAddress($email, $studentName);

        $mail->isHTML(true);
        $mail->Subject = "Interview Invitation from $company";
        $mail->Body = "
            Dear $studentName,<br><br>
            Congratulations! You have been shortlisted for an interview with <strong>$company</strong>.<br><br>
            <strong>Interview Details:</strong><br>
            - Company: $company<br>
            - Date: $date<br>
            - Time: $time<br>
            - Location/Link: $location<br><br>
            Please confirm your availability by replying to this email.<br><br>
            Best regards,<br>
            $company Recruitment Team
        ";

        $mail->send();
        echo "✅ Internship interview email sent to $email.";

        $stmt = $conn->prepare("UPDATE student_application SET App_Status = 'Interview' WHERE ApplicationID = ?");
        $stmt->bind_param("i", $appid);
        $stmt->execute();

    } catch (Exception $e) {
        echo "❌ Failed to send email. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "❌ Student not found for Application ID: $appid";
}
?>
