<?php
session_start();
include('config/connect.php');

if (!isset($_SESSION['studentID'])) {
    header('Location: login.html');
    exit;
}

include 'UserHeader.php';

if (isset($_SESSION['response_status'])) {
    $msg = "You've successfully {$_SESSION['response_status']} the offer!";
    if (isset($_SESSION['email_error'])) $msg .= " (Email notification failed)";
    echo "<script>alert('$msg');</script>";
    unset($_SESSION['response_status'], $_SESSION['email_error']);
}

$studentID     = $_SESSION['studentID'];
$selectedAppID = $_GET['app'] ?? null;

$q = $conn->prepare(
    "SELECT sa.ApplicationID, sa.App_Date, sa.App_Status,
            il.Int_Position, il.Int_City, il.Int_State
     FROM student_application sa
     JOIN intern_listings il ON sa.InternshipID = il.InternshipID
     WHERE sa.StudentID = ?
     ORDER BY sa.App_Date DESC"
);
$q->bind_param('i', $studentID);
$q->execute();
$rows = $q->get_result()->fetch_all(MYSQLI_ASSOC);

function stage($status, $stage)
{
    $order = ['Pending', 'In Review', 'Interview', 'Offered', 'Accepted', 'Declined', 'Rejected'];
    return array_search($status, $order) >= array_search($stage, $order) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Application Progress</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Roboto, sans-serif;
            background: #f6f6f6;
            margin: 0
        }

        .progress-container {
            width: 700px;
            margin: 30px auto;
            text-align: center;
            border: 2px solid #ccc;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
            padding: 20px
        }

        .progress-steps {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 20px
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #333
        }

        .step i {
            font-size: 28px;
            margin-bottom: 5px
        }

        .step.active i,
        .step.active p {
            color: #ffb800;
            font-weight: bold
        }

        .table-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1)
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            padding: 14px;
            text-align: left
        }

        th {
            color: #666;
            border-bottom: 2px solid #ccc;
            padding-left: 30px
        }

        tr:not(:last-child) {
            border-bottom: 1px solid #ddd
        }

        .status-pill {
            font-size: 13px;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: bold;
            display: inline-block;
            color: #000
        }

        .status-pending {
            background: #56ebff
        }

        .status-review {
            background: #ffd900
        }

        .status-interview {
            background: #ffb109
        }

        .status-offered {
            background: #5fff5f
        }

        .status-accepted {
            background: #27ae60
        }

        .status-declined {
            background: #f66
        }

        .accept-btn {
            background: green;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer
        }

        .reject-btn {
            background: red;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer
        }

        tbody tr {
            cursor: pointer
        }

        tbody tr:hover {
            background: #f1f1f1
        }
    </style>
</head>

<body>
    <?php
    if ($selectedAppID) {
        $d = $conn->prepare(
            "SELECT sa.App_Date, sa.App_Status, il.Int_Position
         FROM student_application sa
         JOIN intern_listings il ON sa.InternshipID = il.InternshipID
         WHERE sa.ApplicationID = ? AND sa.StudentID = ?"
        );
        $d->bind_param('ii', $selectedAppID, $studentID);
        $d->execute();
        if ($r = $d->get_result()->fetch_assoc()) {
            $s = $r['App_Status'];
            echo "<div class='progress-container'><h2>" . htmlspecialchars($r['Int_Position']) . " Application Progress</h2>
              <h4>Date: " . htmlspecialchars($r['App_Date']) . "</h4><div class='progress-steps'>";
            $steps = [
                'Pending'   => ['bx bx-send', 'Application'],
                'In Review' => ['bx bx-time', 'In Review'],
                'Interview' => ['bx bx-calendar', 'Interview'],
                'Offered'   => ['bx bx-file', 'Offer'],
                'Accepted'  => ['bx bx-check', 'Completed'],
                'Declined'  => ['bx bx-x', 'Declined'],
                'Rejected'  => ['bx bx-x', 'Declined']
            ];
            foreach ($steps as $key => $info) {
                if (!in_array($key, ['Accepted', 'Declined', 'Rejected']) || $s == $key || stage($s, $key) == 'active') {
                    echo "<div class='step " . stage($s, $key) . "'><i class='{$info[0]}'></i><p>{$info[1]}</p></div>";
                }
            }
            echo "</div></div>";
        } else {
            echo "<div class='progress-container'><h3>Application not found.</h3></div>";
        }
    } else {
        echo "<div class='progress-container'><h3>Select an application to view progress.</h3></div>";
    }
    ?>
    <h2 style="text-align:center;margin-top:50px">All Applications</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Offer</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($rows) {
                    foreach ($rows as $r) {
                        $s = $r['App_Status'];
                        switch ($s) {
                            case 'Pending':
                                $cls = 'status-pill status-pending';
                                break;
                            case 'In Review':
                                $cls = 'status-pill status-review';
                                break;
                            case 'Interview':
                                $cls = 'status-pill status-interview';
                                break;
                            case 'Offered':
                                $cls = 'status-pill status-offered';
                                break;
                            case 'Accepted':
                                $cls = 'status-pill status-accepted';
                                break;
                            case 'Declined':
                            case 'Rejected':
                                $cls = 'status-pill status-declined';
                                break;
                            default:
                                $cls = 'status-pill';
                        }
                        echo "<tr onclick=\"window.location='progressStudent.php?app={$r['ApplicationID']}'\">
              <td>" . htmlspecialchars($r['Int_Position']) . "</td>
              <td>" . htmlspecialchars($r['App_Date']) . "</td>
              <td>" . htmlspecialchars($r['Int_City']) . ", " . htmlspecialchars($r['Int_State']) . "</td>
              <td><span class='$cls'>$s</span></td><td style='text-align:center'>";
                        if ($s === 'Offered') {
                            echo "<form method='POST' action='respondOffer.php'>
                  <input type='hidden' name='applicationID' value='{$r['ApplicationID']}'>
                  <button type='submit' name='response' value='Accepted' class='accept-btn'>Accept</button>
                  <button type='submit' name='response' value='Declined' class='reject-btn'>Reject</button>
                  </form>";
                        } elseif ($s === 'Accepted') {
                            echo "<span class='status-pill status-accepted'>Accepted</span>";
                        } elseif ($s === 'Declined' || $s === 'Rejected') {
                            echo "<span class='status-pill status-declined'>Declined</span>";
                        } else echo '–';
                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center'>No applications found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>