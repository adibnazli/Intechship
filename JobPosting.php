<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Form</title>
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
<?php include("EmployerHeader.php"); ?>

<h1>Job Posting</h1>
<div class="container">
  <h3>Please fill in all fields and click Submit.</h3>
  <form method="post" action="p_form.php">

    <label for="job_title">Job Title</label>
    <input type="text" name="job_title" id="job_title" placeholder="Job Title">

    <div class="form-row">
      <div>
        <label for="state">Location</label>
        <select name="state" id="state">
          <option value="">-- Select a State --</option>
          <option value="SB">Sabah</option>
          <option value="SR">Sarawak</option>
          <option value="PS">Perlis</option>
          <option value="PHG">Pahang</option>
          <option value="PRK">Perak</option>
          <option value="PP">Pulau Pinang</option>
          <option value="TG">Terengganu</option>
          <option value="KD">Kedah</option>
          <option value="KTN">Kelantan</option>
          <option value="NS">Negeri Sembilan</option>
          <option value="MK">Melaka</option>
          <option value="JR">Johor</option>
          <option value="SLG">Selangor</option>
          <!-- Add more states as needed -->
        </select>
      </div>
      <div>
        <label for="city">&nbsp;</label>
        <input type="text" name="city" id="city" placeholder="City">
      </div>
    </div>

    <label for="programme">Programme</label>
    <select name="programme" id="programme">
      <option value="">Select a Programme</option>
      <option value="dcs">Diploma in Computer Science</option>
      <option value="gt">Bachelor of Information Technology (Game Technology)</option>
      <option value="cs">Bachelor of Computer Science (Computer Security)</option>
      <option value="cn">Bachelor of Computer Science (Computer Networking)</option>
      <option value="sd">Bachelor of Computer Science (Software Development)</option>
      <option value="dm">Bachelor of Computer Science (Database Management)</option>
      <option value="im">Bachelor of Computer Science (Interactive Media)</option>
      <option value="ai">Bachelor of Computer Science (Artificial Intelligence)</option>
      <option value="cca">Bachelor of Technology in Cloud Computing and Application with Honours</option>
    </select>

    <label for="job_details">Job Details</label>
    <textarea name="job_details" id="job_details" placeholder="Describe the job..."></textarea>

    <div class="button-group">
      <button type="reset" class="btn-cancel">Cancel</button>
      <button type="submit" class="btn-submit">Submit</button>
    </div>

  </form>
</div>

</body>
</html>
