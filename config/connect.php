<?php
$servername = "localhost";
$username = "intechship";
$password = "1234";
$dbname = "student_intechship";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";

?>