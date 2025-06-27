<?php
session_start();
if (!isset($_SESSION['Comp_Name'])) {
  header("Location: login.html");
  exit();
}

include("employerheader.php");
include('config/connect.php');

// Get only applications for this employer
$sql = "SELECT student_application.*, 
               student.Stud_Name, 
               student.Stud_Programme, 
               student.Email, 
               student.Stud_Phone, 
               student.Stud_ResumePath,
               student.StudentID,
               intern_listings.Int_Position 
        FROM student_application
        JOIN student ON student_application.StudentID = student.StudentID
        JOIN intern_listings ON student_application.InternshipID = intern_listings.InternshipID
        JOIN employer ON intern_listings.EmployerID = employer.EmployerID
        WHERE employer.Comp_Name = ?
        ORDER BY student_application.App_Date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['Comp_Name']);
$stmt->execute();
$result = $stmt->get_result();
?>

<html>
<head>
  <title>Application Management</title>
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
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
    td.candidate .line strong, td.candidate .line a.name-link {
      font-size: 18px;
      font-weight: bold;
      color: #222;
      text-decoration: underline;
      cursor: pointer;
    }
    td.candidate .line a.name-link:hover {
      color: #007bff;
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
      background-color: #b2f0f3;
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
    .pending { background-color: rgb(86, 235, 255); color: #000; }
    .in-review { background-color: #FFD900; color: #000; }
    .interview, .offered, .accepted { background-color: rgb(95, 255, 95); color: #000; }
    .rejected, .declined { background-color: #f66; color: #000; }
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
    .application-received:hover {
      background-color:rgb(197, 175, 32);
      transform: translateY(-2px);
      transition: background-color 0.3s ease, transform 0.2s ease;
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
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $counter++ ?></td>
              <td class="candidate">
                <div class="line">
                  <!-- Remove target="_blank" so it opens in same window -->
                  <a class="name-link" href="employerviewprof.php?id=<?= $row['StudentID'] ?>">
                    <?= htmlspecialchars($row['Stud_Name']) ?>
                  </a>
                </div>
                <div class="line"><?= htmlspecialchars($row['Stud_Programme']) ?></div>
                <div class="line"><?= htmlspecialchars($row['Email']) ?></div>
                <div class="line"><?= htmlspecialchars($row['Stud_Phone']) ?></div>
              </td>
              <td class="position">
                <div><?= htmlspecialchars($row['Int_Position']) ?></div>
                <?php if (!empty($row['Stud_ResumePath'])): ?>
                  <a class="application-received" href="download_resume.php?appid=<?= $row['ApplicationID'] ?>" target="_blank">Application Received<img src="image/download-icon.png" alt="Download Resume" class="download-icon">
                  </a>
                <?php endif; ?>
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
                    <button class="dropdown-item interview-btn" data-email="<?= $row['Email'] ?>" data-company="<?= $_SESSION['Comp_Name'] ?>" data-appid="<?= $row['ApplicationID'] ?>" data-status="<?= $row['App_Status'] ?>">Interview</button>
                    <button class="dropdown-item offer-btn" data-email="<?= $row['Email'] ?>" data-company="<?= $_SESSION['Comp_Name'] ?>" data-appid="<?= $row['ApplicationID'] ?>" data-status="<?= $row['App_Status'] ?>">Offer</button>
                    <button class="dropdown-item reject-btn" data-email="<?= $row['Email'] ?>" data-company="<?= $_SESSION['Comp_Name'] ?>" data-appid="<?= $row['ApplicationID'] ?>" data-status="<?= $row['App_Status'] ?>">Reject</button>
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
      const toggles = document.querySelectorAll(".dropdown-toggle");
      toggles.forEach(function(toggle) {
        const menu = toggle.nextElementSibling;
        toggle.addEventListener("click", function(e) {
          e.stopPropagation();
          document.querySelectorAll(".dropdown-menu").forEach(function(m) {
            if (m !== menu) m.style.display = "none";
          });
          menu.style.display = menu.style.display === "flex" ? "none" : "flex";
        });
      });
      document.addEventListener("click", function() {
        document.querySelectorAll(".dropdown-menu").forEach(function(menu) {
          menu.style.display = "none";
        });
      });

      // Interview button click
      // Show the interview modal
      function openInterviewModal(email, company, appid) {
        document.getElementById('interviewEmail').value = email;
        document.getElementById('interviewCompany').value = company;
        document.getElementById('interviewAppID').value = appid;
        document.getElementById('interviewModal').style.display = 'flex';
      }

      // Close the interview modal
      function closeInterviewModal() {
        document.getElementById('interviewModal').style.display = 'none';
        document.getElementById('interviewForm').reset();
      }
      document.getElementById('cancelInterviewBtn').addEventListener('click', closeInterviewModal);

      // Update interview button click to show modal instead of prompt
      document.querySelectorAll('.interview-btn').forEach(button => {
        button.addEventListener('click', function () {
          const status = this.getAttribute('data-status');
          if (['Offered', 'Rejected', 'Interview'].includes(status)) {
            alert(`❌ An email has already been sent for this application (${status}).`);
            return;
          }

          if (['Accepted', 'Declined'].includes(status)) {
            alert(`❌ Unable to send an interview email, student's status is (${status}).`);
            return;
          }

          if (status === 'Pending') {
            alert("❌ You must review the student's resume before inviting them to interview.");
            return;
          }

          openInterviewModal(
            this.getAttribute('data-email'),
            this.getAttribute('data-company'),
            this.getAttribute('data-appid')
          );
        });
      });

      // Interview form submission
      document.getElementById('interviewForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const email = document.getElementById('interviewEmail').value;
        const company = document.getElementById('interviewCompany').value;
        const appid = document.getElementById('interviewAppID').value;
        const date = document.getElementById('interviewDate').value;
        const time = document.getElementById('interviewTime').value;
        const location = document.getElementById('interviewLocation').value;

        fetch('send_interview_email.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `email=${encodeURIComponent(email)}&company=${encodeURIComponent(company)}&appid=${appid}&date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}&location=${encodeURIComponent(location)}`
        })
          .then(response => response.text())
          .then(data => {
            alert(data);
            closeInterviewModal();
          });
      });


      // Offer button click
      // Show the offer modal
      function openOfferModal(email, company, appid) {
        document.getElementById('offerEmail').value = email;
        document.getElementById('offerCompany').value = company;
        document.getElementById('offerAppID').value = appid;
        document.getElementById('offerModal').style.display = 'flex';
      }

      // Close the modal
      function closeOfferModal() {
        document.getElementById('offerModal').style.display = 'none';
        document.getElementById('offerForm').reset();
      }
      document.getElementById('cancelOfferBtn').addEventListener('click', closeOfferModal);

      // Handle "Offer" button clicks
      document.querySelectorAll('.offer-btn').forEach(button => {
        button.addEventListener('click', function () {
          const status = this.getAttribute('data-status');
          if (['Offered', 'Rejected', 'Accepted', 'Declined'].includes(status)) {
            alert(`❌ You cannot make an offer. Application already marked as "${status}".`);
            return;
          }
          if (!['In Review', 'Interview'].includes(status)) {
            alert("❌ You must review the student's resume before making an offer.");
            return;
          }

          openOfferModal(
            this.getAttribute('data-email'),
            this.getAttribute('data-company'),
            this.getAttribute('data-appid')
          );
        });
      });

      // Handle form submission
      document.getElementById('offerForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const email = document.getElementById('offerEmail').value;
        const company = document.getElementById('offerCompany').value;
        const appid = document.getElementById('offerAppID').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        fetch('send_offer_email.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `email=${encodeURIComponent(email)}&company=${encodeURIComponent(company)}&appid=${appid}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`
        })
          .then(response => response.text())
          .then(data => {
            alert(data);
            closeOfferModal();
          });
      });


      // Reject button click
      document.querySelectorAll('.reject-btn').forEach(button => {
      button.addEventListener('click', function() {
        const status = this.getAttribute('data-status');
        if (['Offered', 'Rejected', 'Accepted', 'Declined'].includes(status)) {
          alert(`❌ You cannot reject this application. It is already marked as "${status}".`);
          return;
        }

        const email = this.getAttribute('data-email');
        const company = this.getAttribute('data-company');
        const appid = this.getAttribute('data-appid');

        fetch('send_rejection_email.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `email=${encodeURIComponent(email)}&company=${encodeURIComponent(company)}&appid=${appid}`
          })
          .then(response => response.text())
          .then(data => {
            alert(data);
          });
      });
    });


    });
  </script>

  <!-- Offer Modal -->
  <div id="offerModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 400px; width: 100%;">
      <h2 style="margin-top: 0;">Send Internship Offer</h2>
      <form id="offerForm">
        <label>Start Date:</label>
        <input type="date" id="startDate" name="start_date" required style="width: 100%; padding: 8px; margin-bottom: 15px;"><br>
        
        <label>End Date:</label>
        <input type="date" id="endDate" name="end_date" required style="width: 100%; padding: 8px; margin-bottom: 15px;"><br>

        <input type="hidden" id="offerEmail" name="email">
        <input type="hidden" id="offerCompany" name="company">
        <input type="hidden" id="offerAppID" name="appid">

        <div style="display: flex; justify-content: space-between; margin-top: 20px;">
          <button type="submit" style="padding: 8px 16px; cursor: pointer; background-color: #28a745; color: white; border: none; border-radius: 5px;">Send</button>
          <button type="button" id="cancelOfferBtn" style="padding: 8px 16px; cursor: pointer; background-color: #ccc; border: none; border-radius: 5px;">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Interview Modal -->
<div id="interviewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
  <div style="background: white; padding: 30px; border-radius: 10px; max-width: 400px; width: 100%;">
    <h2 style="margin-top: 0;">Send Interview Invitation</h2>
    <form id="interviewForm">
      <label>Date:</label>
      <input type="date" id="interviewDate" name="date" required style="width: 100%; padding: 8px; margin-bottom: 15px;"><br>

      <label>Time:</label>
      <input type="text" id="interviewTime" name="time" required placeholder="e.g. 10:00 AM" style="width: 100%; padding: 8px; margin-bottom: 15px;"><br>

      <label>Location / Link:</label>
      <input type="text" id="interviewLocation" name="location" required style="width: 100%; padding: 8px; margin-bottom: 15px;"><br>

      <input type="hidden" id="interviewEmail" name="email">
      <input type="hidden" id="interviewCompany" name="company">
      <input type="hidden" id="interviewAppID" name="appid">

      <div style="display: flex; justify-content: space-between; margin-top: 20px;">
        <button type="submit" style="padding: 8px 16px; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 5px;">Send</button>
        <button type="button" id="cancelInterviewBtn" style="padding: 8px 16px; cursor: pointer; background-color: #ccc; border: none; border-radius: 5px;">Cancel</button>
      </div>
    </form>
  </div>
</div>




  <?php include("footer.php"); ?>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>