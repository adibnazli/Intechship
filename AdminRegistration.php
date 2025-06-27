<?php
session_start();
include('config/connect.php');
include 'Header.php';

/*Delete*/
if (isset($_POST['delete_btn'])) {
    $idDelete = $_POST['delete_id'];
    $conn->query("DELETE FROM person_in_charge WHERE PicID = $idDelete");
    echo "<script>alert('Record deleted'); window.location='AdminRegistration.php';</script>";
    exit;
}





/*Fetch for Edit*/
$editID = $name = $email = $programme = '';
if (isset($_POST['edit_btn'])) {
    $editID = $_POST['edit_id'];
    $res = $conn->query("SELECT * FROM person_in_charge WHERE PicID = $editID");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $name      = $row['Pic_Name'];
        $email     = $row['Email'];
        $programme = $row['Program_Desc'];
    }
}

/*Save or update*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_btn'])) {

    $name      = $_POST['name']      ?? '';
    $email     = $_POST['Email']     ?? '';
    $programme = $_POST['programme'] ?? '';
    $password  = $_POST['password']  ?? '';
    $repass    = $_POST['repassword']?? '';
    $editID    = $_POST['edit_id']   ?? '';

    $academicID = $_SESSION['academicID'] ?? null;   // siapa daftar PIC

    /*validate password jika nak ubah atau rekod baru*/
    if (($password || $repass) && $password !== $repass) {
        echo "<script>alert('Passwords do not match');</script>";
    } else {
        if ($editID) {           
            if ($password) {     // ubah password
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("
                    UPDATE person_in_charge
                    SET Pic_Name=?, Email=?, Program_Desc=?, password=?, academicID=?
                    WHERE PicID=?");
                $stmt->bind_param("ssssis", $name, $email, $programme, $hashed, $academicID, $editID);
            } 
            else {             //tanpa ubah password
                $stmt = $conn->prepare("
                    UPDATE person_in_charge
                    SET Pic_Name=?, Email=?, Program_Desc=?, academicID=?
                    WHERE PicID=?");
                $stmt->bind_param("sssii", $name, $email, $programme, $academicID, $editID);
            }
            $stmt->execute();
            echo "<script>alert('Admin info updated'); window.location='AdminRegistration.php';</script>";
        } 
        else {                 /*INSERT*/
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("
                INSERT INTO person_in_charge (Pic_Name, Email, Program_Desc, password, academicID)
                VALUES (?,?,?,?,?)");
            $stmt->bind_param("ssssi", $name, $email, $programme, $hashed, $academicID);
            $stmt->execute();
            echo "<script>alert('Admin info saved successfully'); window.location='AdminRegistration.php';</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="AdminRegistration.css">
    <link rel="stylesheet" href="Header.css">
    <title>AdminRegistration</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .buttonSave {
            font-size:16px;
        }
    </style>
</head>
<body>
<div class="container">
    <h4>Person-In-Charge Registration</h4>
    <hr>
    <form method="POST" class="form">
        <input type="hidden" name="edit_id" value="<?= htmlspecialchars($editID) ?>">
        <div class="header-section">
            <h2 class="generals">GENERALS</h2>
            <div class="button-group">
                <a href="AdminRegistration.php" class="buttonCan" style="text-decoration: none; display: inline-block;">Cancel</a>
                <button type="submit" class="buttonSave" name="save_btn">Save</button>
            </div>
        </div>

        <div class="form-content">
            <div class="left-form">
                <label>Name</label>
                <input type="text" name="name" placeholder="E.g John Doe"
                       value="<?= htmlspecialchars($name) ?>" required>

                <label>Email</label>
                <input type="email" name="Email" placeholder="abcd@university.edu"
                       value="<?= htmlspecialchars($email) ?>" required>

                <label>Programme</label>
                <select name="programme" required>
                    <option value="">Select a Programme</option>
                    <option value="Diploma" <?= $programme==='Diploma'?'selected':'' ?>>Diploma</option>
                    <option value="Degree"  <?= $programme==='Degree'?'selected':'' ?>>Degree</option>
                </select>
            </div>

            <div class="right-form">
                <label>Password</label>
                <input type="password" name="password" >

                <label>Re-enter Password</label>
                <input type="password" name="repassword" >
            </div>
        </div>
    </form>

    <hr>
    <h2>Person-In-Charge Info Section</h2>
    <table id="picTable">
        <thead>
            <tr><th>No</th><th>Name</th><th>Email</th><th>Programme</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php
        $res = $conn->query("SELECT PicID, Pic_Name, Email, Program_Desc FROM person_in_charge");
        $n=1;
        while($r=$res->fetch_assoc()){
            echo "<tr>
                    <td>{$n}</td>
                    <td>{$r['Pic_Name']}</td>
                    <td>{$r['Email']}</td>
                    <td>{$r['Program_Desc']}</td>
                    <td>
                        <form method='POST' style='display:inline'>
                            <input type='hidden' name='delete_id' value='{$r['PicID']}'>
                            <button type='submit' name='delete_btn'>Delete</button>
                        </form>
                        <form method='POST' style='display:inline'>
                            <input type='hidden' name='edit_id' value='{$r['PicID']}'>
                            <button type='submit' name='edit_btn'>Edit</button>
                        </form>
                    </td>
                  </tr>";
            $n++;
        }
        ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
