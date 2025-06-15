<?php
include("employerheader.php");
include("config/config.php");

// Fetch student application by Application Date on a descending order
$sql = "SELECT student_application.*, 
               student.Stud_Name, 
               student.Stud_Programme, 
               student.Stud_Email, 
               student.Stud_Phone, 
               student.Stud_ResumePath,
               intern_listings.Int_Position 
        FROM student_application
        JOIN student ON student_application.StudentID = student.StudentID
        JOIN intern_listings ON student_application.InternshipID = intern_listings.InternshipID
        ORDER BY student_application.App_Date DESC";

$result = $conn->query($sql);
?>

<html>
<head>
<title></title>
<style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f9f9f9;
      margin: 0;
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
      font-weight: normal;
      color: #666666; 
      border-bottom: 2px solid #ccc;
    }

    td {
      vertical-align: middle;
    }

    tr:not(:last-child) {
      border-bottom: 1px solid #ddd;
    }

    td.candidate .line {
      margin-bottom: 6px;
    }

    td.candidate .line strong {
      font-size: 18px;
    }

    td.position {
      font-size: 18px;
    }

    .application-status {
      font-size: 13px;
      display: inline-block;
      padding: 18px 15px;
      background-color: #b2f0f3; /* or your color */
      border-radius: 8px;
      font-weight: bold;
      text-align: center;
      white-space: nowrap;
    }

    td.application-status-cell {
      padding-top: 40px;
      display: flex;
      align-items: center;
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
      background-color: #FFD900;
      padding: 6px 30px;
      font-size: 13px;
      font-weight: bold;
      border-radius: 8px;
      color: black;
      display: inline-block;
      margin-top: 8px;
      text-decoration: none;
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
<body>

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
                <?php $counter = 1; ?>
                <?php if ($result->num_rows > 0): ?>
                  <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= $counter++ ?></td>
                      <td class="candidate">
                        <div class="line"><strong><?= htmlspecialchars($row['Stud_Name']) ?></strong></div>
                        <div class="line"><?= htmlspecialchars($row['Stud_Programme']) ?></div>
                        <div class="line"><?= htmlspecialchars($row['Stud_Email']) ?></div>
                        <div class="line"><?= htmlspecialchars($row['Stud_Phone']) ?></div>
                      </td>
                      <td class="position">
                        <div><?= htmlspecialchars($row['Int_Position']) ?></div>
                        <div><a class="application-received" href="<?= htmlspecialchars($row['Stud_ResumePath']) ?>"download>Application Received</a></div>
                      </td>
                      <td class="application-status-cell">
                        <div class="application-status pending"><?= htmlspecialchars($row['App_Status']) ?></div>
                      </td>
                      <td><?= date('d/m/Y', strtotime($row['App_Date'])) ?></td>
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
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" style="text-align: center;">No applications found.</td>
                  </tr>
                <?php endif; ?>
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


<?php
include("footer.php");
?>

</body>
</html>

<?php
$conn->close();
?>