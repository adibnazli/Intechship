[file name]: respondOffer.php
[file content begin]
<?php
session_start();
include("config/config.php");

// Check if student is logged in
if (!isset($_SESSION['studentID'])) {
    die("Access denied.");
}

// Include PHPMailer
require 'uploads/phpmailer/PHPMailer.php';
require 'uploads/phpmailer/SMTP.php';
require 'uploads/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationID = $_POST['applicationID'] ?? 0;
    $response = $_POST['response'] ?? '';

    // Validate input
    if (!$applicationID || !in_array($response, ['Accepted', 'Declined'])) {
        die("Invalid request.");
    }

    // Get student and application details with proper column names
    $sql = "SELECT 
                sa.ApplicationID, 
                sa.App_Status,
                s.Stud_Name, 
                s.Email AS Stud_Email,
                il.Int_Position,
                e.Comp_Name,
                e.Email AS Comp_Email
            FROM student_application sa
            JOIN student s ON sa.StudentID = s.StudentID
            JOIN intern_listings il ON sa.InternshipID = il.InternshipID
            JOIN employer e ON il.EmployerID = e.EmployerID
            WHERE sa.ApplicationID = ? AND sa.StudentID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $applicationID, $_SESSION['studentID']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Check if status is 'Offered'
        if ($row['App_Status'] !== 'Offered') {
            echo "<script>alert('This offer is no longer available.'); window.history.back();</script>";
            exit;
        }

        // Update application status
        $updateStmt = $conn->prepare("UPDATE student_application SET App_Status = ? WHERE ApplicationID = ?");
        $updateStmt->bind_param("si", $response, $applicationID);
        $updateStmt->execute();
        
        // Send email to EMPLOYER (not student)
        $mail = new PHPMailer(true);
        
        try {
            // Mailtrap configuration
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'e219ddf02b800a';
            $mail->Password = '690b2325ea3c33';
            $mail->Port = 2525;
            
            // Email content - SENT TO EMPLOYER
            $mail->setFrom('no-reply@intechship.com', 'InTechShip System');
            $mail->addAddress($row['Comp_Email'], $row['Comp_Name']); // Employer's email
            
            if ($response === 'Accepted') {
                $mail->Subject = "Offer Accepted - {$row['Int_Position']}";
                $mail->Body = "
                    <h2>Offer Accepted</h2>
                    <p>Dear {$row['Comp_Name']} Team,</p>
                    <p>We are pleased to inform you that <strong>{$row['Stud_Name']}</strong> has accepted your internship offer for the position of <strong>{$row['Int_Position']}</strong>.</p>
                    <p><strong>Student Contact:</strong> {$row['Stud_Email']}</p>
                    <p>Please contact the student to finalize the internship arrangements.</p>
                    <p>Best regards,<br>InTechShip Team</p>
                ";
            } else {
                $mail->Subject = "Offer Declined - {$row['Int_Position']}";
                $mail->Body = "
                    <h2>Offer Declined</h2>
                    <p>Dear {$row['Comp_Name']} Team,</p>
                    <p>We regret to inform you that <strong>{$row['Stud_Name']}</strong> has declined your internship offer for the position of <strong>{$row['Int_Position']}</strong>.</p>
                    <p>You may wish to consider other candidates for this position.</p>
                    <p>Best regards,<br>InTechShip Team</p>
                ";
            }
            
            $mail->isHTML(true);
            $mail->send();
            
            // Redirect with success message
            $_SESSION['response_status'] = $response;
            header("Location: progressStudent.php?app=$applicationID");
            exit;
            
        } catch (Exception $e) {
            // Email failed but status was updated - log error
            error_log("Email send failed: " . $mail->ErrorInfo);
            $_SESSION['response_status'] = $response;
            $_SESSION['email_error'] = true;
            header("Location: progressStudent.php?app=$applicationID");
            exit;
        }
        
    } else {
        echo "<script>alert('Application not found.'); window.history.back();</script>";
    }
} else {
    header("Location: progressStudent.php");
}
?>
[file content end]