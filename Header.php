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
                <button class="logout-btn">Log Out</button> 
    </div>
  </div>
<script>
</script>
</body>
</html>