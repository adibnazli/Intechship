<?php
session_start();
include("AdminHeader.php");
include('config/connect.php');

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$companyID = $_GET['id'];
$sql = "SELECT * FROM employer WHERE EmployerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $companyID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Company not found.";
    exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Company</title>
  <style>
    body {
      font-family: Roboto, sans-serif;
      background-color: #f6f6f6;
      margin: 0;
      padding: 0;
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
    }

    .container {
      max-width: 700px;
      margin: 50px auto;
      background-color: #ffffff;
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h3 {
      text-align: center;
      margin-bottom: 30px;
    }

    form label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }

    input[type="text"],
    input[type="email"],
    textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 4px;
      background-color: #f2f2f2;
      box-sizing: border-box;
    }

    .button-group {
      display: flex;
      justify-content: center;
      gap: 20px;
    }

    .btn-cancel {
      background-color: #d9d9d9;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }

    .btn-submit {
      background-color: #ffd700;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<h1>Edit Company Details</h1>
<div class="container">
  <h3>Update the company details</h3>
  <form method="post" action="updatecompany.php">
    <input type="hidden" name="employer_id" value="<?= $row['EmployerID'] ?>">

    <label for="company_name">Company Name</label>
    <input type="text" name="company_name" id="company_name" value="<?= htmlspecialchars($row['Comp_Name']) ?>" required>

    <label for="company_registration">Registration No</label>
    <input type="text" name="company_registration" id="company_registration" value="<?= htmlspecialchars($row['Comp_RegistrationNo']) ?>" required>

    <label for="company_address">Address</label>
    <input type="text" name="company_address" id="company_address" value="<?= htmlspecialchars($row['Address']) ?>" required>

    <label for="company_email">Email</label>
    <input type="email" name="company_email" id="company_email" value="<?= htmlspecialchars($row['Email']) ?>" required>

    <div class="button-group">
      <button type="button" class="btn-cancel" onclick="window.location.href='company_registration.php'">Cancel</button>
      <button type="submit" class="btn-submit">Save</button>
    </div>

  </form>
</div>
<?php include("footer.php"); ?>
</body>
</html>
<?php $conn->close(); ?>
