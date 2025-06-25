<?php
include("UserHeader.php");
include("config/config.php");

if (!isset($_GET['id'])) {
  echo "Invalid internship ID.";
  exit;
}

$internshipID = intval($_GET['id']);
$sql = "SELECT i.*, e.Comp_Name AS EmployerName 
        FROM intern_listings i 
        JOIN employer e ON i.EmployerID = e.EmployerID 
        WHERE i.InternshipID = $internshipID";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  $row = $result->fetch_assoc();
} else {
  echo "Internship not found.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($row['Int_Position']) ?> - InTechShip</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
    }
    .detail-container {
      background: #fff;
      max-width: 800px;
      margin: auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      margin-top: 0;
    }
    .info {
      margin: 10px 0;
    }
    .info strong {
      display: inline-block;
      width: 150px;
    }
    .button-group {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
    }
    .btn {
      padding: 10px 30px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      border: none;
      color: black;
    }
    .btn-back {
      background-color: #ffdc00;
    }
    .btn-apply {
      background-color: #ffdc00;
    }
  </style>
</head>
<body>

<div class="detail-container">
  <h2><?= htmlspecialchars($row['Int_Position']) ?></h2>
  <p class="info"><strong>Company:</strong> <?= htmlspecialchars($row['EmployerName']) ?></p>
  <p class="info"><strong>Program Type:</strong> <?= htmlspecialchars($row['Int_Qualification']) ?></p>
  <p class="info"><strong>Programme:</strong> <?= htmlspecialchars($row['Int_Programme']) ?></p>
  <p class="info"><strong>Location:</strong> <?= htmlspecialchars($row['Int_City']) ?>, <?= htmlspecialchars($row['Int_State']) ?></p>
  <p class="info"><strong>Allowance:</strong> RM <?= number_format($row['Int_Allowance'], 2) ?></p>
  <p class="info"><strong>Posted At:</strong> <?= htmlspecialchars($row['PostedAt']) ?></p>
  <p class="info"><strong>Description:</strong></p>
  <p><?= nl2br(htmlspecialchars($row['Int_Details'])) ?></p>

  <div class="button-group">
    <form action="InternshipSearch.php" method="get">
      <button type="submit" class="btn btn-back">Back</button>
    </form>

    <form method="POST" action="apply_submit.php" onsubmit="return confirmApplication();">
      <input type="hidden" name="internship_id" value="<?= htmlspecialchars($row['InternshipID']) ?>">
      <button type="submit" class="btn btn-apply">Apply</button>
  </form>
  </div>
</div>

<script>
  function confirmApplication() {
    return confirm("Are you sure you want to apply for this internship?");
  }
</script>

</body>
</html>
