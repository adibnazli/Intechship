<html>
<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Martel+Sans&display=swap" rel="stylesheet">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Document</title>
<link rel="stylesheet" type="text/css" href="EmployerHeader.css">
<style>
body {
      margin: 0;
      padding: 0;
    }

    .header-container {
      display: flex;
    }

    .imageheader {
      background-color: #FFD900;
      padding: 10px 20px;
      width: 280px;
      height: 130px;
      display: flex;
      align-items: center;
    }

    .imageheader img {
      height: 120px;
      margin-right: 20px;
    }

    .header {
        background-color: #1F1F1F;
        padding: 10px 20px;
        width: 100%;
        height: 60px;
    }

    </style>
</head>
<body>    
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="header-container">
    <div class="imageheader">
      <a href="PostListing.php"><img src="image/FTMK-Logo.png" alt="FTMK Logo"></a>
    </div>
    <div class="header">
            <nav class="nav-menu">
                <ul>
                    <li><a href="PostListing.php" class="<?php echo ($current_page == 'PostListing.php') ? 'active' : ''; ?>">Post Listing</a></li>
                    <li><a href="JobPosting.php" class="<?php echo ($current_page == 'JobPosting.php') ? 'active' : ''; ?>">Job Posting</a></li>
                    <li><a href="ApplicationManagement.php" class="<?php echo ($current_page == 'ApplicationManagement.php') ? 'active' : ''; ?>">Application Management</a></li>

                </ul>
            </nav>
                <button class="logout-btn">Log Out</button> 
    </div>
  </div>
<script>
</script>
</body>
</html>
