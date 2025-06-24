<?php
session_start();
include("employerheader.php");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Posting</title>
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
<h1>Job Posting</h1>
<div class="container">
  <h3>Please fill in all fields and click Submit.</h3>
  <form method="post" action="joblist.php" onsubmit="return validateForm()">

    <label for="job_title">Job Title</label>
    <input type="text" name="job_title" id="job_title" placeholder="Job Title" required>

    <div class="form-row">
      <div>
        <label for="state">Location</label>
        <select name="state" id="state" required>
          <option value="">-- Select a State --</option>
          <option value="Sabah">Sabah</option>
          <option value="Sarawak">Sarawak</option>
          <option value="Perlis">Perlis</option>
          <option value="Pahang">Pahang</option>
          <option value="Perak">Perak</option>
          <option value="Pulau Pinang">Pulau Pinang</option>
          <option value="Terengganu">Terengganu</option>
          <option value="Kedah">Kedah</option>
          <option value="Kelantan">Kelantan</option>
          <option value="Negeri Sembilan">Negeri Sembilan</option>
          <option value="Melaka">Melaka</option>
          <option value="Johor">Johor</option>
          <option value="Selangor">Selangor</option>
        </select>
      </div>
      <div>
        <label for="city">&nbsp;</label>
        <input type="text" name="city" id="city" placeholder="City" required>
      </div>
    </div>

    <div>
      <label for="qualification">Qualification</label>
      <select name="qualification" id="qualification" required>
          <option value="">-- Select a Level --</option>
          <option value="Diploma">Diploma</option>
          <option value="Degree">Degree</option>
        </select>
    </div>

    <label for="programme">Field</label>
    <div style="
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 15px;
      background-color: #f2f2f2;
      margin-bottom: 20px;
    ">
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
        <label><input type="checkbox" name="programme[]" value="Computer Science"> <span style="font-weight: normal;">Computer Science</span></label>
        <label><input type="checkbox" name="programme[]" value="Game Technology"> <span style="font-weight: normal;">Game Technology</span></label>
        <label><input type="checkbox" name="programme[]" value="Computer Security"> <span style="font-weight: normal;">Computer Security</span></label>
        <label><input type="checkbox" name="programme[]" value="Computer Networking"> <span style="font-weight: normal;">Computer Networking</span></label>
        <label><input type="checkbox" name="programme[]" value="Software Development"> <span style="font-weight: normal;">Software Development</span></label>
        <label><input type="checkbox" name="programme[]" value="Database Management"> <span style="font-weight: normal;">Database Management</span></label>
        <label><input type="checkbox" name="programme[]" value="Interactive Media"> <span style="font-weight: normal;">Interactive Media</span></label>
        <label><input type="checkbox" name="programme[]" value="Artificial Intelligence"> <span style="font-weight: normal;">Artificial Intelligence</span></label>
        <label><input type="checkbox" name="programme[]" value="Cloud Computing"> <span style="font-weight: normal;">Cloud Computing</span></label>
      </div>
    </div>


    <label for="allowance">Monthly Allowance (MYR)</label>
    <input type="text" name="allowance" id="allowance" placeholder="e.g. 500" required>



    <label for="job_details">Job Details</label>
    <textarea name="job_details" id="job_details" placeholder="Describe the job..." required></textarea>

    <div class="button-group">
      <button type="reset" class="btn-cancel" onclick="return confirmCancel()">Cancel</button>
      <button type="submit" class="btn-submit">Submit</button>
    </div>

  </form>
</div>

<?php
include("footer.php");
?>
<script>
    // Function to ensure that the user choose at least 1 programme
    function validateForm() {
      const checkboxes = document.querySelectorAll('input[name="programme[]"]');
      const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

      if (!isChecked) {
        alert("Please select at least one programme.");
        return false;
      }
      return true;
    }

    // Function to confirm user cancellation to avoid accidental data loss
    function confirmCancel() {
      return confirm("Are you sure you want to cancel? All entered data will be lost.");
    }
  </script>

</body>
</html>
