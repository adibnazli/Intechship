<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    footer a {
      color: #4fc3f7;
      text-decoration: none;
    }

    footer a:visited {
      color: #4fc3f7;
    }

    footer a:hover {
      text-decoration: none;
    }
  </style>
</head>
<body>
  <footer style="
  background-color:#1F1F1F;
  padding: 30px 0;
  text-align: center;
  font-family: 'Roboto', sans-serif;
  font-size: 14px;
  color: #fafafa;
  margin-top: auto;
">
    <div>
      <strong>Faculty of Information and Communication Technology (FTMK)</strong><br>
      Universiti Teknikal Malaysia Melaka (UTeM)<br>
      Hang Tuah Jaya, 76100 Durian Tunggal, Melaka, Malaysia<br>
      Phone: +606-270 1000 | Website: 
      <a href="https://ftmk.utem.edu.my/" target="_blank">ftmk.utem.edu.my</a>
    </div>
    <div style="margin-top: 10px; color: #adadad;">
      &copy; <?= date("Y") ?> FTMK UTeM Internship Portal. All rights reserved.
    </div>
  </footer>
</body>
</html>
