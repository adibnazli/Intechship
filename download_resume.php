<?php
include('config/connect.php');

if (isset($_GET['appid'])) {
    $appId = (int) $_GET['appid'];

    // Step 1: Check current App_Status first
    $statusCheck = $conn->prepare("SELECT student_application.App_Status, student.Stud_ResumePath
                                   FROM student_application
                                   JOIN student ON student_application.StudentID = student.StudentID
                                   WHERE student_application.ApplicationID = ?");
    $statusCheck->bind_param("i", $appId);
    $statusCheck->execute();
    $result = $statusCheck->get_result();

    if ($row = $result->fetch_assoc()) {
        $currentStatus = $row['App_Status'];
        $file = $row['Stud_ResumePath'];

        // Step 2: Only update to 'In Review' if still 'Pending'
        if ($currentStatus === 'Pending') {
            $update = $conn->prepare("UPDATE student_application SET App_Status = 'In Review' WHERE ApplicationID = ?");
            $update->bind_param("i", $appId);
            $update->execute();
        }

        // Step 3: Proceed to download file
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        } else {
            echo "Resume file not found.";
        }
    } else {
        echo "Invalid application ID.";
    }
}

$conn->close();
?>
