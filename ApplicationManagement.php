<?php
include("employerheader.php");
include("config/config.php");

$sql = "SELECT student_application.*, 
               student.Stud_Name, 
               student.Stud_Programme, 
               student.Email, 
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

    .download-icon {
      height: 20px;
      width: auto;
      margin-left: 12px;
      margin-bottom: 3px;
      vertical-align: middle; 
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

    .in-review {
      background-color: #FFD900;
      color: #000;
    }

    .interview {
      background-color: #2577db;
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
      padding: 3px 20px;
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
                        <div class="line"><?= htmlspecialchars($row['Email']) ?></div>
                        <div class="line"><?= htmlspecialchars($row['Stud_Phone']) ?></div>
                      </td>
                      <td class="position">
                        <div><?= htmlspecialchars($row['Int_Position']) ?></div>
                        <a class="application-received" href="download_resume.php?appid=<?= $row['ApplicationID'] ?>" target="_blank">Application Received<img src="image/download-icon.png" alt="FTMK Logo" class="download-icon"></a>
                      </td>
                      <td class="application-status-cell">
                        <?php
                          $statusClass = strtolower(str_replace(' ', '-', $row['App_Status']));
                        ?>
                        <div class="application-status <?= $statusClass ?>">
                          <?= htmlspecialchars($row['App_Status']) ?>
                        </div>
                      </td>
                      <td><?= date('d/m/Y', strtotime($row['App_Date'])) ?></td>
                      <td>
                        <div class="threedots-wrapper">
                          <img src="image/horizontal 3 dots image.png" alt="horizontal 3 dots" class="dropdown-toggle">
                          <div class="dropdown-menu">
                            <button class="dropdown-item interview-btn" data-email="<?= $row['Email'] ?>" data-company="<?= $_SESSION['Comp_Name'] ?>" data-appid="<?= $row['ApplicationID'] ?>">Interview</button>
                            <button class="dropdown-item">Offer</button>
                            <button class="dropdown-item">Reject</button>
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
  document.addEventListener("DOMContentLoaded", function () {
    const toggles = document.querySelectorAll(".dropdown-toggle");

    toggles.forEach(function (toggle) {
      const menu = toggle.nextElementSibling;

      toggle.addEventListener("click", function (e) {
        e.stopPropagation();

        // Close all other menus
        document.querySelectorAll(".dropdown-menu").forEach(function (m) {
          if (m !== menu) m.style.display = "none";
        });

        // Toggle current menu
        menu.style.display = menu.style.display === "flex" ? "none" : "flex";
      });
    });

    // Close all menus on outside click
    document.addEventListener("click", function () {
      document.querySelectorAll(".dropdown-menu").forEach(function (menu) {
        menu.style.display = "none";
      });
    });

    // Interview button click logic
    document.querySelectorAll('.interview-btn').forEach(button => {
      button.addEventListener('click', function () {
        const email = this.getAttribute('data-email');
        const company = this.getAttribute('data-company');
        const appid = this.getAttribute('data-appid');

        fetch('send_interview_email.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `email=${encodeURIComponent(email)}&company=${encodeURIComponent(company)}&appid=${appid}`
        })
        .then(response => response.text())
        .then(data => {
          alert(data); // Show success or error message
        });
      });
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