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
      <div class="welcome"><h1>Verify</h1></div>
      <div class="Register-Login">
        <div class="wrapper">
          <form action="checkStudent.php" method="post">
            <h1>Student</h1>
            <div class="input-box">
              <input type="text" id="Email" name="Email" placeholder="Email" autocomplete="off" required />
              <i class="bx bxs-user"></i>
            </div>
            <div class="input-box">
              <input type="text" name="idno" id="idno" placeholder="Identity Number" autocomplete="off" required />
              <i class='bx bxs-id-card'></i>
            </div>
            <button type="submit" class="btn">Check</button>
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