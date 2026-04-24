<?php
include("connection.php");
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial scale=1.0">
    <title>Officer Complaint Portal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/adminstyles.css">
    <link rel="stylesheet" href="css/mystyle.css">
    <style>
        body {font-family: Arial; margin:0; padding:0; background:lavender;}
        .sidebar {width:200px; background:#b3c6ff; height:100vh; position:fixed; top:0; left:0; color:white;}
        .sidebar a {display:block; color:white; padding:15px; text-decoration:none;}
        .sidebar a:hover {background:#213cb0;}
        .headbar {
            height: 50px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 200px;
            background-color: #b3c6ff;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
            font-weight: bold;
        }
        .content {margin-left:200px; margin-top:50px; padding:20px;}
        table {width:100%; border-collapse: collapse;}
        th, td {border:1px solid #ccc; padding:8px; text-align:left;}
        .btn {padding:6px 10px; background:#668cff; color:white; border:none; cursor:pointer;}
        .btn:hover {background:#0056b3;}
        .back-btn {margin-top: 20px;}
    </style>
</head>
<body>

<?php
$officer = $_SESSION['officer'];
$name_q = mysqli_query($conn, "SELECT ename FROM user WHERE user_name = '$officer'");
$name_row = mysqli_fetch_assoc($name_q);
$ename = $name_row['ename'];
?>

<div class='sidebar'>
    <a href='?section=register'>Register Complaint</a>
    <a href='?section=status'>View Status</a>
    <a href='?section=resubmit'>Resubmit Complaint</a>
</div>

<div class='headbar'>
    Welcome, <?php echo htmlspecialchars($ename); ?>
</div>

<div class='content'>
<?php
$section = isset($_GET['section']) ? $_GET['section'] : 'register';

if ($section == 'register') {
?>
    <h2>Register New Complaint</h2>
    <form method="post" action="?section=register" enctype="multipart/form-data">
        <div class="form-group">
            <label>Complaint Type</label>
            <select name="type" class="form-control" style="width:300px;">
                <option value="Hardware">Hardware</option>
                <option value="Software">Software</option>
                <option value="Others">Others</option>
            </select>
        </div><br>
        Description:<br>
        <textarea name="comment" rows="5" cols="40" required></textarea><br><br>
        Select file to upload (jpg, jpeg, png, gif, pdf):
        <input type="file" name="fileToUpload" id="fileToUpload"><br><br>
        <input type="submit" name="submit" value="Submit" class="btn">
    </form>

    <div class="back-btn">
        <a href="officer.php" class="btn">← Back to Dashboard</a>
    </div>

<?php
    if (isset($_POST['submit'])) {
        $c_type = $_POST['type'];
        $c_comm = $_POST['comment'];

        $fwdResult = mysqli_query($conn, "SELECT user_name FROM user WHERE user_type = 'Senior Admin' AND admin_type = 'A0' LIMIT 1");
        $fwdRow = mysqli_fetch_assoc($fwdResult);
        $fwd = $fwdRow ? $fwdRow['user_name'] : null;

        if (!$fwd) {
            echo "<script>alert('No Senior Admin found. Cannot register complaint.');</script>";
            exit;
        }

        $officer = $_SESSION['officer'];
        $dept_query = "SELECT department FROM user WHERE user_name = '$officer' LIMIT 1";
        $dept_result = mysqli_query($conn, $dept_query);
        $dept_row = mysqli_fetch_assoc($dept_result);
        $c_department = $dept_row['department'];

        $check_dup = "SELECT * FROM complaint WHERE department='$c_department' AND comp_type='$c_type' AND complaint='$c_comm'";
        $result_dup = mysqli_query($conn, $check_dup);

        if (mysqli_num_rows($result_dup) > 0) {
            echo "<script>alert('Complaint already exists!');</script>";
        } else {
            $stat = 'pending';
            $insert = "INSERT INTO complaint (user_name, comp_type, department, complaint, fwd, cdate, stat) 
                       VALUES ('$officer', '$c_type', '$c_department', '$c_comm', '$fwd', now(), '$stat')";
            mysqli_query($conn, $insert);
            $compid = mysqli_insert_id($conn);

            $insertMov = "INSERT INTO movement (compid, mov_no, fwd, remarks, rem_date, action, current) 
                          VALUES ($compid, 1, '$fwd', 'Complaint Created', CURDATE(), 'Created', '$officer')";
            mysqli_query($conn, $insertMov);

            if (!empty($_FILES["fileToUpload"]["name"])) {
                $target_dir = "uploads/";
                $fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

                if (in_array($fileType, $allowed)) {
                    $new_filename = $compid . "." . $fileType;
                    $target_file = $target_dir . $new_filename;

                    $isImage = in_array($fileType, ['jpg', 'jpeg', 'png', 'gif']);
                    $fileSize = $_FILES["fileToUpload"]["size"];

                    if ((!$isImage || getimagesize($_FILES["fileToUpload"]["tmp_name"])) && $fileSize <= 500000) {
                        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                            mysqli_query($conn, "UPDATE complaint SET img_name = '$new_filename' WHERE compid = $compid");
                            echo "<script>alert('Complaint Registered Successfully'); window.location.href='ocomplaints.php?section=status';</script>";
                            exit;
                        } else {
                            mysqli_query($conn, "DELETE FROM complaint WHERE compid = $compid");
                            echo "<script>alert('Error uploading file.');</script>";
                        }
                    } else {
                        mysqli_query($conn, "DELETE FROM complaint WHERE compid = $compid");
                        echo "<script>alert('Invalid file or file too large (max 500KB).');</script>";
                    }
                } else {
                    $allowed_str = implode(", ", $allowed);
                    mysqli_query($conn, "DELETE FROM complaint WHERE compid = $compid");
                    echo "<script>alert('Invalid file type. Allowed: $allowed_str');</script>";
                }
            } else {
                echo "<script>alert('Complaint Registered Successfully'); window.location.href='ocomplaints.php?section=status';</script>";
                exit;
            }
        }
    }

} elseif ($section == 'status') {
    echo "<h2>Status of Complaints</h2>";
    $officer = $_SESSION['officer'];
    $sel = "SELECT * FROM complaint WHERE user_name = '$officer'";
    $compsel = mysqli_query($conn, $sel);

    if (mysqli_num_rows($compsel) > 0) {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Department</th>
                    <th>Complaint</th>
                    <th>View File</th>
                    <th>Forwarded To</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>";
        while ($row = mysqli_fetch_assoc($compsel)) {
            echo "<tr>
                    <td>{$row['compid']}</td>
                    <td>{$row['comp_type']}</td>
                    <td>{$row['department']}</td>
                    <td>{$row['complaint']}</td>
                    <td>";
            if (!empty($row['img_name'])) {
                echo "<a href='uploads/{$row['img_name']}' target='_blank'>View</a>";
            } else {
                echo "No File";
            }
            echo "</td>
                    <td>{$row['fwd']}</td>
                    <td>{$row['cdate']}</td>
                    <td>{$row['stat']}</td>
                    <td><a href='view_remarks.php?compid={$row['compid']}' target='_blank'>View Remarks</a></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No complaints found.</p>";
    }

    echo '<div class="back-btn"><a href="officer.php" class="btn">← Back to Dashboard</a></div>';

} elseif ($section == 'resubmit') {
    echo "<h2>Resubmit or Close Returned Complaints</h2>";
    $officer = $_SESSION['officer'];
    $returned = mysqli_query($conn, "SELECT * FROM complaint WHERE user_name = '$officer' AND stat = 'Returned to User'");
    echo '<div class="back-btn"><a href="officer.php" class="btn">← Back to Dashboard</a></div>';

    if (isset($_POST['close'])) {
        $cid = intval($_POST['close']);

        mysqli_query($conn, "UPDATE complaint SET stat='closed' WHERE compid=$cid");
        $movRes = mysqli_query($conn, "SELECT MAX(mov_no) AS maxno FROM movement WHERE compid=$cid");
        $mov_no = mysqli_fetch_assoc($movRes)['maxno'] + 1;
        $date = date("Y-m-d");

        mysqli_query($conn, "INSERT INTO movement (compid, mov_no, fwd, remarks, rem_date, action, current) 
                             VALUES ($cid, $mov_no, '$officer', 'User closed the complaint', '$date', 'Closed', '$officer')");

        echo "<script>alert('Complaint closed successfully.'); window.location='ocomplaints.php?section=status';</script>";
    }

    if (isset($_POST['resubmit'])) {
        $cid = intval($_POST['resubmit']);
        $desc = mysqli_real_escape_string($conn, $_POST["desc_$cid"]);

        if (empty($desc)) {
            echo "<script>alert('Please enter a description before resubmitting.'); window.history.back();</script>";
            exit;
        }

        $adminRes = mysqli_query($conn, "SELECT user_name FROM user WHERE user_type = 'Senior Admin' AND admin_type = 'A0' LIMIT 1");
        $adminRow = mysqli_fetch_assoc($adminRes);
        $seniorAdmin = $adminRow['user_name'];

        $movRes = mysqli_query($conn, "SELECT MAX(mov_no) AS maxno FROM movement WHERE compid = $cid");
        $mov_no = mysqli_fetch_assoc($movRes)['maxno'] + 1;
        $resubDate = date("Y-m-d");

        if (!empty($_FILES["resubmitImage_$cid"]["name"])) {
            $target_dir = "uploads/";
            $fileType = strtolower(pathinfo($_FILES["resubmitImage_$cid"]["name"], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
            $fileSize = $_FILES["resubmitImage_$cid"]["size"];
            $isImage = in_array($fileType, ['jpg', 'jpeg', 'png', 'gif']);

            if (in_array($fileType, $allowed)) {
                if ((!$isImage || getimagesize($_FILES["resubmitImage_$cid"]["tmp_name"])) && $fileSize <= 500000) {
                    $new_filename = $cid . "." . $fileType;
                    $target_file = $target_dir . $new_filename;
                    move_uploaded_file($_FILES["resubmitImage_$cid"]["tmp_name"], $target_file);
                    mysqli_query($conn, "UPDATE complaint SET img_name = '$new_filename' WHERE compid = $cid");
                } else {
                    echo "<script>alert('Invalid file or size exceeds 500KB');</script>";
                    exit;
                }
            } else {
                echo "<script>alert('Invalid file type. Allowed: jpg, jpeg, png, gif, pdf');</script>";
                exit;
            }
        }

        mysqli_query($conn, "UPDATE complaint SET complaint = '$desc', stat = 'pending', fwd = '$seniorAdmin' WHERE compid = $cid");
        mysqli_query($conn, "INSERT INTO movement (compid, mov_no, fwd, remarks, rem_date, action, current) 
                             VALUES ($cid, $mov_no, '$seniorAdmin', 'Resubmitted', '$resubDate', 'Resubmitted', '$officer')");

        echo "<script>alert('Complaint Resubmitted Successfully'); window.location.href='ocomplaints.php?section=status';</script>";
    }

    if (mysqli_num_rows($returned) > 0) {
        echo "<form method='post' enctype='multipart/form-data'>
                <table class='table'>
                <tr><th>ID</th><th>Type</th><th>Original Complaint</th><th>New Description</th><th>Upload New File</th><th>Action</th></tr>";
        while ($row = mysqli_fetch_assoc($returned)) {
            echo "<tr>
                    <td>{$row['compid']}</td>
                    <td>{$row['comp_type']}</td>
                    <td>{$row['complaint']}</td>
                    <td><textarea name='desc_{$row['compid']}' maxlength='2000'></textarea></td>
                    <td><input type='file' name='resubmitImage_{$row['compid']}'></td>
                    <td>
                        <button type='submit' name='resubmit' value='{$row['compid']}' class='btn'>Resubmit</button>
                        <button type='submit' name='close' value='{$row['compid']}' class='btn' onclick='return confirm(\"Are you sure you want to close this complaint?\");'>Close</button>
                    </td>
                </tr>";
        }
        echo "</table></form>";
    } else {
        echo "<p>No complaints marked 'Returned to User'.</p>";
    }
}
?>
</div>
</body>
</html>