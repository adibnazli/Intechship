<?php
session_start();
include('config/config.php');
include('AdminHeader.php');

$adminProgram = $_SESSION['Program_Desc'] ?? '';

if (!empty($adminProgram)) {
    $stmt = $conn->prepare("SELECT * FROM student WHERE password IS NOT NULL AND Stud_protype = ?");
    $stmt->bind_param("s", $adminProgram);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM student WHERE password IS NOT NULL");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Approval</title>
  <style>
    body {
      background-color: #f0f0f0;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    .container {
      background-color: #ffffff;
        width: 80%;
        margin: 50px auto;
        padding: 50px;
        border-radius: 10px;
        justify-content: space-between;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
    }

    h1 {
      margin-bottom: 5px;
    }

    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .status-indicator {
      padding: 5px 12px;
      background: orange;
      color: white;
      font-weight: bold;
      border-radius: 4px;
    }

    .search-bar {
      display: flex;
      margin-bottom: 20px;
    }

    .search-bar input {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px 0 0 4px;
    }

    .search-bar button {
      padding: 10px 20px;
      border: none;
      background: #333;
      color: white;
      border-radius: 0 4px 4px 0;
      cursor: pointer;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    .status-pending {
      background-color: #fff3cd;
      color: #856404;
      padding: 4px 8px;
      border-radius: 4px;
      display: inline-block;
    }

    .status-approved {
      background-color: #d4edda;
      color: #155724;
      padding: 4px 8px;
      border-radius: 4px;
      display: inline-block;
    }

    .status-rejected {
    background-color: #f8d7da;
    color: #721c24;
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
    }
    .btn-allow {
    background-color: #007bff;
    color: white;
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    margin-top: 5px;
    }


    .btn-approve, .btn-reject {
    display: inline-block;
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    color: white;
    margin-right: 5px;
    white-space: nowrap;
    }


    .btn-approve {
      background-color: #28a745;
      margin-right: 5px;
    }

    .btn-reject {
      background-color: #dc3545;
    }

    .no-data {
      text-align: center;
      padding: 50px;
      color: gray;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="dashboard-header">
      <div>
        <h1>Student Approval</h1>
        <p>Review and manage students for your program</p>
      </div>
      <div class="status-indicator">Admin Panel</div>
    </div>

    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search by name, matric or email...">
      <button onclick="searchTable()">Search</button>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <table id="studentTable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Matric No</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['Stud_Name']) ?></td>
              <td><?= htmlspecialchars($row['Stud_MatricNo']) ?></td>
              <td><?= htmlspecialchars($row['Email']) ?></td>
              <td>
                <?php if (is_null($row['approve'])): ?>
                <span style="color: gray;">No request</span>
                <?php elseif ($row['approve'] == 0): ?>
                  <span class="status-pending">Pending</span>
                <?php elseif ($row['approve'] == 1): ?>
                  <span class="status-approved">Approved</span>
                <?php elseif ($row['approve'] == 3): ?>
                  <span class="status-rejected">Rejected</span>
                <?php else: ?>
                    —
                <?php endif; ?>
              </td>
              <td>
                <?php if (is_null($row['approve'])): ?>
                    —
                <?php elseif ($row['approve'] == 0): ?>
                    <div style="white-space: nowrap;">
                    <a class="btn-approve" href="approve.php?id=<?= $row['StudentID'] ?>">Approve</a>
                    <a class="btn-reject" href="reject.php?id=<?= $row['StudentID'] ?>">Reject</a>
                    </div>
                <?php elseif ($row['approve'] == 3): ?>
                    <div>
                    <a class="btn-allow" href="allow.php?id=<?= $row['StudentID'] ?>">Allow</a>
                    </div>
                <?php else: ?>
                    —
                <?php endif; ?>
                </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="no-data">No student records found for your program.</div>
    <?php endif; ?>
  </div>

  <script>
    function searchTable() {
      const input = document.getElementById("searchInput").value.toLowerCase();
      const rows = document.querySelectorAll("#studentTable tbody tr");

      rows.forEach(row => {
        const name = row.cells[0].textContent.toLowerCase();
        const matric = row.cells[1].textContent.toLowerCase();
        const email = row.cells[2].textContent.toLowerCase();

        if (name.includes(input) || matric.includes(input) || email.includes(input)) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    }

    document.getElementById("searchInput").addEventListener("keyup", function(e) {
      if (e.key === "Enter") {
        searchTable();
      }
    });
  </script>
  <?php include("footer.php"); ?>
</body>
</html>
