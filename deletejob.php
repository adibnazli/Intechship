<?php
include('config/connect.php');

if (isset($_GET['id'])) {
    $jobID = intval($_GET['id']);

    $sql = "DELETE FROM intern_listings WHERE InternshipID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $jobID);

    if ($stmt->execute()) {
        header("Location: PostListing.php?deleted=1");
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
