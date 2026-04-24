<?php
include("connection.php");
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial scale=1.0">
    <title>Complaint Portal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/adminstyles.css">
    <link rel="stylesheet" href="css/mystyle.css">
    <style>
        body {
            font-family: Arial;
            margin:0;
            padding:0;
            background:lavender;
        }
        .headbar {
            background-color: #b3c6ff;
            color: white;
            padding: 15px 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            z-index: 1000;
        }
        .headbar h1 {
            margin: 0;
            font-size: 20px;
        }
        .sidebar {
            width: 200px;
            background: #b3c6ff;
            height: calc(100vh - 60px);
            position: fixed;
            top: 60px;
            left: 0;
            color: white;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #213cb0;
        }
        .content {
            margin-left: 200px;
            margin-top: 60px;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .btn {
            padding: 6px 10px;
            background: #668cff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #0056b3;
        }
        .back-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="headbar">
    <h1>User Complaint Portal</h1>
</div>

<div class='sidebar'>
    <a href='?section=register'>Register Complaint</a>
    <a href='?section=status'>View Status</a>
    <a href='?section=resubmit'>Resubmit Complaint</a>
</div>
<?php
$user = $_SESSION['user'];
$name_q = mysqli_query($conn, "SELECT ename FROM user WHERE user_name = '$user'");
$name_row = mysqli_fetch_assoc($name_q);
$ename = $name_row['ename'];
?>
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
    <textarea name="comment" rows="5" cols="40"></textarea><br><br>
    Select file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload"><br><br>
    <input type="submit" name="submit" value="Submit" class="btn">
</form>
<div class="back-btn">
    <a href="user.php" class="btn">← Back to Dashboard</a>
</div>
<?php
if (isset($_POST['submit'])) {
    $user = $_SESSION['user'];
    $type = $_POST['type'];
    $desc = $_POST['comment'];

    $dept_q = mysqli_query($conn, "SELECT department FROM user WHERE user_name = '$user'");
    $dept_row = mysqli_fetch_assoc($dept_q);
    $dept = $dept_row['department'];

    $officer_q = mysqli_query($conn, "SELECT user_name FROM user WHERE department = '$dept' AND user_type = 'officer' LIMIT 1");
    $officer_row = mysqli_fetch_assoc($officer_q);
    $fwd = $officer_row['user_name'];

    $dup_check = mysqli_query($conn, "SELECT * FROM complaint WHERE department='$dept' AND comp_type='$type' AND complaint='$desc'");
    if (mysqli_num_rows($dup_check) > 0) {
        echo "<script>alert('Complaint already exists!');</script>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO complaint(user_name, comp_type, department, complaint, fwd, cdate, stat) VALUES('$user', '$type', '$dept', '$desc', '$fwd', now(), 'pending')");
        $compid = mysqli_insert_id($conn);
        mysqli_query($conn, "INSERT INTO movement (compid, mov_no, fwd, remarks, rem_date, action, current) VALUES ($compid, 1, '$fwd', 'Complaint Created', NOW(), 'Created', '$user')");

        $target_dir = "uploads/";
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));

        if ($_FILES["fileToUpload"]["name"] != '') {
            if (in_array($fileType, $allowed)) {
                $new_filename = $compid . "." . $fileType;
                $target_file = $target_dir . $new_filename;

                $fileSize = $_FILES["fileToUpload"]["size"];
                $isPDF = ($fileType == 'pdf');

                if (($isPDF || getimagesize($_FILES["fileToUpload"]["tmp_name"])) && $fileSize <= 500000) {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        mysqli_query($conn, "UPDATE complaint SET img_name='$new_filename' WHERE compid=$compid");
                    } else {
                        echo "<script>alert('Error uploading file.');</script>";
                    }
                } else {
                    echo "<script>alert('Invalid file or size too large.');</script>";
                    mysqli_query($conn, "DELETE FROM complaint WHERE compid=$compid");
                    exit;
                }
            } else {
                $allowed_str = implode(", ", $allowed);
                echo "<script>alert('Invalid file type. Allowed: $allowed_str');</script>";
                mysqli_query($conn, "DELETE FROM complaint WHERE compid=$compid");
                exit;
            }
        }

        echo "<script>alert('Complaint Registered Successfully'); window.location='ucomplaints.php?section=status';</script>";
    }
}
} elseif ($section == 'status') {
    echo "<h2>Status of Complaints</h2>";
    $user = $_SESSION['user'];
    $sel = mysqli_query($conn, "SELECT * FROM complaint WHERE user_name = '$user'");
    if (mysqli_num_rows($sel) > 0) {
        echo "<table>
                <tr><th>ID</th><th>Type</th><th>Department</th><th>Complaint</th><th>File</th><th>Currently With</th><th>Date</th><th>Status</th><th>Remarks</th></tr>";
        while ($row = mysqli_fetch_assoc($sel)) {
            echo "<tr>
                    <td>{$row['compid']}</td>
                    <td>{$row['comp_type']}</td>
                    <td>{$row['department']}</td>
                    <td>{$row['complaint']}</td>
                    <td>";
            echo $row['img_name'] ? "<a href='uploads/{$row['img_name']}' target='_blank'>View File</a>" : "No File";
            echo "</td>
                    <td>{$row['fwd']}</td>
                    <td>{$row['cdate']}</td>
                    <td>{$row['stat']}</td>
                    <td><a href='viewremarks.php?compid={$row['compid']}' target='_blank'>View Remarks</a></td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No complaints found.</p>";
    }
    echo '<div class="back-btn"><a href="user.php" class="btn">← Back to Dashboard</a></div>';
} elseif ($section == 'resubmit') {
    echo "<h2>Resubmit/Close Complaints</h2>";
    $user = $_SESSION['user'];
    $returned = mysqli_query($conn, "SELECT * FROM complaint WHERE user_name = '$user' AND stat = 'Returned to User'");
    echo '<div class="back-btn"><a href="user.php" class="btn">← Back to Dashboard</a></div>';

    if (isset($_POST['close'])) {
        $cid = intval($_POST['close']);
        mysqli_query($conn, "UPDATE complaint SET stat='Closed' WHERE compid=$cid");
        $movRes = mysqli_query($conn, "SELECT MAX(mov_no) AS maxno FROM movement WHERE compid=$cid");
        $mov_no = mysqli_fetch_assoc($movRes)['maxno'] + 1;
        $date = date("Y-m-d");
        mysqli_query($conn, "INSERT INTO movement (compid, mov_no, fwd, remarks, rem_date, action, current) 
                             VALUES ($cid, $mov_no, '$user', 'User closed the complaint', '$date', 'Closed', '$user')");
        echo "<script>alert('Complaint closed successfully.'); window.location='ucomplaints.php?section=status';</script>";
    }

    if (isset($_POST['resubmit'])) {
        $cid = intval($_POST['resubmit']);
        $desc = mysqli_real_escape_string($conn, $_POST["desc_$cid"]);

        if (empty($desc)) {
            echo "<script>alert('Please enter a description before resubmitting.'); window.history.back();</script>";
            exit;
        }

        $fwd_q = mysqli_query($conn, "SELECT fwd FROM movement WHERE compid=$cid AND mov_no=1");
        $fwd_row = mysqli_fetch_assoc($fwd_q);
        $originalFwd = $fwd_row['fwd'];

        $movRes = mysqli_query($conn, "SELECT MAX(mov_no) AS maxno FROM movement WHERE compid=$cid");
        $mov_no = mysqli_fetch_assoc($movRes)['maxno'] + 1;
        $date = date("Y-m-d");

        $target_dir = "uploads/";
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $fileType = strtolower(pathinfo($_FILES["newImage_$cid"]["name"], PATHINFO_EXTENSION));

        if ($_FILES["newImage_$cid"]["name"] != '') {
            if (in_array($fileType, $allowed)) {
                $new_filename = $cid . "." . $fileType;
                $target_file = $target_dir . $new_filename;
                $fileSize = $_FILES["newImage_$cid"]["size"];
                $isPDF = ($fileType == 'pdf');

                if (($isPDF || getimagesize($_FILES["newImage_$cid"]["tmp_name"])) && $fileSize <= 500000) {
                    move_uploaded_file($_FILES["newImage_$cid"]["tmp_name"], $target_file);
                    mysqli_query($conn, "UPDATE complaint SET img_name='$new_filename' WHERE compid=$cid");
                } else {
                    echo "<script>alert('Invalid file or size too large.');</script>";
                    exit;
                }
            } else {
                $allowed_str = implode(", ", $allowed);
                echo "<script>alert('Invalid file type. Allowed: $allowed_str');</script>";
                exit;
            }
        }

        mysqli_query($conn, "UPDATE complaint SET complaint='$desc', stat='pending', fwd='$originalFwd' WHERE compid=$cid");
        mysqli_query($conn, "INSERT INTO movement (compid, mov_no, fwd, remarks, rem_date, action, current) VALUES ($cid, $mov_no, '$originalFwd', 'Resubmitted', '$date', 'Resubmitted', '$user')");
        echo "<script>alert('Complaint Resubmitted Successfully'); window.location='ucomplaints.php?section=status';</script>";
    }

    if (mysqli_num_rows($returned) > 0) {
        echo "<form method='post' enctype='multipart/form-data'>
                <table class='table'>
                <tr><th>ID</th><th>Type</th><th>Complaint</th><th>New Description</th><th>Upload New File</th><th>Action</th></tr>";
        while ($row = mysqli_fetch_assoc($returned)) {
            echo "<tr>
                    <td>{$row['compid']}</td>
                    <td>{$row['comp_type']}</td>
                    <td>{$row['complaint']}</td>
                    <td><textarea name='desc_{$row['compid']}' maxlength='2000'></textarea></td>
                    <td><input type='file' name='newImage_{$row['compid']}'></td>
                    <td>
                        <button type='submit' name='resubmit' value='{$row['compid']}' class='btn'>Resubmit</button>
                        <button type='submit' name='close' value='{$row['compid']}' class='btn' onclick=\"return confirm('Are you sure you want to close this complaint?');\">Close</button>
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