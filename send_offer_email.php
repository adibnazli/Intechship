<?php
session_start();
include('config/connect.php');

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
    $start_date = $_POST['start_date'] ?? '[Insert the start date here]';
    $end_date = $_POST['end_date'] ?? '[Insert the end date here]';

    
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

        if (!in_array($currentStatus, ['In Review', 'Interview'])) {
            echo "❌ You must review the student's resume before making an offer.";
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
        $state = $row['Int_State'];
        $city = $row['Int_City'];


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
            $mail->Subject = "Internship Offer from $company";
            $mail->Body = "
                Dear $studentName,<br><br>
                We are pleased to inform you that you have been selected for an <strong>INTERNSHIP OPPORTUNITY</strong> with <strong>$company</strong>.<br><br>
                <strong>Offer Details:</strong><br>
                - Company: $company<br>
                - Position: $position<br>
                - Start Date : $start_date<br>
                - End Date : $end_date<br>
                - Location: $city, $state<br><br><br><br><br>
                Please confirm your acceptance of this offer by replying to this email as soon as possible.<br><br>
                We look forward to welcoming you to our team.<br><br>
                Best regards,<br>
                $company Recruitment Team
            ";


            // Send the email
            $mail->send();
            echo "✅ Internship offer email sent to $email.";

            // Update application status in database
            $stmt = $conn->prepare("UPDATE student_application SET App_Status = 'Offered' WHERE ApplicationID = ?");
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
