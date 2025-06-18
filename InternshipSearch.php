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
    }

    .job-card {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      padding: 20px;
      width: calc(50% - 10px);
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
  </style>
</head>
<body>

<?php include("UserHeader.php"); ?>

<div class="search-container">

  <!-- Search Bar Centered -->
  <form method="GET">
    <div style="text-align: center; margin-bottom: 20px;">
      <input type="text" name="keyword" placeholder="🔍 Search Internship"
             value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>"
             style="width: 60%; max-width: 600px;">
    </div>

    <!-- Filter Options -->
    <div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 20px; margin-bottom: 20px;">

      <!-- Program Type -->
      <select name="program_type" id="program_type" onchange="updateProgramOptions()">
        <option value="">Program Type</option>
        <option value="Diploma" <?= (isset($_GET['program_type']) && $_GET['program_type'] == 'Diploma') ? 'selected' : '' ?>>Diploma</option>
        <option value="Degree" <?= (isset($_GET['program_type']) && $_GET['program_type'] == 'Degree') ? 'selected' : '' ?>>Degree</option>
      </select>

      <!-- Program -->
      <select name="program_name" id="program_name">
        <option value="">Program</option>
        <?php if (isset($_GET['program_name'])): ?>
          <option value="<?= htmlspecialchars($_GET['program_name']) ?>" selected><?= htmlspecialchars($_GET['program_name']) ?></option>
        <?php endif; ?>
      </select>

      <!-- Location (State) -->
      <select name="location" id="location" onchange="updateAreaOptions()">
        <option value="">Location</option>
        <?php
        $states = ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Penang', 'Perak', 'Perlis', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu', 'Kuala Lumpur', 'Labuan', 'Putrajaya'];
        foreach ($states as $state) {
          $selected = (isset($_GET['location']) && $_GET['location'] == $state) ? 'selected' : '';
          echo "<option value=\"$state\" $selected>$state</option>";
        }
        ?>
      </select>

      <!-- Area -->
      <select name="area" id="area">
        <option value="">Area</option>
        <?php if (isset($_GET['area'])): ?>
          <option value="<?= htmlspecialchars($_GET['area']) ?>" selected><?= htmlspecialchars($_GET['area']) ?></option>
        <?php endif; ?>
      </select>

      <!-- Industry -->
      <select name="industry">
        <option value="">Industry</option>
        <?php
        $industries = ['Information Technology', 'Software Engineering', 'Administrative', 'Maintenance'];
        foreach ($industries as $ind) {
          $selected = (isset($_GET['industry']) && $_GET['industry'] == $ind) ? 'selected' : '';
          echo "<option value=\"$ind\" $selected>$ind</option>";
        }
        ?>
      </select>
    </div>

    <!-- Search Button -->
    <div style="text-align: center;">
      <button type="submit" class="apply-btn">Search</button>
    </div>
  </form>

  <!-- Job Listings -->
  <div class="job-cards">
    <?php
    $jobs = [
      [
        "title" => "IT Support Intern",
        "company" => "TechSys Solutions",
        "location" => "Johor",
        "area" => "Skudai",
        "industry" => "Information Technology",
        "desc" => "Provide technical support to users (hardware & software).",
        "duration" => "🕒 Duration: 3 - 6 months",
        "program" => "Diploma in Computer Science"
      ],
      [
        "title" => "Backend Developer Intern",
        "company" => "ByteWorks Inc.",
        "location" => "Selangor",
        "area" => "Petaling Jaya",
        "industry" => "Software Engineering",
        "desc" => "Work on API and database systems.",
        "duration" => "🕒 Duration: 6 months",
        "program" => "Bachelor in Computer Science (Database Management)"
      ],
      [
        "title" => "Admin Assistant Intern",
        "company" => "OfficePro Services",
        "location" => "Selangor",
        "area" => "Shah Alam",
        "industry" => "Administrative",
        "desc" => "Assist in clerical and office work.",
        "duration" => "🕒 Duration: 3 months",
        "program" => "Diploma in Computer Science"
      ]
    ];

    $keyword = isset($_GET['keyword']) ? strtolower(trim($_GET['keyword'])) : '';
    $locFilter = $_GET['location'] ?? '';
    $areaFilter = $_GET['area'] ?? '';
    $industryFilter = $_GET['industry'] ?? '';
    $programFilter = $_GET['program_name'] ?? '';

    foreach ($jobs as $job) {
      $combined = strtolower($job['title'] . ' ' . $job['company'] . ' ' . $job['desc']);
      $match = true;

      if ($keyword && strpos($combined, $keyword) === false) $match = false;
      if ($locFilter && $job['location'] !== $locFilter) $match = false;
      if ($areaFilter && $job['area'] !== $areaFilter) $match = false;
      if ($industryFilter && $job['industry'] !== $industryFilter) $match = false;
      if ($programFilter && $job['program'] !== $programFilter) $match = false;

      if ($match) {
        echo '<div class="job-card">';
        echo "<h3>{$job['title']}</h3>";
        echo "<p><strong>{$job['company']}</strong></p>";
        echo "<p class='location'>📍 {$job['area']}, {$job['location']}</p>";
        echo "<p class='duration'>{$job['duration']}</p>";
        echo "<p>{$job['desc']}</p>";
        echo '<button class="apply-btn">Apply</button>';
        echo '</div>';
      }
    }
    ?>
  </div>
</div>

<!-- JavaScript Dropdown Updates -->
<script>
  const areaOptions = {
    "Johor": ["Johor Bahru", "Batu Pahat", "Kluang", "Muar", "Segamat", "Skudai"],
    "Kedah": ["Alor Setar", "Sungai Petani", "Kulim", "Langkawi"],
    "Kelantan": ["Kota Bharu", "Pasir Mas", "Tumpat", "Gua Musang"],
    "Melaka": ["Melaka Tengah", "Alor Gajah", "Jasin"],
    "Negeri Sembilan": ["Seremban", "Port Dickson", "Nilai"],
    "Pahang": ["Kuantan", "Temerloh", "Bentong", "Cameron Highlands"],
    "Penang": ["George Town", "Butterworth", "Bayan Lepas", "Seberang Perai"],
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
    if (state && areaOptions[state]) {
      areaOptions[state].forEach(area => {
        const option = document.createElement("option");
        option.value = area;
        option.text = area;
        areaSelect.appendChild(option);
      });
    }
  }

  const programOptions = {
    "Diploma": ["Diploma in Computer Science"],
    "Degree": [
      "Bachelor in Computer Science (Database Management)",
      "Bachelor in Computer Science (Software Engineering)",
      "Bachelor in Computer Science (AI & Data Science)",
      "Bachelor in Computer Science (Cybersecurity)"
    ]
  };

  function updateProgramOptions() {
    const type = document.getElementById("program_type").value;
    const programSelect = document.getElementById("program_name");
    programSelect.innerHTML = '<option value="">Program</option>';
    if (type && programOptions[type]) {
      programOptions[type].forEach(program => {
        const option = document.createElement("option");
        option.value = program;
        option.text = program;
        programSelect.appendChild(option);
      });
    }
  }

  window.onload = function () {
    updateAreaOptions();
    updateProgramOptions();
  };
</script>

</body>
</html>
