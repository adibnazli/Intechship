<?php
include("employerheader.php");
include("config/config.php");

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$internshipID = $_GET['id'];
$sql = "SELECT * FROM intern_listings WHERE InternshipID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $internshipID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Job not found.";
    exit;
}

$row = $result->fetch_assoc();
$selectedProgrammes = explode(',', $row['Int_Programme']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Job</title>
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
    select,
    textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 4px;
      background-color: #f2f2f2;
      box-sizing: border-box;
    }

    textarea {
      height: 150px;
    }

    .form-row {
      display: flex;
      gap: 10px;
    }

    .form-row > div {
      flex: 1;
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

<h1>Edit Job Posting</h1>
<div class="container">
  <h3>Update the details and click Save.</h3>
  <form method="post" action="updatejob.php">
    <input type="hidden" name="internship_id" value="<?= $row['InternshipID'] ?>">

    <label for="job_title">Job Title</label>
    <input type="text" name="job_title" id="job_title" value="<?= htmlspecialchars($row['Int_Position']) ?>">

    <div class="form-row">
      <div>
        <label for="state">Location</label>
        <select name="state" id="state">
          <option value="">-- Select a State --</option>
          <?php
          $states = ["Sabah", "Sarawak", "Perlis", "Pahang", "Perak", "Pulau Pinang", "Terengganu", "Kedah", "Kelantan", "Negeri Sembilan", "Melaka", "Johor", "Selangor"];
          foreach ($states as $state) {
              $selected = $row['Int_State'] == $state ? 'selected' : '';
              echo "<option value=\"$state\" $selected>$state</option>";
          }
          ?>
        </select>
      </div>
      <div>
        <label for="city">&nbsp;</label>
        <input type="text" name="city" id="city" value="<?= htmlspecialchars($row['Int_City']) ?>">
      </div>
    </div>

    <label for="programme">Programme</label>
    <div style="border: 1px solid #ccc; border-radius: 8px; padding: 15px; background-color: #f2f2f2; margin-bottom: 20px;">
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
        <?php
        $programmes = ["Computer Science", "Game Technology", "Computer Security", "Computer Networking", "Software Development", "Database Management", "Interactive Media", "Artificial Intelligence", "Cloud Computing"];
        foreach ($programmes as $programme) {
            $checked = in_array($programme, $selectedProgrammes) ? 'checked' : '';
            echo "<label><input type=\"checkbox\" name=\"programme[]\" value=\"$programme\" $checked> <span style=\"font-weight: normal;\">$programme</span></label>";
        }
        ?>
      </div>
    </div>

    <label for="allowance">Monthly Allowance (MYR)</label>
    <input type="text" name="allowance" id="allowance" value="<?= htmlspecialchars($row['Int_Allowance']) ?>">

    <label for="job_details">Job Details</label>
    <textarea name="job_details" id="job_details"><?= htmlspecialchars($row['Int_Details']) ?></textarea>

    <div class="button-group">
      <button type="button" class="btn-cancel" onclick="window.location.href='PostListing.php'">Cancel</button>
      <button type="submit" class="btn-submit">Save</button>
    </div>

  </form>
</div>
</body>
</html>
<?php $conn->close(); ?>
