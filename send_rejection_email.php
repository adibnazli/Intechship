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
    $appid = isset($_POST['appid']) ? (int) $_POST['appid'] : 0;
    
    // Protect: if already sent any email for this app, block
    $check = $conn->prepare("SELECT App_Status FROM student_application WHERE ApplicationID = ?");
    $check->bind_param("i", $appid);
    $check->execute();
    $statusResult = $check->get_result();

    if ($statusRow = $statusResult->fetch_assoc()) {
        $currentStatus = $statusRow['App_Status'];
        if (in_array($currentStatus, ['Offered', 'Rejected'])) {
            echo "❌ An email has already been sent for this application ($currentStatus).";
            exit;
        }
    } else {
        echo "❌ Invalid Application ID.";
        exit;
    }

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
            $mail->Username = 'f54adc311ed7fc';
            $mail->Password = 'c2a89f88f9a14c';
            $mail->Port = 2525; // Default Mailtrap port

            // Recipients
            $mail->setFrom('no-reply@yourcompany.com', "$company Recruitment");
            $mail->addAddress($email, $studentName);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = "Internship Application Outcome from $company";
            $mail->Body = "
                Dear $studentName,<br><br>

                Thank you for interviewing for the position of <strong>$position</strong> with <strong>$company</strong>. 
                We enjoyed the interview and hope your visit was both interesting and informative.<br><br>

                Unfortunately, we are only able to select one candidate from among the many that applied for the position. 
                Therefore, we regret to inform you that you were not selected as the leading candidate for the position of <strong>$position</strong>. 
                We are currently unable to offer you an internship with <strong>$company</strong>.<br><br>

                We wish you the best of success with your future endeavors. Please be mindful of any future opportunities that may exist with our department. 
                We encourage you to apply for future jobs with our department.<br><br>

                Thank you for your interest in <strong>$company</strong>.<br><br>

                Regards,<br>
                $company Recruitment Team
            ";


            // Send the email
            $mail->send();
            echo "✅ Internship rejection email sent to $email.";

            // Update application status in database
            $stmt = $conn->prepare("UPDATE student_application SET App_Status = 'Rejected' WHERE ApplicationID = ?");
            $stmt->bind_param("i", $appid);
            $stmt->execute();
        } 
        catch (Exception $e) {
            echo "❌ Failed to send email. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "❌ Student not found for Application ID: $appid";
    }
}
?>
