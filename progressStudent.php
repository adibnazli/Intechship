<?php
session_start();
include 'config/config.php';

//kalau tak log in tak boleh masuk
if (!isset($_SESSION['studentID'])) {
    echo "Student not logged in.";
    header("Location: login.html");
    exit;
}

include 'UserHeader.php';
// progressStudent.php - Add after session_start()
if (isset($_SESSION['response_status'])) {
    $status = $_SESSION['response_status'];
    $message = "You've successfully $status the offer!";

    if (isset($_SESSION['email_error'])) {
        $message .= " (Email notification failed)";
    }

    echo "<script>alert('$message');</script>";

    unset($_SESSION['response_status']);
    unset($_SESSION['email_error']);
}
$studentID = $_SESSION['studentID'];
$selectedAppID = $_GET['app'] ?? null;

//ambik maklumat application
$sql = "SELECT sa.ApplicationID, sa.App_Date, sa.App_Status, il.Int_Position, il.Int_City, il.Int_State 
        FROM student_application sa 
        JOIN intern_listings il ON sa.InternshipID = il.InternshipID 
        WHERE sa.StudentID = ? 
        ORDER BY sa.App_Date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

$allRows = [];
while ($row = $result->fetch_assoc()) {
    $allRows[] = $row;
}

function getCurrentStageClass($status, $stageName) {
    $stageOrder = ['Pending', 'In Review', 'Interview', 'Offered', 'Accepted'];
    $statusIndex = array_search($status, $stageOrder);
    $stageIndex = array_search($stageName, $stageOrder);
    return $statusIndex !== false && $stageIndex !== false && $statusIndex >= $stageIndex ? 'active' : '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Application Progress</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f6f6f6;
            margin: 0;
        }

        .progress-container {
            text-align: center;
            width: 700px;
            margin: 30px auto;
            border: 2px solid #ccc;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 20px;
        }

        .progress-steps {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            margin-top: 20px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #333;
        }

        .step i {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .step.active i, .step.active p {
            color: #ffb800;
            font-weight: bold;
        }

        .table-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 14px;
        }

        th {
            font-weight: normal;
            color: #666666; 
            border-bottom: 2px solid #ccc;
            padding-left: 30px;
        }

        td {
            vertical-align: middle;
        }

        tr:not(:last-child) {
            border-bottom: 1px solid #ddd;
        }

        .status-pill {
            font-size: 13px;
            display: inline-block;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: bold;
            color: black;
        }

        .status-pending { background-color: rgb(86, 235, 255); }
        .status-review { background-color: #FFD900; }
        .status-interview { background-color: rgb(255, 177, 9); }
        .status-offered { background-color: rgb(95, 255, 95); }
        .status-accepted { background-color: #27ae60; }
        .status-declined, .status-rejected { background-color: #f66; }

        .accept-btn {
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .reject-btn {
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .table-container tbody tr {
            cursor: pointer; /* shows pointer hand */
        }

        .table-container tbody tr:hover {
            background-color: #f1f1f1; /* adds hover effect */
        }

    </style>
</head>
<body>

<?php
if ($selectedAppID) {
    foreach ($allRows as $row) {
        if ($row['ApplicationID'] == $selectedAppID) {
            $status = $row['App_Status'];
            echo "<div class='progress-container'>";
            echo "<h2>" . htmlspecialchars($row['Int_Position']) . " Application Progress</h2>";
            echo "<h4>Date: " . htmlspecialchars($row['App_Date']) . "</h4>";
            echo "<div class='progress-steps'>";
            echo "<div class='step " . getCurrentStageClass($status, 'Pending') . "'><i class='bx bx-send'></i><p>Application</p></div>";
            echo "<div class='step " . getCurrentStageClass($status, 'In Review') . "'><i class='bx bx-time'></i><p>In Review</p></div>";
            echo "<div class='step " . getCurrentStageClass($status, 'Interview') . "'><i class='bx bx-calendar'></i><p>Interview</p></div>";
            echo "<div class='step " . getCurrentStageClass($status, 'Offered') . "'><i class='bx bx-file'></i><p>Offer</p></div>";
            echo "<div class='step " . getCurrentStageClass($status, 'Accepted') . "'><i class='bx bx-check'></i><p>Completed</p></div>";
            echo "</div></div>";
            break;
        }
    }
} else {
    echo "<div class='progress-container'><h3>No application selected. Click on a row to view progress.</h3></div>";
}
?>

<h2 style="text-align: center; margin-top: 50px;">All Applications</h2>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Position</th>
                <th>Date</th>
                <th>Location</th>
                <th>Status</th>
                <th>Internship Offer</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($allRows) > 0) {
                foreach ($allRows as $row) {
                    $status = $row['App_Status'];
                    $statusClass = match($status) {
                        'Pending' => 'status-pill status-pending',
                        'In Review' => 'status-pill status-review',
                        'Interview' => 'status-pill status-interview',
                        'Offered' => 'status-pill status-offered',
                        'Accepted' => 'status-pill status-accepted',
                        'Declined', 'Rejected' => 'status-pill status-declined',
                        default => 'status-pill'
                    };

                    echo "<tr onclick=\"window.location='progressStudent.php?app={$row['ApplicationID']}'\">";
                    echo "<td>" . htmlspecialchars($row['Int_Position']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['App_Date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Int_City']) . ", " . htmlspecialchars($row['Int_State']) . "</td>";
                    echo "<td><span class='{$statusClass}'>{$status}</span></td>";

                    echo "<td style='text-align: center;'>";
                    if ($status === 'Offered') {
                        echo "<form method='POST' action='respondOffer.php'>
                                <input type='hidden' name='applicationID' value='{$row['ApplicationID']}'>
                                <button type='submit' name='response' value='Accepted' class='accept-btn'>Accept</button>
                                <button type='submit' name='response' value='Declined' class='reject-btn'>Reject</button>
                              </form>";
                    } elseif ($status === 'Accepted') {
                        echo "<span class='status-pill status-accepted'>Accepted</span>";
                    } elseif ($status === 'Declined' || $status === 'Rejected') {
                        echo "<span class='status-pill status-declined'>Declined</span>";
                    } else {
                        echo "<span>–</span>";
                    }
                    echo "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No applications found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<?php include("footer.php"); ?>
</body>
</html>
