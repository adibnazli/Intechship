<?php
session_start();
include ("AdminHeader.php");

if (isset($_SESSION['company_registered'])) {
    echo "<script>alert('Company registered successfully!');</script>";
    unset($_SESSION['company_registered']);
}

$PicID = $_SESSION['PicID'];

$sql = "SELECT EmployerID, Comp_Name, Address, Email, Comp_RegistrationNo 
        FROM employer 
        WHERE PicID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $PicID);
$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Company Registration</title>
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 900px;
      margin: 30px auto;
      background-color:rgb(238, 237, 237);
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .tab-header {
        position: relative;
        margin-bottom: 20px;
    }

    .tab-header::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        width: 100%;
        background-color: #ccc; /* Grey full-width line */
        z-index: 0;
    }

    .tab-header h2 {
        display: inline-block;
        position: relative;
        z-index: 1;
        border-bottom: 3px solid black; /* Black underline */
        margin: 0;
        padding-bottom: 3px;
    }

    .section-title {
      font-weight: bold;
      margin-top: 30px;
      margin-bottom: 10px;
    }
    form {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }
    form input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .full-width {
      grid-column: span 2;
    }
    .button-group {
      grid-column: span 2;
      text-align: right;
    }
    .button-group button {
      padding: 10px 20px;
      margin-left: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }
    .cancel-btn {
      background-color: #d6d6d6;
    }
    .register-btn {
      background-color: #fdd835;
    }
    table {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
    }
    table, th, td {
      border: 1px solid #ddd;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }

    .company-table {
        background-color: #ffffff;
    }

    .action-buttons {
    display: flex;
    flex-direction: column;
    gap: 5px;
    }

    .action-buttons a {
    display: block;
    width: 100%;
    padding: 8px 2px;
    border-radius: 3px;
    text-align: center;
    text-decoration: none;
    font-size: 14px;
    }


    .edit-btn {
    background-color: #ffeb3b;
    color: black;
    }

    .remove-btn {
    background-color: #f44336;
    color: white;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="tab-header">
    <h2>Company Registration</h2>
    </div>

    <div class="section-title">GENERALS</div>
    <form method="post" action="register_company.php">
        <input type="text" name="company_name" id="company_name" placeholder="Company Name" required>
        <input type="text" name="company_registration" id="company_registration" placeholder="Company Registration No" required>
        <input type="text" name="company_address" id="company_address" placeholder="Address" class="full-width" required>
        <input type="text" name="company_email" id="company_email" placeholder="Email"required>
        <input type="password" name="company_pass" id="company_pass" placeholder="Password" required>
        <div class="button-group">
            <button type="reset" class="cancel-btn" onclick="return confirmCancel()">Cancel</button>
            <button type="submit" class="register-btn" onclick="return confirmRegister()">Register</button>
        </div>
    </form>

    <div class="tab-header">
        <h2></h2>
    </div>
    <div class="section-title">Company Info Section</div>
    <table class="company-table">
    <thead>
        <tr>
        <th>No</th>
        <th>Name</th>
        <th>Address</th>
        <th>Email</th>
        <th>Registration No</th>
        <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $counter = 1; ?>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($row['Comp_Name']) ?></td>
                <td><?= htmlspecialchars($row['Address']) ?></td>
                <td><?= htmlspecialchars($row['Email']) ?></td>
                <td><?= htmlspecialchars($row['Comp_RegistrationNo']) ?></td>
                <td class="action-buttons">
                    <a class="edit-btn" href="editcompany.php?id=<?= $row['EmployerID'] ?>">Edit</a>
                    <a class="remove-btn" href="removecompany.php?id=<?= $row['EmployerID'] ?>" onclick="return confirm('Are you sure you want to remove this company permanently?');">Remove</a>
                </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                <td colspan="6" style="text-align:center;">No companies registered.</td>
                </tr>
                <?php endif; ?>
    </tbody>
    </table>
</div>

<script>
    function confirmCancel() {
      return confirm("Are you sure you want to cancel? All entered data will be lost.");
    }
    function confirmRegister() {
      return confirm("Are you sure you want to register this company?");
    }
</script>
</body>
</html>
