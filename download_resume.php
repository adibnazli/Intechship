<?php
include("config/config.php");

if (isset($_GET['appid'])) {
    $appId = $_GET['appid'];

    // Update App_Status to 'In Review'
    $update = "UPDATE student_application SET App_Status = 'In Review' WHERE ApplicationID = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("i", $appId);
    $stmt->execute();

    // Fetch the resume path
    $sql = "SELECT student.Stud_ResumePath 
            FROM student_application
            JOIN student ON student_application.StudentID = student.StudentID
            WHERE student_application.ApplicationID = ?
            ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $file = $row['Stud_ResumePath'];
        if (file_exists($file)) {
            // Force file download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        } 
        else {
            echo "Resume file not found.";
        }
    }
}
$conn->close();
?>
