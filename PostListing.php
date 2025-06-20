<?php
session_start();
include("employerheader.php");
include("config/config.php");

// Fetch jobs (you can filter by EmployerID if needed)
$sql = "SELECT intern_listings.*, employer.Comp_Name 
        FROM intern_listings 
        JOIN employer ON intern_listings.EmployerID = employer.EmployerID 
        ORDER BY intern_listings.InternshipID DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
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
      font-size: 14px;
      cursor: pointer;
      text-decoration: none;
      color: #333;
    }

    .dropdown-item:hover {
      background-color: #f1f1f1;
    }
  </style>
</head>
<body>

<h1>Post Listings</h1>

      <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <?php
          $postedAt = $row['PostedAt'];
          $postedDate = new DateTime($postedAt);
          $today = new DateTime();
          $interval = $today->diff($postedDate);

          if ($interval->y > 0) {
              $postedAgo = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
          } elseif ($interval->m > 0) {
              $postedAgo = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
          } elseif ($interval->d >= 7) {
              $weeks = floor($interval->d / 7);
              $postedAgo = $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
          } elseif ($interval->d > 0) {
              $postedAgo = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
          } else {
              $postedAgo = 'Today';
          }
        ?>

        <div class="job-template">
          <div class="job-info">
            <h2><?= htmlspecialchars($row['Int_Position']) ?></h2>
            <nav class="nav-jobdesc">
              <ul>
                <li><?= htmlspecialchars($row['Comp_Name']); ?></li>
                <li><?= htmlspecialchars($row['Int_City']) ?>, <?= htmlspecialchars($row['Int_State']) ?></li>
                <li><?= htmlspecialchars($row['Int_Programme']) ?></li>
                <li>RM<?= htmlspecialchars($row['Int_Allowance']) ?>/month</li>
                <li><?= $postedAgo ?></li>
              </ul>
            </nav>
          </div>

          <div class="threedots-wrapper">
            <img src="image/3-dots-icon.png" alt="3 dots icon" class="dropdown-toggle">
            <div class="dropdown-menu">
              <a class="dropdown-item" href="editjob.php?id=<?= $row['InternshipID'] ?>">Edit</a>
              <a class="dropdown-item" href="deletejob.php?id=<?= $row['InternshipID'] ?>" onclick="return confirm('Are you sure you want to delete this job?');">Delete</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center;">No jobs posted yet.</p>
    <?php endif; ?>


<script>
  document.addEventListener("DOMContentLoaded", function() {
    const toggles = document.querySelectorAll('.dropdown-toggle');
    toggles.forEach(toggle => {
      const menu = toggle.nextElementSibling;
      toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
      });
    });

    document.addEventListener('click', function() {
      document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.display = 'none';
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
