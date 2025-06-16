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
  height: 650px;
  margin: 0px auto;
  background-color:rgb(231, 223, 223);
  display:  flex;
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

.salary-section .save-btn,
.salary-section .edit-btn {
  padding: 10px 16px;
}

.section-title {
  font-size: 20px;
  font-weight: bold;
  color: #333;
  margin-bottom: 20px;
  text-align: left;
}




  </style>
</head>
<body>
<?php include("UserHeader.php"); ?>

<div class="content">
  <div class="box">
    <h3>Resume</h3>
    <div class="upload-section">
      <label>Upload your resume to create and strengthen your profile.</label>
      <div class="resume-box">
        <label for="resume" class="custom-upload">Upload</label>
        <input type="file" id="resume" name="resume" accept=".pdf">
      </div>
</div>

    <h3 style="margin-top: 30px;">Skills</h3>
    <p>Add your skills to make your profile more valuable.</p>
    <div class="input-group">
      <input type="text" id="skillInput" placeholder="e.g. Creative">
      <button class="add-skill-btn" onclick="addSkill()">Add Skills</button>
    </div>
    <div class="skills-list" id="skillsList">
      <div class="tag">1. Quick to adapt <button onclick="this.parentElement.remove()">&times;</button></div>
      <div class="tag">2. Expert in UI <button onclick="this.parentElement.remove()">&times;</button></div>
    </div>
  </div>

  <!-- RIGHT SIDE BOX: Job Preferences (Title inside the box) -->
  <div class="box">
    <div class="section-title">Job Preferences</div>

    <h4>Preferred Location</h4>
    <input type="text" id="locationInput">
    <button class="save-btn" onclick="addLocation()">Save</button>

    <div class="locations-list" id="locationsList">
      <div class="tag">1. Hulu Langat, Selangor <button onclick="this.parentElement.remove()">&times;</button></div>
      <div class="tag">2. Seremban, Negeri Sembilan <button onclick="this.parentElement.remove()">&times;</button></div>
    </div>

    <h4 style="margin-top: 30px;">Preferred Allowance / Salary</h4>
    <div class="salary-section">
      <input type="number" placeholder="500">
      <select>
        <option>Monthly</option>
        <option>Weekly</option>
      </select>
      <button class="save-btn">Save</button>
      <button class="edit-btn">Edit</button>
    </div>
  </div>
</div>


  <script>
    function addSkill() {
        const skillInput = document.getElementById('skillInput');
        const skillsList = document.getElementById('skillsList');
        
        if (skillInput.value.trim() !== '') {
            const currentSkills = skillsList.getElementsByClassName('tag').length;
            const skillNumber = currentSkills + 1;
            const tag = document.createElement('div');
            tag.className = 'tag';
            tag.innerHTML = `${skillNumber}. ${skillInput.value} <button onclick="this.parentElement.remove(); renumberSkills();">&times;</button>`;
            skillsList.appendChild(tag);
            skillInput.value = '';
        }
    }
    
    function renumberSkills() {
        const tags = document.querySelectorAll('#skillsList .tag');
        tags.forEach((tag, index) => {
            const text = tag.innerText.replace(/\d+\.\s/, ''); // Remove existing number
            const button = tag.querySelector('button');
            tag.innerHTML = `${index + 1}. ${text}`;
            tag.appendChild(button); // Re-append the remove button
            });
        }


    function addLocation() {
        const locationInput = document.getElementById('locationInput');
        const locationsList = document.getElementById('locationsList');

        if (locationInput.value.trim() !== '') {
            const currentLocations = locationsList.getElementsByClassName('tag').length;
            const locationNumber = currentLocations + 1;
            
            const tag = document.createElement('div');
            tag.className = 'tag';
            tag.innerHTML = `${locationNumber}. ${locationInput.value} <button onclick="this.parentElement.remove(); renumberLocations();">&times;</button>`;
            locationsList.appendChild(tag);

            locationInput.value = '';
        }
    }
    
    function renumberLocations() {
        const tags = document.querySelectorAll('#locationsList .tag');
        tags.forEach((tag, index) => {
            const text = tag.innerText.replace(/\d+\.\s/, ''); // Remove existing number
            const button = tag.querySelector('button');
            tag.innerHTML = `${index + 1}. ${text}`;
            tag.appendChild(button); // Re-append the remove button
            });
}

  </script>
</body>
</html>
