<?php
session_start();
if (!isset($_SESSION['Comp_Name'])) {
  header("Location: login.html");
  exit();
}

include("employerheader.php");
include('config/connect.php');

if (!isset($_GET['id'])) {
  echo "<div style='padding:40px;text-align:center;'>Student ID is missing.</div>";
  include("footer.php");
  exit();
}

$studentID = intval($_GET['id']);

$sql = "SELECT Stud_Name, Stud_Programme, Email, Stud_Phone, Stud_Skills, Preferred_Allowance, Pref_Location, Allowance_Type, Stud_ResumePath
        FROM student WHERE StudentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
  echo "<div style='padding:40px;text-align:center;'>Student not found.</div>";
  include("footer.php");
  exit();
}
$row = $result->fetch_assoc();

function neatValue($val) {
  return htmlspecialchars(trim(str_replace('×', '', $val)));
}
?>
<html>
<head>
  <title>Student Profile</title>
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f9f9f9;
      margin: 0;
    }
    .profile-container {
      max-width: 700px;
      margin: 40px auto;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.14);
      padding: 42px 50px 48px 50px;
    }
    .profile-title {
      text-align: center;
      margin-bottom: 38px;
      font-size: 2.1rem;
      color: orange;
      font-weight: 500;
      border: 2.5px solid orange;
      border-radius: 18px;
      padding: 18px 0 16px 0;
      background: #fff7eb;
      letter-spacing: 0.2px;
      box-shadow: 0 1px 6px rgba(255, 140, 0, 0.09);
    }
    .profile-section {
      margin-bottom: 28px;
    }
    .profile-label {
      font-weight: bold;
      color: #666;
      font-size: 1.08rem;
      display: block;
      margin-bottom: 6px;
      letter-spacing: 0.2px;
    }
    .profile-value {
      display: inline-block;
      font-size: 1.1rem;
      color: #222;
      background: #f6f6f6;
      border-radius: 8px;
      padding: 10px 15px;
      margin-bottom: 5px;
      margin-right: 6px;
      box-sizing: border-box;
    }
    .download-resume-link {
      display: inline-block;
      padding: 10px 22px;
      background: orange;
      color: #fff !important;
      border-radius: 7px;
      font-weight: bold;
      text-decoration: none;
      font-size: 1.05rem;
      margin-top: 3px;
      transition: background 0.15s;
    }
    .download-resume-link:hover {
      background: #ef8800;
    }
    .back-btn {
      display: inline-block;
      margin-top: 36px;
      background: #fff;
      color: #333;
      padding: 11px 35px;
      border-radius: 24px;
      text-decoration: none;
      border: 1px solid #ccc;
      font-size: 1.08rem;
      transition: background 0.18s;
      font-weight: 500;
      letter-spacing: 0.1px;
      cursor: pointer;
    }
    .back-btn:hover {
      background: #f0f0f0;
    }
  </style>
</head>
<body>
  <div class="profile-container">
    <div class="profile-title">
      <?= neatValue($row['Stud_Name']) ?>'s Profile
    </div>

    <?php if (!empty($row['Stud_Programme'])): ?>
    <div class="profile-section">
      <span class="profile-label">Programme:</span>
      <span class="profile-value"><?= neatValue($row['Stud_Programme']) ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($row['Email'])): ?>
    <div class="profile-section">
      <span class="profile-label">Email:</span>
      <span class="profile-value"><?= neatValue($row['Email']) ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($row['Stud_Phone'])): ?>
    <div class="profile-section">
      <span class="profile-label">Phone:</span>
      <span class="profile-value"><?= neatValue($row['Stud_Phone']) ?></span>
    </div>
    <?php endif; ?>

    <?php
    // SKILLS SECTION
    if (!empty($row['Stud_Skills'])) {
      $skills = array_filter(array_map('trim', preg_split('/,|\n/', $row['Stud_Skills'])));
    }
    ?>
    <?php if (!empty($skills)): ?>
    <div class="profile-section">
      <span class="profile-label">Skills:</span>
      <?php foreach($skills as $skill): ?>
        <span class="profile-value"><?= neatValue($skill) ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php
    // PREFERRED ALLOWANCE SECTION
    $showAllowance = !empty($row['Preferred_Allowance']);
    ?>

    <?php if ($showAllowance): ?>
    <div class="profile-section">
      <span class="profile-label">Preferred Allowance:</span>
      <span class="profile-value">
        <?= neatValue($row['Preferred_Allowance']) ?>
        <?php if (!empty($row['Allowance_Type'])): ?>
          (<?= neatValue($row['Allowance_Type']) ?>)
        <?php endif; ?>
      </span>
    </div>
    <?php endif; ?>

    <?php
    // PREFERRED LOCATION SECTION
    if (!empty($row['Pref_Location'])) {
      $locs = array_filter(array_map('trim', preg_split('/,|\n/', $row['Pref_Location'])));
    }
    ?>
    <?php if (!empty($locs)): ?>
    <div class="profile-section">
      <span class="profile-label">Preferred Location:</span>
      <?php foreach($locs as $loc): ?>
        <span class="profile-value"><?= neatValue($loc) ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($row['Stud_ResumePath'])): ?>
      <div class="profile-section">
        <span class="profile-label">Resume:</span>
        <a class="download-resume-link" href="<?= neatValue($row['Stud_ResumePath']) ?>" target="_blank">View Resume</a>
      </div>
    <?php endif; ?>

    <a href="ApplicationManagement.php" class="back-btn">&larr; Back to Application Management</a>
  </div>
  <?php include("footer.php"); ?>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>