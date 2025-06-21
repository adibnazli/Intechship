<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="AdminRegistration.css">
    <link rel="stylesheet" href="Header.css" />
    <title>AdminRegistration</title>
</head>
<body>
    <?php 
    include 'config/config.php'; 
    include 'Header.php';

            //delete
            if (isset($_POST['delete_btn'])) {
            $idDelete = $_POST['delete_id'];
            $conn->query("DELETE FROM person_in_charge WHERE PicID = $idDelete");
            echo "<script>alert('Record deleted'); window.location='AdminRegistration.php';</script>";
        }
        //edit
        $editID = "";
        $name = "";
        $email = "";
        $programme = "";
        if (isset($_POST['edit_btn'])) {
            $editID = $_POST['edit_id'];
            $res = $conn->query("SELECT * FROM person_in_charge WHERE PicID = $editID");
            if ($res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $name = $row['Pic_Name'];
                $email = $row['Email'];
                $programme = $row['Program_Desc'];
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_btn'])) {
        $name = $_POST['name'];
        $email = $_POST['Email'];
        $programme = $_POST['programme'];
        $password = $_POST['password'];
        $repassword = $_POST['repassword'];
        $editID = $_POST['edit_id'];

        if (!empty($editID)) {
        // editing(benarkan update password or not)
                if (!empty($password) || !empty($repassword)) {
                    if ($password !== $repassword) {
                        echo "<script>alert('Passwords do not match.');</script>";
                    } 
                    else {
                        //update dengan password(tak sentuh password)
                        $stmt = $conn->prepare("UPDATE person_in_charge SET Pic_Name=?, Email=?, Program_Desc=?, password=? WHERE PicID=?");
                        $stmt->bind_param("ssssi", $name, $email, $programme, $password, $editID);
                        $stmt->execute();
                        $stmt->close();
                        echo "<script>alert('Admin info updated (with new password)'); window.location='AdminRegistration.php';</script>";
                    }
                } 
                else {
                    //update without password
                    $stmt = $conn->prepare("UPDATE person_in_charge SET Pic_Name=?, Email=?, Program_Desc=? WHERE PicID=?");
                    $stmt->bind_param("sssi", $name, $email, $programme, $editID);
                    $stmt->execute();
                    $stmt->close();
                    echo "<script>alert('Admin info updated (no password change).'); window.location='AdminRegistration.php';</script>";
                }
            } 
            else {
            //insert record
            if ($password !== $repassword) {
                echo "<script>alert('Passwords do not match.');</script>";
            } 
            else {
                $stmt = $conn->prepare("INSERT INTO person_in_charge (Pic_Name, Email, Program_Desc, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $programme, $password);
                $stmt->execute();
                $stmt->close();
                echo "<script>alert('Admin info saved successfully'); window.location='AdminRegistration.php';</script>";
                }
        }
    }
    ?>
    
    <div class="container">
        <h4>Person-In-Charge Registration</h4>

        <hr>
        <form method="POST" class="form">
            <input type="hidden" name="edit_id" value="<?= htmlspecialchars($editID ?? '') ?>">
        <div class="header-section">
            <h2 class="generals">GENERALS</h2>
            <div class="button-group">
                <a href="AdminRegistration.php" class="buttonCan" style="text-decoration: none; display: inline-block;">Cancel</a>
                <button type="submit" class="buttonSave" name="save_btn" >Save Information</button>
            </div>
        </div>

        <div class="form-content">
            <div class="left-form">
                <label>Name</label>
                <input type="text" id="name" name="name" placeholder="E.g John Doe" value="<?= htmlspecialchars($name ?? '') ?>" required>

                <label for="email" >Email</label>
                <input type="text" id="Email" name="Email" placeholder="E.g abcd@university.edu" value="<?= htmlspecialchars($email ?? '') ?>" required>

            
                <label>Programme</label>
                <select id="programme" name="programme" required>
                <option value="">Select a Programme</option>
                <option value="DCS" <?= $programme == 'DCS' ? 'selected' : '' ?>>DCS</option>
                <option value="BITI" <?= $programme == 'BITI' ? 'selected' : '' ?>>BITI</option>
                <option value="BITS" <?= $programme == 'BITS' ? 'selected' : '' ?>>BITS</option>
                <option value="BITM" <?= $programme == 'BITM' ? 'selected' : '' ?>>BITM</option>
                </select>
            </div>

            <div class="right-form">
                <label>Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>

                <label>Re-enter Password</label>
                <input type="password" id="repassword" name="repassword" placeholder="Re-enter Password" required>
            </div>
            </div>
        </form>
        <hr>
        <h2>Person-In-Charge Info Section</h2>
        <table id="picTable">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Programme</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $result = $conn->query("SELECT PicID,Pic_Name, Email, Program_Desc FROM person_in_charge");
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['Pic_Name']}</td>
                        <td>{$row['Email']}</td>
                        <td>{$row['Program_Desc']}</td>
                        <td>
                <form method='POST' style='display:inline;'>
                    <input type='hidden' name='delete_id' value='{$row['PicID']}'>
                    <button type='submit' name='delete_btn'>Delete</button>
                </form>
                <form method='POST' style='display:inline;'>
                    <input type='hidden' name='edit_id' value='{$row['PicID']}'>
                    <button type='submit' name='edit_btn'>Edit</button>
                </form>
            </td>
                    </tr>";
                $no++;
            }
            ?>
            </tbody>
            </table>

        <script>
            // document.getElementById("form").addEventListener("submit",function(event)
            // {
            // event.preventDefault();

            // const name = document.getElementById("name").value;
            // const email = document.getElementById("email").value;
            // const programme = document.getElementById("programme").value;
            // const row = document.createElement('tr');
            // row.innerHTML = `
            //     <td>${name}</td>
            //     <td>${email}</td>
            //     <td>${programme}</td>
            //     <td><button class="delete-btn">Delete</button></td>
            // `;

            // row.querySelector(".delete-btn").addEventListener("click",function()
            // {
            //     row.remove();
            // });
            // document.querySelector("#picTable tbody").appendChild(row);
            // document.getElementById("form").reset();
            // });
            
        </script>
    </div>
</body>
</html>