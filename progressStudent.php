<?php
session_start();
include 'config/config.php';
include 'UserHeader.php';

if (!isset($_SESSION['studentID'])) {
    echo "Student not logged in.";
    exit;
}
$studentID = $_SESSION['studentID'];

$sql = "
SELECT 
    sa.ApplicationID,
    sa.App_Date,
    sa.App_Status,
    il.Int_Position,
    il.Int_City,
    il.Int_State
FROM 
    student_application sa
JOIN 
    intern_listings il ON sa.InternshipID = il.InternshipID
WHERE 
    sa.StudentID = ?
ORDER BY 
    sa.App_Date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Student Progress</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        .blue { color: blue; }
        .yellow { color: orange; }
        .orange { color: darkorange; }
        .green { color: green; }
        .red { color: red; }

        button {
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .accept-btn { background-color: green; color: white; }
        .reject-btn { background-color: red; color: white; }
    </style>
</head>
<body>

<h2 style="text-align: center;">Student Application Progress</h2>

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
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $status = $row['App_Status'];
                $colorClass = match($status) {
                    'Pending' => 'blue',
                    'In Review' => 'yellow',  //kena setting semula warna
                    'Offered' => 'green',
                    'Accepted' => 'green',
                    'Declined', 'Rejected' => 'red',
                    default => ''
                };

                echo "<td>{$row['Int_Position']}</td>";
                echo "<td>{$row['App_Date']}</td>";
                echo "<td>{$row['Int_City']}, {$row['Int_State']}</td>";
                echo "<td class='{$colorClass}'>{$status}</td>";

                if ($status === 'Offered') { //part ni ada edit lagi
                    echo "<td>
                        <form method='POST' action='respondOffer.php'>
                            <input type='hidden' name='applicationID' value='{$row['ApplicationID']}'> 
                            <button type='submit' name='response' value='Accepted' class='accept-btn'>Accept Offer</button>
                            <button type='submit' name='response' value='Declined' class='reject-btn'>Reject Offer</button>
                        </form>
                    </td>";
                } elseif ($status === 'Accepted') {
                    echo "<td class='green'>You accepted the offer </td>";
                } elseif ($status === 'Declined') {
                    echo "<td class='red'>You rejected the offer </td>";
                } else {
                    echo "<td>–</td>";
                }

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No applications found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>