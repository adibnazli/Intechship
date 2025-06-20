<?php
session_start();
include("config/config.php");

// Include PHPMailer
require 'uploads/phpmailer/PHPMailer.php';
require 'uploads/phpmailer/SMTP.php';
require 'uploads/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $company = $_POST['company'] ?? '';
    $appid = $_POST['appid'] ?? '';

    // Get student name
    $sql = "SELECT student.Stud_Name 
            FROM student_application 
            JOIN student ON student_application.StudentID = student.StudentID 
            WHERE student_application.ApplicationID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $studentName = $row['Stud_Name'];

        // Create PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'b597cefe42b0e4';
            $mail->Password = 'd19776e18e0ce8';
            $mail->Port = 2525; // Default Mailtrap port

            // Recipients
            $mail->setFrom('no-reply@yourcompany.com', "$company Recruitment");
            $mail->addAddress($email, $studentName);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = "Interview Invitation from $company";
            $mail->Body = "
                Dear $studentName,<br><br>
                Congratulations! You have been shortlisted for an interview with <strong>$company</strong>.<br><br>
                <strong>Interview Details:</strong><br>
                - Company: $company<br>
                - Application ID: $appid<br>
                - Date: [Insert Date Here]<br>
                - Time: [Insert Time Here]<br>
                - Location/Link: [Insert Location or Online Link Here]<br><br>
                Please confirm your availability by replying to this email.<br><br>
                Best regards,<br>
                $company Recruitment Team
            ";

            // Send the email
            $mail->send();
            echo "Interview email sent to $email.";
        } catch (Exception $e) {
            echo "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Student not found for Application ID: $appid";
    }
}
?>
