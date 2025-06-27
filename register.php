<?php
session_start();

$Stud_Name = $_SESSION['Stud_Name'] ?? '';
$Email = $_SESSION['Email'] ?? '';
$Stud_MatricNo = $_SESSION['Stud_MatricNo'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign up(Student)</title>
  <link rel="stylesheet" href="LoginStyle.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
  <div class="main-container">
    <div class="left-content">
      <div class="welcome"><h1>Welcome</h1></div>
      <div class="Register-Login">
        <div class="wrapper">
          <form action="member.php" method="post">
            <h1>Register</h1>
            <div class="input-box">
              <input type="text" name="Fname" value="<?= htmlspecialchars($Stud_Name) ?>" placeholder="Full Name" readonly />
              <i class="bx bxs-user"></i>
            </div>
            <div class="input-box">
              <input type="text" name="matricno" value="<?= htmlspecialchars($Stud_MatricNo)?>" placeholder="Matric Number" autocomplete="off" readonly />
              <i class="bx bxs-graduation"></i>
            </div>
            <div class="input-box">
              <input type="email" name="Email" value="<?= htmlspecialchars($Email) ?>" placeholder="Email" autocomplete="off" readonly />
              <i class="bx bxs-envelope"></i>
            </div>
              <div class="input-box">
                <input type="password" name="password" id="password" placeholder="Password" required />
                <i class="bx bxs-lock-alt"></i>
              </div>
              <div class="input-box">
                <input type="password" name="repassword" id="repassword" placeholder="Re-enter Password" required />
                <i class="bx bxs-lock-alt"></i>
              </div>
            <button type="submit" class="btn">Sign up</button>
          </form>
          <p class="center">
            <br /><b>Already have account? <a href="login.html" style="color: bisque">Sign-in now..</a></b>
          </p>
        </div>
      </div>
    </div>
    <div class="right-content"><img src="logo.png" alt="Logo" /></div>
  </div>
</body>
</html>
