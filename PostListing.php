<!DOCTYPE html>
<html lang="en">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Martel+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="PostListing.css" type="text/css">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employer Post Listings</title>

  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f6f6f6;
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
    }

    .job-template {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      background-color: #ffffff;
      box-shadow: 0 8px 12px rgba(0, 0, 0, 0.3);
      padding: 20px;
      margin: 40px auto;
      width: 90%;
      max-width: 1200px;
      border-radius: 8px;
      position: relative;
    }

    .job-info {
      flex-grow: 1;
      font-family: 'Martel Sans', sans-serif;
      padding-left: 20px;
    }

    .job-info h2 {
      font-size: 25px;
    }

    .nav-jobdesc ul {
      list-style: none;
      padding-left: 35px;
      margin: 10px 0 0;
      display: flex;
      flex-direction: column;
      gap: 12px;
      font-family: 'Martel Sans', sans-serif;
      font-size: 16px;
      color: #333;
    }

    .threedots-wrapper {
      cursor: pointer;
      position: relative;
    }


    .threedots-wrapper img {
        height: 22px;
        padding: 5px;
        padding-top: 30px;
    }

    .dropdown-menu {
        position: absolute;
        right: 0;
        top: 70px;
        background-color: #fff;
        box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
        display: none;
        flex-direction: column;
        z-index: 100;
        border-radius: 4px;
        width: 140px;
    }

    .dropdown-item {
        padding: 16px;
        text-align: left;
        background: none;
        border: none;
        font-family: 'Roboto', sans-serif;
        font-size: 14px;
        cursor: pointer;
    }

    .dropdown-item:hover {
        background-color: #f1f1f1;
    }

    .job-status {
        background-color:rgb(172, 235, 187);
        color: #155724; 
        text-align: center;
        margin-top: 2px;
        margin-left: 35px;
        border-radius: 10px;
        font-family: 'Roboto', sans-serif;
        font-weight: bolder;
        font-size: 14px;
        width: 260px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .job-status p {
        padding: 8px;
    }

  </style>
</head>
<body>
  <?php
  include("EmployerHeader.php");
  ?>
  <h1>Post Listing</h1>
<div class="job-template">
  <div class="job-info">
    <h2>Software Engineering Internship</h2>
    <nav class="nav-jobdesc">
        <ul>
            <li>Razer Malaysia</li>
            <li>Shah Alam, Selangor</li>
            <li>Diploma</li>
            <li>1 week ago</li>
        </ul>
    </nav>
    <div class="job-status">
        <p>APPLICATION AVAILABLE</p>
    </div>
  </div>
  <div class="threedots-wrapper">
    <img src="image/3-dots-icon.png" alt="3 dots icon" class="dropdown-toggle">
    <div class="dropdown-menu">
        <button class="dropdown-item">Edit</button>
        <button class="dropdown-item">Delete</button>
    </div>
  </div>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const toggle = document.querySelector('.dropdown-toggle');
    const menu = document.querySelector('.dropdown-menu');

    toggle.addEventListener('click', function(e) {
      e.stopPropagation(); // Prevent click from bubbling
      menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
      menu.style.display = 'none';
    });
  });
</script>

</body>
</html>
