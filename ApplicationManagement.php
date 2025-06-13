<html>
<head>
<title></title>
<style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f9f9f9;
      margin: 20px;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      background-color: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      text-align: left;
      padding: 14px;
    }

    th {
      border-bottom: 2px solid #ccc;
    }

    tr:not(:last-child) {
      border-bottom: 1px solid #ddd;
    }

    .candidate-name {
      font-weight: bold;
    }

    .email {
      color: #007BFF;
      text-decoration: none;
    }

    .application-status {
      font-size: 13px;
      padding: 18px 20px;
      border-radius: 8px;
      font-weight: bold;
    }

    .pending {
      background-color: #b2ebf2;
      color: #000;
    }

    .accepted {
      background-color: #98f598;
      color: #000;
    }

    .rejected {
      background-color: #f66;
      color: #000;
    }

    .application-received {
      background-color: #ffc107;
      padding: 6px 12px;
      font-size: 13px;
      font-weight: bold;
      border-radius: 8px;
      color: black;
      display: inline-block;
      margin-top: 8px;
    }

    .threedots-wrapper {
      cursor: pointer;
      position: relative;
    }


    .threedots-wrapper img {
        height: 22px;
        padding: 5px;
        padding-top: 5px;
        padding-left: 14px;
    }

    .dropdown-menu {
        position: absolute;
        right: 0;
        top: 40px;
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

  </style>
</head>
</html>
<body>
<?php
include("EmployerHeader.php");
?>

<h1>Application Management</h1>
<div class="container">
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Candidate</th>
        <th>Job Applied</th>
        <th>Status</th>
        <th>Date Applied</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <!-- Row 1 -->
      <tr>
        <td>1</td>
        <td>
          <div class="candidate-name">John Doe</div>
          <div>Bachelor in Computer Science (Database Management)</div>
          <a href="mailto:johndoe71@gmeel.com" class="email">johndoe71@gmeel.com</a><br>
          011-xxx-xxxx
        </td>
        <td>
          Software Engineering Internship<br>
          <span class="application-received">Application Received ▼</span>
        </td>
        <td><span class="application-status pending">Pending</span></td>
        <td>03/04/2024</td>
        <td>
          <div class="threedots-wrapper">
            <img src="image/horizontal 3 dots image.png" alt="horizontal 3 dots" class="dropdown-toggle">
            <div class="dropdown-menu">
              <button class="dropdown-item">Edit</button>
              <button class="dropdown-item">Delete</button>
            </div>
          </div>
        </td>
      </tr>

    </tbody>
  </table>
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