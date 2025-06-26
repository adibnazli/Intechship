<?php
session_start();
include("UserHeader.php");
include("config/config.php");

// Access control: only logged-in students can access
if (!isset($_SESSION['studentID'])) {
    die("Access denied.");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Internship Search - InTechShip</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f9f9f9;
    }

    .search-container {
      padding: 20px;
      max-width: 1300px;
      margin: auto;
    }

    select, input[type="text"] {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      width: 180px;
    }

    .job-cards {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      justify-content: space-between;
    }

    .job-card {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      padding: 20px;
      width: calc(50% - 10px);
      box-sizing: border-box;
    }

    .job-card h3 {
      margin: 0 0 10px;
    }

    .job-card p {
      margin: 8px 0;
      color: #333;
    }

    .job-card .location,
    .job-card .duration {
      font-size: 14px;
      color: #666;
    }

    .apply-btn {
      margin-top: 15px;
      background-color: #ffdc00;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }

    .apply-btn:hover {
      background-color:rgb(197, 175, 32);
      transform: translateY(-2px);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }
  </style>
</head>
<body>

<?php
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'success':
            echo "<p style='color:green;'>Application submitted successfully!</p>";
            break;
        case 'already_applied':
            echo "<p style='color:orange;'>You have already applied for this internship.</p>";
            break;
        case 'fail':
            echo "<p style='color:red;'>Failed to apply. Please try again.</p>";
            break;
        case 'sql_error':
            echo "<p style='color:red;'>A server error occurred. Please contact admin.</p>";
            break;
        case 'invalid_request':
            echo "<p style='color:red;'>Invalid request.</p>";
            break;
    }
}
?>


<div class="search-container">
  <!-- Search Form -->
  <form method="GET">
    <div style="text-align: center; margin-bottom: 20px;">
      <input type="text" name="keyword" placeholder="🔍 Search Internship"
             value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>"
             style="width: 60%; max-width: 600px;">
    </div>

    <!-- Filter Options -->
    <div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 20px; margin-bottom: 20px;">
      <select name="program_type" id="program_type" onchange="updateProgramOptions()">
        <option value="">Program Type</option>
        <option value="Diploma" <?= ($_GET['program_type'] ?? '') == 'Diploma' ? 'selected' : '' ?>>Diploma</option>
        <option value="Degree" <?= ($_GET['program_type'] ?? '') == 'Degree' ? 'selected' : '' ?>>Degree</option>
      </select>

      <select name="program_name" id="program_name">
        <option value="">Program</option>
        <?php if (isset($_GET['program_name'])): ?>
          <option value="<?= htmlspecialchars($_GET['program_name']) ?>" selected><?= htmlspecialchars($_GET['program_name']) ?></option>
        <?php endif; ?>
      </select>

      <select name="location" id="location" onchange="updateAreaOptions()">
        <option value="">Location</option>
        <?php
        $states = ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Pulau Pinang', 'Perak', 'Perlis', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'Kuala Lumpur', 'Labuan', 'Putrajaya'];
        foreach ($states as $state) {
          $selected = ($_GET['location'] ?? '') == $state ? 'selected' : '';
          echo "<option value=\"$state\" $selected>$state</option>";
        }
        ?>
      </select>

      <select name="area" id="area">
        <option value="">Area</option>
        <?php if (isset($_GET['area'])): ?>
          <option value="<?= htmlspecialchars($_GET['area']) ?>" selected><?= htmlspecialchars($_GET['area']) ?></option>
        <?php endif; ?>
      </select>

    </div>

    <div style="text-align: center;">
      <button type="submit" class="apply-btn">Search</button>
    </div>
  </form>

  <!-- Job Listings -->
  <div class="job-cards">
    <?php
    // 1. Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total results (excluding LIMIT)
$countSql = "SELECT COUNT(*) AS total FROM intern_listings i 
             JOIN employer e ON i.EmployerID = e.EmployerID 
             WHERE 1=1";

$filterSql = ""; // Store filter conditions separately

if (!empty($_GET['keyword'])) {
    $kw = $conn->real_escape_string($_GET['keyword']);
    $filterSql .= " AND (i.Int_Position LIKE '%$kw%' OR i.Int_Details LIKE '%$kw%')";
}

if (!empty($_GET['location'])) {
    $loc = $conn->real_escape_string($_GET['location']);
    $filterSql .= " AND i.Int_State = '$loc'";
}

if (!empty($_GET['area'])) {
    $area = $conn->real_escape_string($_GET['area']);
    $filterSql .= " AND i.Int_City LIKE '%$area%'";
}

if (!empty($_GET['program_name'])) {
    $prog = $conn->real_escape_string($_GET['program_name']);
    $filterSql .= " AND i.Int_Programme = '$prog'";
}

if (!empty($_GET['program_type'])) {
    $type = $conn->real_escape_string($_GET['program_type']);
    $filterSql .= " AND i.Int_Qualification = '$type'";
}

$countResult = $conn->query($countSql . $filterSql);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Final listing query
$sql = "SELECT i.*, e.Comp_Name AS EmployerName 
        FROM intern_listings i 
        JOIN employer e ON i.EmployerID = e.EmployerID 
        WHERE 1=1 $filterSql 
        ORDER BY i.InternshipID DESC 
        LIMIT $limit OFFSET $offset";


    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
      echo '<div class="job-card">';
echo '<h3>' . htmlspecialchars($row['Int_Position']) . '</h3>';

echo '<div style="display: flex; justify-content: space-between; align-items: flex-start;">';

echo '<div>';
echo '<p><strong>' . htmlspecialchars($row['EmployerName']) . '</strong></p>';
echo '<p>' . htmlspecialchars($row['Int_Programme']) . '</p>';
echo '</div>';

echo '<div style="text-align: right;">';
echo '<p class="location" style="margin: 0;">📍 ' . htmlspecialchars($row['Int_City']) . ', ' . htmlspecialchars($row['Int_State']) . '</p>';
echo '<p style="font-weight: bold; color: #2c3e50; margin: 4px 0;">RM ' . number_format($row['Int_Allowance'], 2) . '</p>';
echo '</div>';

echo '</div>'; 

// close flex container

$details = strip_tags($row['Int_Details']); // Remove HTML tags if any
$shortDetails = strlen($details) > 150 ? substr($details, 0, 150) . '...' : $details;
echo '<p>' . htmlspecialchars($shortDetails) . '</p>';
echo '<a href="intern_detail.php?id=' . $row['InternshipID'] . '" class="apply-btn" style="display: inline-block; text-decoration: none; color: black; font-weight: normal;">View Detail</a>';
echo '</div>';
      }
    } else {
      echo "<p>No internships found.</p>";
    }
    ?>
  </div>
  <?php if ($totalPages > 1): ?>
<div style="text-align:center; margin-top: 30px;">
  <?php
    $baseUrl = strtok($_SERVER["REQUEST_URI"], '?');
    parse_str($_SERVER['QUERY_STRING'], $params);

    function createPageLink($p, $label) {
        global $params, $baseUrl;
        $params['page'] = $p;
        $query = http_build_query($params);
        return "<a href=\"$baseUrl?$query\" style=\"margin: 0 8px; text-decoration:none; color: #333; font-weight:" . ($p == ($_GET['page'] ?? 1) ? 'bold' : 'normal') . ";\">$label</a>";
    }

    // Previous
    if ($page > 1) echo createPageLink($page - 1, "◀ Prev");

    // Page numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        echo createPageLink($i, $i);
    }

    // Next
    if ($page < $totalPages) echo createPageLink($page + 1, "Next ▶");
  ?>
</div>
<?php endif; ?>

</div>

<script>
  const areaOptions = {
    "Johor": ["Johor Bahru", "Batu Pahat", "Kluang", "Muar", "Segamat", "Skudai"],
    "Kedah": ["Alor Setar", "Sungai Petani", "Kulim", "Langkawi"],
    "Kelantan": ["Kota Bharu", "Pasir Mas", "Tumpat", "Gua Musang"],
    "Melaka": ["Melaka Tengah", "Alor Gajah", "Jasin"],
    "Negeri Sembilan": ["Seremban", "Port Dickson", "Nilai"],
    "Pahang": ["Kuantan", "Temerloh", "Bentong", "Cameron Highlands"],
    "Pulau Pinang": ["George Town", "Butterworth", "Bayan Lepas", "Seberang Perai"],
    "Perak": ["Ipoh", "Taiping", "Teluk Intan", "Lumut"],
    "Perlis": ["Kangar", "Arau"],
    "Sabah": ["Kota Kinabalu", "Tawau", "Sandakan", "Lahad Datu"],
    "Sarawak": ["Kuching", "Miri", "Sibu", "Bintulu"],
    "Selangor": ["Shah Alam", "Petaling Jaya", "Klang", "Ampang", "Gombak"],
    "Terengganu": ["Kuala Terengganu", "Dungun", "Kemaman", "Marang"],
    "Kuala Lumpur": ["Cheras", "Setapak", "Bukit Bintang", "Wangsa Maju"],
    "Labuan": ["Labuan Town"],
    "Putrajaya": ["Presint 1", "Presint 2", "Presint 3"]
  };

  function updateAreaOptions() {
    const state = document.getElementById("location").value;
    const areaSelect = document.getElementById("area");
    areaSelect.innerHTML = '<option value="">Area</option>';
    if (areaOptions[state]) {
      areaOptions[state].forEach(area => {
        const opt = document.createElement("option");
        opt.value = area;
        opt.text = area;
        areaSelect.appendChild(opt);
      });
    }
  }

  const programOptions = {
    "Diploma": [
      "Computer Science",
      "Computer Science, Game Technology",
      "Computer Science, Computer Security",
      "Computer Science, Computer Networking",
      "Computer Science, Software Development",
      "Computer Science, Database Management",
      "Computer Science, Interactive Media",
      "Computer Science, Artificial Intelligence",
      "Computer Science, Cloud Computing"
  ],
    
    "Degree": [
      "Computer Science",
      "Computer Science, Game Technology",
      "Computer Science, Computer Security",
      "Computer Science, Computer Networking",
      "Computer Science, Software Development",
      "Computer Science, Database Management",
      "Computer Science, Interactive Media",
      "Computer Science, Artificial Intelligence",
      "Computer Science, Cloud Computing",
    ]
  };

  function updateProgramOptions() {
    const type = document.getElementById("program_type").value;
    const progSelect = document.getElementById("program_name");
    progSelect.innerHTML = '<option value="">Program</option>';
    if (programOptions[type]) {
      programOptions[type].forEach(p => {
        const opt = document.createElement("option");
        opt.value = p;
        opt.text = p;
        progSelect.appendChild(opt);
      });
    }
  }

  window.onload = function () {
    updateAreaOptions();
    updateProgramOptions();
  };
</script>
<?php include("footer.php"); ?>
</body>
</html>
