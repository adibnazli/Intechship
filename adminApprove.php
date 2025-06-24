<?php
include('config/config.php');


$result = $conn->query("SELECT * FROM student WHERE approve = 0 AND password IS NOT NULL");

echo "<h3>Students Awaiting Approval</h3>";
while ($row = $result->fetch_assoc()) {
    echo "{$row['Stud_Name']} - {$row['Email']}
          <a href='approve.php?id={$row['StudentID']}'>Approve</a><br>";
}
?>
