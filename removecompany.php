<?php
include('config/connect.php');

if (isset($_GET['id'])) {
    $EmployerID = intval($_GET['id']);

    $sql = "DELETE FROM employer WHERE EmployerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $EmployerID);

    if ($stmt->execute()) {
        header("Location: company_registration.php?deleted=1");
        exit();
    } else {
        echo "Error deleting job: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
