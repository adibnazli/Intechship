<?php
// Dummy data – replace with actual DB fetch later
$students = [
  ['name' => 'John Doe', 'matric' => 'B032410763', 'status' => 'Pending', 'date' => '26/04/2024'],
  ['name' => 'Jane Doe', 'matric' => 'B032110278', 'status' => 'Verified', 'date' => '17/04/2024'],
  ['name' => 'Alan Smith', 'matric' => 'D032310171', 'status' => 'Verified', 'date' => '13/04/2024'],
  ['name' => 'David Martinez', 'matric' => 'D032310012', 'status' => 'Failed', 'date' => '08/04/2024'],
  ['name' => 'John Hamm', 'matric' => 'D032310782', 'status' => 'Verified', 'date' => '03/04/2024'],
];

$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

if (!empty($search) || !empty($statusFilter)) {
    $students = array_filter($students, function ($s) use ($search, $statusFilter) {
        $matchesSearch = empty($search) || stripos($s['matric'], $search) !== false;
        $matchesStatus = empty($statusFilter) || strtolower($s['status']) === strtolower($statusFilter);
        return $matchesSearch && $matchesStatus;
    });
}

?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    include("config/config.php");
}
include 'AdminHeader.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Validation (Admin)</title>
  <link rel="stylesheet" href="StudentValidation.css">
  <style>
    .badge {
      padding: 5px 12px;
      border-radius: 12px;
      font-size: 0.9em;
      color: white;
      font-weight: bold;
    }

    .badge.pending {
      background-color: #f0ad4e;
    }

    .badge.verified {
      background-color: #5cb85c;
    }

    .badge.failed {
      background-color: #d9534f;
    }

    .search-bar {
      margin: 20px 0;
      display: flex;
      gap: 10px;
    }

    .search-bar input[type="text"] {
      flex: 1;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .btn-find {
      background-color: #FFD900;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-find:hover {
      background-color: #e6c200;
    }

    .btn-view {
      background-color: #337ab7;
      color: white;
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }

    .tabs {
      display: flex;
      gap: 10px;
      margin: 20px 0;
    }

    .tab {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      background-color: #eee;
      cursor: pointer;
      font-weight: bold;
    }

    .tab.active {
      background-color: #FFD900;
    }

    .students-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
    }

    .students-table th, .students-table td {
      padding: 15px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    .students-table thead {
      background-color: #FFD900;
    }

    .students-table th:first-child, .students-table td:first-child {
      width: 30px;
    }
  </style>
</head>
<body>
  <div class="container">
<div class="header-section">
  <h2 class="generals" style="margin: 0 auto;">Dashboard</h2>
</div>
    <hr>

    <form method="GET" class="search-bar">
  <input type="text" name="search" placeholder="Search Matric Number" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
  <button type="submit" class="btn-find">Find</button>
</form>


    <?php
$statusFilter = $_GET['status'] ?? '';
?>
<div class="tabs">
  <a href="StudentValidation.php" class="tab <?= $statusFilter == '' ? 'active' : '' ?>">All Students</a>
  <a href="StudentValidation.php?status=Pending" class="tab <?= $statusFilter == 'Pending' ? 'active' : '' ?>">Pending</a>
  <a href="StudentValidation.php?status=Verified" class="tab <?= $statusFilter == 'Verified' ? 'active' : '' ?>">Verified</a>
</div>


    <table class="students-table">
      <thead>
        <tr>
          <th></th>
          <th>Student Name</th>
          <th>Matric Number</th>
          <th>Status</th>
          <th>Apply Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $s): ?>
          <tr>
            <td><input type="checkbox"></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['matric']) ?></td>
            <td><span class="badge <?= strtolower($s['status']) ?>"><?= $s['status'] ?></span></td>
            <td><?= htmlspecialchars($s['date']) ?></td>
            <td><button class="btn-view">View</button></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
