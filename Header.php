<?php
session_start();
include("config/config.php");

if (isset($_SESSION['academicID'])) {
    $unitID = $_SESSION['academicID'];

    $stmt = $conn->prepare("SELECT Name FROM academic_unit WHERE academicID = ?");
    $stmt->bind_param("i", $unitID); 
    $stmt->execute();
    $stmt->bind_result($unitName);
    
    if ($stmt->fetch()) {
        $_SESSION['Name'] = $unitName;
    }
    $stmt->close();
  }
?>

<html>
<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Martel+Sans&display=swap" rel="stylesheet">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Document</title>
<link rel="stylesheet" type="text/css" href="Header.css">
<style>
  .logout-btn {
    text-decoration: none;
  }
</style>
</head>
<body>    
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="header-container">
    <div class="imageheader">
      <a href=""><img src="FTMK-Logo.png" alt="FTMK Logo"></a>
    </div>
    <div class="header">
            <nav class="nav-menu">
                <ul>
                    <li><a href="AdminRegistration.php" class="<?php echo ($current_page == 'AdminRegistration.php') ? 'active' : ''; ?>">Registration</a></li>
                </ul>
            </nav>
                <div style="display: flex; justify-content: space-between; align-items: center; height: 100%;">
              <div style="color: white; font-family: 'Roboto', sans-serif; font-size: 16px; margin-right: 20px;">
                <?php
                  if (isset($_SESSION['Name'])) {
                      echo htmlspecialchars($_SESSION['Name']);
                  } else {
                      echo 'Academic Unit Name';
                  }
                ?>
              </div>
              <a href="logout.php" class="logout-btn" onclick="return confirmLogout()">Log Out</a>
            </div>
    </div>
  </div>
<script>
  function confirmLogout() {
    return confirm("Are you sure you want to log out?");
  }
</script>
</body>
</html>