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
    $sql = "SELECT student.Stud_Name, intern_listings.Int_Position, intern_listings.Int_State, intern_listings.Int_City
            FROM student_application 
            JOIN student ON student_application.StudentID = student.StudentID 
            JOIN intern_listings ON student_application.InternshipID = intern_listings.InternshipID
            WHERE student_application.ApplicationID = ?";

    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $studentName = $row['Stud_Name'];
        $position = $row['Int_Position'];

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
            $mail->Subject = "Internship Application Outcome from $company";
            $mail->Body = "
                Dear $studentName,<br><br>
                Thank you for your interest in the internship opportunity at <strong>$company</strong>.<br><br>
                We appreciate the time and effort you invested in your application for the <strong>$position</strong> position.<br><br>
                After careful consideration, we regret to inform you that you have not been selected for the internship.<br><br>
                Please do not be discouraged. We encourage you to apply for future opportunities that align with your interests and skills.<br><br>
                We wish you all the best in your academic and professional endeavors.<br><br>
                Kind regards,<br>
                $company Recruitment Team
            ";

            // Send the email
            $mail->send();
            echo "Internship rejection email sent to $email.";

            // Update application status in database
            $stmt = $conn->prepare("UPDATE student_application SET App_Status = 'Rejected' WHERE ApplicationID = ?");
            $stmt->bind_param("i", $appid);
        } 
        catch (Exception $e) {
            echo "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Student not found for Application ID: $appid";
    }
}
?>
