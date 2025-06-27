<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile - InTechShip</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f9f9f9;
    }

    .content {
      width: 1300px;
      min-height: 650px;
      margin: 0 auto;
      background-color: rgb(238, 237, 237);
      display: flex;
      gap: 40px;
      padding: 70px 20px;
    }

    .box {
      background-color: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
      flex: 1;
    }

    .box h3, .box h4 {
      margin-bottom: 15px;
      font-size: 20px;
      color: #333;
    }

    .upload-section label {
      margin-bottom: 10px;
      color: #444;
    }

    .upload-section input[type="file"] {
      display: none;
    }

    .resume-box {
      padding: 10px;
      border-radius: 12px;
      text-align: left;
      margin-top: 10px;
    }

    .resume-box .custom-upload {
      display: inline-block;
      background-color: #fff;
      color: #000;
      border: 2px solid #ffcc00;
      padding: 10px 25px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .upload-section .custom-upload {
      border: 2px solid #ffcc00;
      background: #fff;
      color: #000;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      text-align: center;
      display: inline-block;
    }

    .custom-upload:hover {
      background-color: #ffdc00;
      transform: translateY(-2px);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .input-group {
      display: flex;
      margin-top: 10px;
    }

    .input-group input {
      flex: 1;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      margin-right: 10px;
    }

    .add-skill-btn, .save-btn, .edit-btn {
      background-color: #ffdc00;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .add-skill-btn:hover {
      background-color:rgb(197, 175, 32);
      transform: translateY(-2px);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .save-btn:hover {
      background-color:rgb(197, 175, 32);
      transform: translateY(-2px);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .edit-btn:hover {
      background-color:rgb(197, 175, 32);
      transform: translateY(-2px);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .skills-list, .locations-list {
      margin-top: 20px;
      border: 1px solid #eee;
      border-radius: 10px;
      padding: 15px;
    }

    .tag {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 12px;
      background: #f5f5f5;
      border-radius: 6px;
      margin-bottom: 8px;
      font-size: 15px;
    }

    .tag button {
      background: none;
      border: none;
      font-size: 18px;
      color: #000;
      cursor: pointer;
    }

    .salary-section {
      display: flex;
      gap: 10px;
      margin-top: 15px;
    }

    .salary-section input,
    .salary-section select {
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .section-title {
      font-size: 20px;
      font-weight: bold;
      color: #333;
      margin-bottom: 20px;
      text-align: left;
    }

    .save-container {
      text-align: center;
      margin: 30px 0;
    }

    .save-container .save-btn {
      padding: 12px 40px;
      font-size: 16px;
    }
  </style>
</head>
<body>
<?php
session_start();
include("UserHeader.php");
include("config/config.php");

if (!isset($_SESSION['studentID'])) {
  die("Access denied.");
}

$studentID = $_SESSION['studentID'];

// Fetch student details
$sql = "SELECT * FROM student WHERE StudentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();
?>

<form action="save_profile.php" method="POST" enctype="multipart/form-data">
  <div class="content">
    <div class="box">
      <h3>Resume</h3>
      <div class="upload-section">
        <label>Upload your resume to create and strengthen your profile.</label>
        <div class="resume-box">
          <label for="resume" class="custom-upload">Upload</label>
          <input type="file" id="resume" name="resume" accept=".pdf">
        </div>
        <?php if (!empty($student['Stud_ResumePath'])):
          $filePath = $student['Stud_ResumePath'];
          $fileName = basename($filePath);
        ?>
        <p style="margin-top: 10px;">
          Uploaded: <a href="<?= $filePath ?>" target="_blank"><?= $fileName ?></a> |
          <a href="delete_resume.php" onclick="return confirm('Are you sure you want to delete your resume?');" style="color:red;">Delete</a>
        </p>
        <?php endif; ?>
      </div>

      <h3 style="margin-top: 30px;">Skills</h3>
      <p>Add your skills to make your profile more valuable.</p>
      <div class="input-group">
        <input type="text" id="skillInput" placeholder="e.g. Creative">
        <button class="add-skill-btn" type="button" onclick="addSkill()">Add Skills</button>
      </div>
      <div class="skills-list" id="skillsList">
        <?php
        $skills = explode(',', $student['Stud_Skills'] ?? '');
        $i = 1;
        foreach ($skills as $skill) {
          $trim = trim($skill);
          if ($trim) {
            echo "<div class='tag'>{$i}. {$trim} <button onclick='this.parentElement.remove(); renumberSkills();'>&times;</button></div>";
            $i++;
          }
        }
        ?>
      </div>
    </div>

    <!-- RIGHT BOX -->
    <div class="box">
      <div class="section-title">Job Preferences</div>

      <h4>Preferred Location</h4>
      <div class="input-group">
        <input type="text" id="locationInput">
        <button class="save-btn" type="button" onclick="addLocation()">Add Locations</button>
      </div>

      <div class="locations-list" id="locationsList">
        <?php
        $locations = explode(',', $student['Pref_Location'] ?? '');
        $i = 1;
        foreach ($locations as $loc) {
          $trim = trim($loc);
          if ($trim) {
            echo "<div class='tag'>{$i}. {$trim} <button onclick='this.parentElement.remove(); renumberLocations();'>&times;</button></div>";
            $i++;
          }
        }
        ?>
      </div>

      <h4 style="margin-top: 30px;">Preferred Allowance / Salary</h4>
      <div class="salary-section">
        <input type="number" name="allowance" placeholder="500" value="<?= $student['Preferred_Allowance'] ?? '' ?>">
        <select name="allowance_type">
          <option value="Monthly" <?= ($student['Allowance_Type'] ?? '') == 'Monthly' ? 'selected' : '' ?>>Monthly</option>
          <option value="Weekly" <?= ($student['Allowance_Type'] ?? '') == 'Weekly' ? 'selected' : '' ?>>Weekly</option>
        </select>
      </div>
    </div>
  </div>

  <div class="save-container">
    <button class="save-btn" type="submit">Save Profile</button>
  </div>

  <!-- Hidden fields -->
  <input type="hidden" name="skills" id="skillsHidden">
  <input type="hidden" name="locations" id="locationsHidden">
  <input type="hidden" name="matric_no" value="<?= $student['Matric_No'] ?? '' ?>">
</form>

<script>
  function addSkill() {
    const skillInput = document.getElementById('skillInput');
    const skillsList = document.getElementById('skillsList');
    if (skillInput.value.trim() !== '') {
      const count = skillsList.getElementsByClassName('tag').length + 1;
      const tag = document.createElement('div');
      tag.className = 'tag';
      tag.innerHTML = `${count}. ${skillInput.value} <button onclick="this.parentElement.remove(); renumberSkills();">&times;</button>`;
      skillsList.appendChild(tag);
      skillInput.value = '';
    }
  }

  function renumberSkills() {
    const tags = document.querySelectorAll('#skillsList .tag');
    tags.forEach((tag, i) => {
      const text = tag.firstChild.textContent.replace(/\d+\.\s/, '').trim();
      const button = tag.querySelector('button');
      tag.innerHTML = `${i + 1}. ${text}`;
      tag.appendChild(button);
    });
  }

  function addLocation() {
    const locationInput = document.getElementById('locationInput');
    const locationsList = document.getElementById('locationsList');
    if (locationInput.value.trim() !== '') {
      const count = locationsList.getElementsByClassName('tag').length + 1;
      const tag = document.createElement('div');
      tag.className = 'tag';
      tag.innerHTML = `${count}. ${locationInput.value} <button onclick="this.parentElement.remove(); renumberLocations();">&times;</button>`;
      locationsList.appendChild(tag);
      locationInput.value = '';
    }
  }

  function renumberLocations() {
    const tags = document.querySelectorAll('#locationsList .tag');
    tags.forEach((tag, i) => {
      const text = tag.firstChild.textContent.replace(/\d+\.\s/, '').trim();
      const button = tag.querySelector('button');
      tag.innerHTML = `${i + 1}. ${text}`;
      tag.appendChild(button);
    });
  }

  // Collect skills and locations before form submit
  document.querySelector("form").addEventListener("submit", function () {
    const skills = Array.from(document.querySelectorAll("#skillsList .tag"))
      .map(tag => {
        const textNode = tag.firstChild.textContent || '';
        return textNode.replace(/\d+\.\s/, '').trim();
      });
    document.getElementById("skillsHidden").value = skills.join(",");

    const locations = Array.from(document.querySelectorAll("#locationsList .tag"))
      .map(tag => {
        const textNode = tag.firstChild.textContent || '';
        return textNode.replace(/\d+\.\s/, '').trim();
      });
    document.getElementById("locationsHidden").value = locations.join(",");
  });

  
</script>
<?php include("footer.php"); ?>
</body>
</html>
